<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sosiometri extends CI_Controller
{
	const TEMPIMGLOC = 'tempimg.png';

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		$this->load->model('GetModel');

		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	public function index()
	{
		$kelas = $this->Main_model->join(
			'kelas',
			'*,kelas.id as id',
			array(
				array(
					'table' => 'user_konselor',
					'parameter' => 'user_konselor.id=kelas.konselor_id'
				)
			),
			array(
				'kelas.user_id' => $this->session->userdata('id')
			),
			'kelas',
			'asc'
		);

		$konselor = $this->Main_model->get_where(
			'user_konselor',
			array(
				'user_id' => $this->session->userdata('id')
			),
			'nama_lengkap',
			'asc'
		);

		$profil = $this->Main_model->get_where(
			'user_info',
			array(
				'user_id' => $this->session->userdata('id')
			)
		);

		$codeSettled = $this->Main_model->get_where(
			'sosiometri',
			[
				'user_id' => $this->session->userdata('id')
			]
		);

		// $get_ticket = $this->GetModel->getLastTicket($this->session->userdata('id'));
		$get_ticket = $this->GetModel->getLastTicketSociometri($this->session->userdata('id'));
		$day_remaining = 0;
		// printA($get_ticket);

		if ($get_ticket) {
			$day_remaining = ceil((strtotime($get_ticket[0]['tgl_kadaluarsa']) - time()) / (60 * 60 * 24));
			$content = 'layouts/sosiometri/index.php';
		} else {
			$content = 'key_sociometri';
		}

		if ($day_remaining <= 0) {
			$content = 'key_sociometri';
		}

		$data = [
			'get_kelas' => $kelas,
			'get_konselor' => $konselor,
			'get_profil' => $profil,
			'codeSettled' => $codeSettled,
			// 'content' => 'layouts/sosiometri/index.php'
			'content' => $content
		];

		$this->load->view('main.php', $data, false);
	}

	public function setcode()
	{
		$pertanyaan = $this->Main_model->get('sosiometri_pertanyaan');
		$codeSettled = $this->Main_model->get_where('sosiometri', ['user_id' => $this->session->userdata('id')]);

		$data = [
			'pertanyaan' => $pertanyaan,
			'codeSettled' => $codeSettled ? $codeSettled : [],
			'content' => 'layouts/sosiometri/sosiometri.setcode.php'
		];

		$this->load->view('main.php', $data, false);
	}

	public function codeSave()
	{
		$request = $this->input->post();

		$codeSettled = $this->Main_model->get_where('sosiometri', ['user_id' => $this->session->userdata('id')]);

		$urlUsed = $this->Main_model->get_where('sosiometri', [
			'user_id !=' => $this->session->userdata('id'),
			'url' => $request['url']
		]);

		// Check if url duplicated
		if ($urlUsed) {
			$this->session->set_flashdata('error', 'Url telah digunakan. Silahkan coba Url lain.');
			redirect('sosiometri');

			return false;
		}

		// Insert new record
		if (!$codeSettled) {

			// Reset data
			$data = [
				'id_pertanyaan' => $request['id_pertanyaan'],
				'judul' => $request['judul'],
				'jumlah_pilihan' => $request['jumlah_pilihan'],
				'bobot_penilaian' => serialize($request['bobot_penilaian']),
				'url' => $request['url'],
				'user_id' => $this->session->userdata('id'),
			];

			// Save config
			$this->Main_model->insert_data('sosiometri', $data);
			$this->session->set_flashdata('success', 'code');
		} else {

			// Reset data
			$data = [
				'id_pertanyaan' => $request['id_pertanyaan'],
				'judul' => $request['judul'],
				'jumlah_pilihan' => $request['jumlah_pilihan'],
				'bobot_penilaian' => serialize($request['bobot_penilaian']),
				'url' => $request['url'],
				'user_id' => $this->session->userdata('id'),
				'updated_at' => date('Y-m-d h:i:s'),
			];

			// Update existing record
			$this->Main_model->update_data('sosiometri', $data, ['id' => $request['id']]);
			$this->session->set_flashdata('success', 'code');
		}

		redirect('sosiometri');
	}

	public function detail($idKelas)
	{
		// Get sosiometri respon by class
		$sosiometriResponse = $this->Main_model->join(
			'sosiometri_respon',
			'*',
			[
				[
					'table' => 'kelas_siswa',
					'parameter' => 'kelas_siswa.id=sosiometri_respon.id_siswa'
				]
			],
			[
				'kelas_siswa.id_kelas' => $idKelas
			],
			'kelas_siswa.nis',
			'asc'
		);

		// Class info
		$kelas = $this->Main_model->get_where('kelas', ['id' => $idKelas]);

		// Gender count
		$genderCount = $this->getGenderCount($this->getStudentByClass($idKelas));

		// printA($sosiometriResponse);
		// printA($kelas);
		// printA($this->unserializeDecision($sosiometriResponse));

		$data = [
			'data' => [
				'tabulasi' => [
					'data' => $this->getStudentByClassWithResponse($idKelas),
					'dataTotal' => count($this->getStudentByClassWithResponse($idKelas)),
					'studentTotal' => count($this->getStudentByClass($idKelas)),
					'girls' => $genderCount['girls'],
					'boys' => $genderCount['boys'],
				],
				'details' => $this->unserializeDecision($sosiometriResponse),
				'responded' => $sosiometriResponse ? count($sosiometriResponse) : 0,
				'kelas_total' => $kelas ? $kelas[0]['jumlah_siswa'] : 0,
				'kelas_detail' => $kelas ? $kelas[0] : [],
			],
			'content' => 'layouts/sosiometri/sosiometri.detail.php'
		];

		$this->load->view('main.php', $data, false);
	}

	public function report($idKelas)
	{
		$this->load->library('fpdf_diag');

		// Data resources
		$param = $this->input->post();

		// printA($param);

		// $config = [];
		$config = [
			// 'url' => $param['url'],
			'filename' => $param['filename'],
			'type' => $param['type'],
			'b64' => true,
			// 'width' => $param['width'],
			'svg' => $param['svg'],
		];

		/* eCurl */
		$curl = curl_init($param['url']);

		/* Set JSON data to POST */
		curl_setopt($curl, CURLOPT_POSTFIELDS, $config);

		/* Define content type */
		curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);

		/* Return json */
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		/* make request */
		$result = curl_exec($curl);

		/* close curl */
		curl_close($curl);

		$surat = $this->getSurat();
		$kelompok = $this->getKelompok($idKelas);
		// $kelas = $this->getKelas($kelompok['kelas']);
		$kelas = $this->getKelas($idKelas);
		$tahunAjaran = $kelas['tahun_ajaran'] ? $kelas['tahun_ajaran'] : $this->Main_model->getTahunAjaran();

		$sosiometriSiswa = $this->getStudentByClassWithResponse($idKelas);
		$amountOfSosiometriSiswa = $sosiometriSiswa ? count($sosiometriSiswa) : 0;
		$sosiometriSiswaResponedOnly = $this->getRespondedStudentOnly($idKelas);
		$amountOfResponded = $sosiometriSiswaResponedOnly ? count($sosiometriSiswaResponedOnly) : 0;
		$lastRespondedDate = $sosiometriSiswaResponedOnly ? date('d/m/Y', strtotime($sosiometriSiswaResponedOnly[0]['created_at'])) : date('d/m/Y');

		$question = $this->getQuestion($idKelas);
		$indexPilihan = $this->getStudentByClassWithResponse($idKelas);
		// printA($question);

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);
		$pdf->SetFont('Arial', '', 11);

		/**
		 * LOGO SECTION
		 */
		if ($surat['logo']) {
			$pdf->Image('./uploads/logo/' . $surat['logo']['path'], 4, 10, 35, 27);
		} else if (@$surat['dataSurat']['logo_kiri']) {
			$pdf->Image('./uploads/logo/' . $surat['dataSurat']['user_id'] . '/' . $surat['dataSurat']['logo_kiri'], 4, 10, 35, 27);
		} else {
			$pdf->Image('./assets/img/logo_iki.png', 8, 6, 30, 27);
		}

		if (@$surat['dataSurat']['logo_kanan']) {
			// $pdf->Image(base_url('uploads/logo/' . $surat['dataSurat']['user_id'] . '/' . $surat['dataSurat']['logo_kanan']), 170, 10, 35, 27);
			$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/uploads/logo/' . $surat['dataSurat']['user_id'] . '/' . $surat['dataSurat']['logo_kanan'], 170, 10, 35, 27);
		} else {
			// $pdf->Image(base_url('assets/img/logo_adebk.jpeg'), 170, 6, 30, 27);
			$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/assets/img/logo_adebk.jpeg', 170, 6, 30, 27);
		}

		/**
		 * KOP SURAT SECTION
		 */
		$pdf->Ln(1);
		$pdf->Cell(205, 6, strtoupper(@$surat['dataSurat']['baris_pertama']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 11);
		$pdf->Cell(205, 6, strtoupper(@$surat['dataSurat']['baris_kedua']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(205, 6, strtoupper(@$surat['dataSurat']['baris_ketiga']), 0, 0, 'C');

		if (@$surat['dataSurat']['baris_kelima']) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln();
		}

		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(205, 6, @$surat['dataSurat']['baris_keempat'], 0, 0, 'C');

		if (@$surat['dataSurat']['baris_kelima']) {
			$pdf->Ln(4);
			$pdf->Cell(205, 6, @$surat['dataSurat']['baris_kelima'], 0, 0, 'C');
		}

		$pdf->Ln();
		$pdf->SetLineWidth(1);
		$pdf->Line(10, 38, 200, 38);
		$pdf->SetLineWidth(0);
		$pdf->Line(10, 39, 200, 39);
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

		/**
		 * REPORT TITLE SECTION
		 */
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(35, 7, 'Rahasia', 1, 0, 'C');
		$pdf->Cell(3);
		$pdf->Ln(10);
		$pdf->Cell(185, 6, 'SOSIOMETRI', 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, strtoupper(getField('user_info', 'instansi', array('id' => $this->session->userdata('id')))), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'TAHUN AJARAN ' . $tahunAjaran, 0, 0, 'C');

		/**
		 * REPORT SUBTITLE SECTION
		 */
		$pdf->SetFont('Arial', '', 12);
		$pdf->Ln(10);
		$pdf->Cell(50, 6, 'Identitas Kelas/Kelompok', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $kelas['kelas'], 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Tanggal Pengadministrasian', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, $lastRespondedDate, 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(50, 6, 'Jumlah Peserta', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $amountOfSosiometriSiswa . ' Siswa', 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Jumlah Responden', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, $amountOfResponded . ' Siswa', 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);

		/**
		 * SOCIOGRAM SECTION
		 */
		$pdf->Cell(50, 6, 'A. SOSIOGRAM', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->MultiCell(200, 6, @$question['pertanyaan'], 0, 'L');

		if ($result) {
			$decodedImg = $this->convertImage('data:image/png;base64,' . $result);

			// //  Check if image was properly decoded
			if ($decodedImg !== false) {
				//  Save image to a temporary location
				if (file_put_contents(self::TEMPIMGLOC, $decodedImg) !== false) {
					$pdf->Image(self::TEMPIMGLOC);

					//  Delete image from server
					unlink(self::TEMPIMGLOC);
				}
			}
		}

		/**
		 * TABULASI ARAH PILIHAN SECTION
		 */
		$tabulasi = [
			'tabulasiData' => $this->getStudentByClassWithResponse($idKelas),
			'genderCount' => $this->getGenderCount($this->getStudentByClass($idKelas)),
			'studentTotal' => count($this->getStudentByClass($idKelas))
		];

		if ($tabulasi['tabulasiData']) {
			$this->reportTabulasiData($pdf, $tabulasi);
		}

		/**
		 * INDEKS PILIHAN SECTION
		 */
		$pdf->AddPage('P');
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, 'C. INDEKS PILIHAN', 0, 0, 'L');
		$pdf->Ln();
		$header = ['NIS', 'NAMA', 'NILAI'];

		$this->FancyTable($pdf, $header, $indexPilihan, $idKelas);

		/**
		 * FOOTER SECTION
		 */
		$pdf->SetLeftMargin(10);
		$pdf->Ln(5);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(178, 6, '......., ................. 20....', 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(178, 6, 'Pengolah Data', 0, 0, 'R');
		$pdf->Ln(20);
		// $pdf->Cell(178, 6, getField('user_konselor', 'nama_lengkap', array('id' => $kelas['konselor_id'])), 0, 0, 'R');
		$pdf->Cell(178, 6, '(..........................................)', 0, 0, 'R');

		// $pdf->SetTitle('Laporan Sosiometri ' . $get_kelompok[0]['nama_kelompok'] . '.pdf');
		// $pdf->SetTitle('Laporan Sosiometri - ' . $kelas['kelas'] . '.pdf');

		// $pdf->Output('I', 'Laporan Sosiometri ' . $get_kelompok[0]['nama_kelompok'] . '.pdf', FALSE);
		header("Content-type:application/pdf");
		$pdf->Output('D', 'Laporan Sosiometri - ' . $kelas['kelas'] . '.pdf', FALSE);
	}

	public function reportTabulasiData($pdf, $tabulasiData)
	{
		// printA($tabulasiData);
		if (!$tabulasiData['tabulasiData']) {
			return false;
		}

		$headerData = array_chunk($tabulasiData['tabulasiData'], 20);

		foreach ($headerData as $dividedData) {
			$pdf->AddPage('L');
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(50, 6, 'B. TABULASI ARAH PILIHAN', 0, 0, 'L');
			$pdf->Ln();

			// Decription section
			$pdf->SetFont('Arial', '', 8);
			$pdf->Ln(10);
			$pdf->Cell(30, 6, 'Jumlah Peserta', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(30, 6, $tabulasiData['studentTotal'], 0, 0, 'L');
			$pdf->Cell(20);
			$pdf->Cell(56, 6, 'Keterangan', 0, 0, 'L');

			$pdf->Ln();
			$pdf->Cell(30, 6, 'Laki-laki', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(30, 6, $tabulasiData['genderCount']['boys'] . ' Siswa', 0, 0, 'L');
			$pdf->Cell(20);
			$pdf->Cell(5, 6, '1', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(10, 6, 'dipilih sebagai pilihan ke-n', 0, 0, 'L');

			$pdf->Ln();
			$pdf->Cell(30, 6, 'Perempuan', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(30, 6, $tabulasiData['genderCount']['girls'] . ' Siswa', 0, 0, 'L');
			$pdf->Cell(20);
			$pdf->Cell(5, 6, 'X', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(10, 6, 'ditolak', 0, 1, 'L');

			// Table section
			$pdf->SetFillColor(52, 58, 64);
			$pdf->SetTextColor(255);
			$pdf->SetDrawColor(52, 58, 64);
			$pdf->SetFont('Arial', 'B', 6);

			$pdf->Cell(10, 10, 'NIS', 1, 0, 'C', true);
			$pdf->Cell(30, 10, 'NAMA', 1, 0, 'L', true);
			$pdf->Cell((count($dividedData) * 10), 5, 'Pemilih/Penolak', 1, 0, 'C', true);
			$pdf->Cell(20, 10, 'BOBOT PEMILIH', 1, 0, 'C', true);
			$pdf->Cell(20, 10, 'BOBOT PENOLAK', 1, 0, 'C', true);
			$pdf->Cell(10, 5, '', 0, 1);
			$pdf->Cell(40, 5, '', 0, 0);

			foreach ($dividedData as $indexNis => $arrayNIS) {
				$pdf->Cell(10, 5, $arrayNIS['nis'], 1, ($indexNis == (count($dividedData) - 1) ? 1 : 0), 'C', true);
			}

			// Color and font restoration
			$pdf->SetFillColor(224, 235, 255);
			$pdf->SetTextColor(0);
			$fill = false;

			// Data rows
			foreach ($tabulasiData['tabulasiData'] as $row) {
				// $pdf->Cell(10, 5, '', 0, 0, 'C');
				$pdf->Cell(10, 5, $row['nis'], 1, 0, 'C', $fill);
				$pdf->Cell(30, 5, $row['nama'], 1, 0, 'L', $fill);

				foreach ($dividedData as $indexNis => $arrayNIS) {
					if ($row['pilihan']) {
						if (in_array($arrayNIS['id'], $row['pilihan'])) {
							$pdf->Cell(10, 5, '1', 1, 0, 'C', $fill);
						} else {
							if ($row['pilihan_negatif'] !== '') {
								if ($arrayNIS['id'] == $row['pilihan_negatif']) {
									$pdf->Cell(10, 5, 'X', 1, 0, 'C', $fill);
								} else {
									$pdf->Cell(10, 5, '', 1, 0, 'C', $fill);
								}
							} else {
								$pdf->Cell(10, 5, '', 1, 0, 'C', $fill);
							}
						}
					} else {
						$pdf->Cell(10, 5, '', 1, 0, 'C', $fill);
					}
				}

				$pdf->Cell(20, 5, $row['score_pemilih'], 1, 0, 'C', $fill);
				$pdf->Cell(20, 5, $row['score_penolak'], 1, 1, 'C', $fill);

				$fill = !$fill;
				// $pdf->Cell(10, 5, '', 0, 0, 'C');
			}
		}
	}

	public function reportTest($idKelas)
	{
		$this->load->library('fpdf_diag');

		$tabulasiData = $this->getStudentByClassWithResponse($idKelas);


		if (!$tabulasiData) {
			return false;
		}
		$headerData = array_chunk($tabulasiData, 20);
		// printA($headerData);

		$pdf = new PDF_Diag();
		$pdf->AliasNbPages();

		foreach ($headerData as $dividedData) {
			$pdf->AddPage('L');
			// $pdf->Cell(50, 6, 'B. TABULASI ARAH PILIHAN', 0, 0, 'L');
			// $pdf->Ln();

			// $pdf->SetLeftMargin(0);
			$pdf->SetFont('Arial', '', 12);
			$pdf->Ln(10);
			$pdf->Cell(30, 6, 'Jumlah Peserta', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(30, 6, 36, 0, 0, 'L');
			$pdf->Cell(20);
			$pdf->Cell(56, 6, 'Keterangan', 0, 0, 'L');

			$pdf->Ln();
			$pdf->Cell(30, 6, 'Laki-laki', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(30, 6, '15' . ' Siswa', 0, 0, 'L');
			$pdf->Cell(20);
			$pdf->Cell(5, 6, '1', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(10, 6, 'dipilih sebagai pilihan ke-n', 0, 0, 'L');

			$pdf->Ln();
			$pdf->Cell(30, 6, 'Perempuan', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(30, 6, '21' . ' Siswa', 0, 0, 'L');
			$pdf->Cell(20);
			$pdf->Cell(5, 6, 'X', 0, 0, 'L');
			$pdf->Cell(2, 6, ':', 0, 0, 'C');
			$pdf->Cell(10, 6, 'ditolak', 0, 1, 'L');

			// Table Section
			$pdf->SetFillColor(52, 58, 64);
			$pdf->SetTextColor(255);
			$pdf->SetDrawColor(52, 58, 64);
			$pdf->SetFont('Arial', 'B', 6);

			$pdf->Cell(10, 10, 'NIS', 1, 0, 'C', true);
			$pdf->Cell(30, 10, 'NAMA', 1, 0, 'L', true);
			$pdf->Cell((count($dividedData) * 10), 5, 'Pemilih/Penolak', 1, 0, 'C', true);
			$pdf->Cell(20, 10, 'BOBOT PEMILIH', 1, 0, 'C', true);
			$pdf->Cell(20, 10, 'BOBOT PENOLAK', 1, 0, 'C', true);
			$pdf->Cell(10, 5, '', 0, 1);
			$pdf->Cell(40, 5, '', 0, 0);

			foreach ($dividedData as $indexNis => $arrayNIS) {
				$pdf->Cell(10, 5, $arrayNIS['nis'], 1, ($indexNis == (count($dividedData) - 1) ? 1 : 0), 'C', true);
			}

			// Color and font restoration
			$pdf->SetFillColor(224, 235, 255);
			$pdf->SetTextColor(0);
			$fill = false;

			// Data rows
			foreach ($tabulasiData as $row) {
				// $pdf->Cell(10, 5, '', 0, 0, 'C');
				$pdf->Cell(10, 5, $row['nis'], 1, 0, 'C', $fill);
				$pdf->Cell(30, 5, $row['nama'], 1, 0, 'L', $fill);

				foreach ($dividedData as $indexNis => $arrayNIS) {
					if ($row['pilihan']) {
						if (in_array($arrayNIS['id'], $row['pilihan'])) {
							$pdf->Cell(10, 5, '1', 1, 0, 'C', $fill);
						} else {
							if ($row['pilihan_negatif'] !== '') {
								if ($arrayNIS['id'] == $row['pilihan_negatif']) {
									$pdf->Cell(10, 5, 'X', 1, 0, 'C', $fill);
								} else {
									$pdf->Cell(10, 5, '', 1, 0, 'C', $fill);
								}
							} else {
								$pdf->Cell(10, 5, '', 1, 0, 'C', $fill);
							}
						}
					} else {
						$pdf->Cell(10, 5, '', 1, 0, 'C', $fill);
					}
				}

				$pdf->Cell(20, 5, $row['score_pemilih'], 1, 0, 'C', $fill);
				$pdf->Cell(20, 5, $row['score_penolak'], 1, 1, 'C', $fill);

				$fill = !$fill;
				// $pdf->Cell(10, 5, '', 0, 0, 'C');
			}
		}

		// $pdf->Cell(10, 10, 'NIS', 1, 0, 'C');
		// $pdf->Cell(30, 10, 'NAMA', 1, 0);
		// $pdf->Cell(200, 5, 'Pemilih/Penolak', 1, 0, 'C');
		// $pdf->Cell(20, 10, 'BOBOT PEMILIH', 1, 0, 'C');
		// $pdf->Cell(20, 10, 'BOBOT PENOLAK', 1, 0, 'C');
		// $pdf->Cell(0, 5, '', 0, 1);

		// Second line (row)
		// $pdf->Cell(50, 5, '', 0, 0);
		// $pdf->Cell(10, 5, 'q1', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q2', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q3', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q4', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q5', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q6', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q7', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q8', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q9', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q10', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q11', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q12', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q13', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q14', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q15', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q16', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q17', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q18', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q19', 1, 0, 'C');
		// $pdf->Cell(10, 5, 'q20', 1, 1, 'C');

		// Data rows
		// $pdf->Cell(10, 5, '', 0, 0, 'C');
		// $pdf->Cell(10, 5, '8989', 1, 0, 'C');
		// $pdf->Cell(30, 5, 'Cerry Cihuy', 1, 0);
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '1', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '1', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(10, 5, '', 1, 0, 'C');
		// $pdf->Cell(20, 5, '4', 1, 0, 'C');
		// $pdf->Cell(20, 5, '0', 1, 1, 'C');
		// $pdf->Cell(10, 5, '', 0, 0, 'C');

		$pdf->setAutoPageBreak(1, 25);

		$pdf->SetTitle('Laporan Sosiometri ' . 'TES' . '.pdf');

		$pdf->Output();
	}

	public function FancyTable($pdf, $header, $data, $idKelas)
	{
		$studentTotal = count($this->getStudentByClass($idKelas));

		// Colors, line width and bold font
		// $pdf->SetFillColor(255, 0, 0);
		// $pdf->SetTextColor(255);
		// $pdf->SetDrawColor(128, 0, 0);
		$pdf->SetFillColor(52, 58, 64);
		$pdf->SetTextColor(255);
		$pdf->SetDrawColor(52, 58, 64);
		$pdf->SetLineWidth(.3);
		$pdf->SetFont('Arial', 'B');
		// Header
		$w = array(15, 80, 50);
		for ($i = 0; $i < count($header); $i++)
			$pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
		$pdf->Ln();
		// Color and font restoration
		$pdf->SetFillColor(224, 235, 255);
		$pdf->SetTextColor(0);
		$pdf->SetFont('Arial', '', '8');
		// Data
		$fill = false;
		foreach ($data as $row) {
			// $pdf->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
			// $pdf->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
			// $pdf->Cell($w[2], 6, number_format($row[2]), 'LR', 0, 'R', $fill);
			// $pdf->Cell($w[3], 6, number_format($row[3]), 'LR', 0, 'R', $fill);
			// $pdf->Ln();
			// $fill = !$fill;
			$nilai = $row['score_pemilih'] / $studentTotal == 0 ? 0 : number_format((float)($row['score_pemilih'] / $studentTotal), 2, ',', '');

			$pdf->Cell($w[0], 6, $row['nis'], 'LR', 0, 'L', $fill);
			$pdf->Cell($w[1], 6, $row['nama'], 'LR', 0, 'L', $fill);
			$pdf->Cell($w[2], 6, $row['score_pemilih'] . ' / ' . $studentTotal . ' = ' . $nilai, 'LR', 0, 'R', $fill);
			$pdf->Ln();
			$fill = !$fill;
		}
		// Closing line
		$pdf->Cell(array_sum($w), 0, '', 'T');
	}

	public function convertImage($dataURI)
	{
		$dataPieces = explode(',', $dataURI);
		$encodedImg = $dataPieces[1];
		$decodedImg = base64_decode($encodedImg);

		return $decodedImg;
	}

	public function getImage($dataURI)
	{
		$img = explode(',', $dataURI, 2);
		$pic = 'data://text/plain;base64,' . $img[1];
		$type = explode("/", explode(':', substr($dataURI, 0, strpos($dataURI, ';')))[1])[1]; // get the image type
		if ($type == "png" || $type == "jpeg" || $type == "gif") return array($pic, $type);
		return false;
	}

	public function getQuestion($idKelas)
	{
		if (!$idKelas) {
			show_error('Error When Fetch data sociometri question', 500);
			return [];
		}

		$pertanyaan = $this->Main_model->join(
			'sosiometri_pertanyaan',
			'sosiometri_pertanyaan.*',
			[
				[
					'table' => 'sosiometri',
					'parameter' => 'sosiometri.id_pertanyaan=sosiometri_pertanyaan.id'
				],
				[
					'table' => 'sosiometri_respon',
					'parameter' => 'sosiometri_respon.id_sosiometri=sosiometri.id'
				],
				[
					'table' => 'kelas_siswa',
					'parameter' => 'kelas_siswa.id=sosiometri_respon.id_siswa'
				]
			],
			[
				'kelas_siswa.id_kelas' => $idKelas
			]
		);

		return $pertanyaan ? $pertanyaan[0] : [];
	}

	public function getRespondedStudentOnly($idKelas)
	{
		if (!$idKelas) {
			show_error('Error When Fetch data responded student only', 500);
			return [];
		}

		$sosiometriResponse = $this->Main_model->join(
			'sosiometri_respon',
			'*',
			[
				[
					'table' => 'kelas_siswa',
					'parameter' => 'kelas_siswa.id=sosiometri_respon.id_siswa'
				]
			],
			[
				'kelas_siswa.id_kelas' => $idKelas
			],
			'sosiometri_respon.created_at',
			'desc'
		);

		return $sosiometriResponse ? $sosiometriResponse : [];
	}

	public function getKelas($idKelas)
	{
		if (!$idKelas) {
			show_error('Error When Fetch data kelas', 500);
			return [];
		}

		$kelas = $this->Main_model->get_where_in('kelas', 'id', explode(',', $idKelas));

		return $kelas ? $kelas[0] : [];
	}

	public function getKelompok($idKelas)
	{
		if (!$idKelas) {
			show_error('Error When Fetch data kelompok', 500);
			return [];
		}

		$kelompok = $this->Main_model->get_where('kelompok', ['id' => $idKelas]);

		return $kelompok ? $kelompok[0] : [];
	}

	public function getSurat()
	{
		$surat = $this->Main_model->get_where('user_surat', ['user_id' => $this->session->userdata('id')]);

		if (!$surat) {
			show_error('Error When Fetch data surat', 500);
			// return [];
		}

		if ($surat[0]['logo'] != 'other' || $surat[0]['logo'] != '') {
			$logo = $this->Main_model->get_where('logo_daerah', ['id' => $surat[0]['logo']]);

			if (!$logo) {
				$logo = false;
			}
		} else {
			$logo = false;
		}

		return $surat ? [
			'dataSurat' => $surat[0],
			'logo' => $logo ? $logo[0] : []
		] : [];
	}

	public function getSociogramData($idKelas)
	{
		if (!$idKelas) {
			$return = json_encode([
				'success' => false,
				'message' => 'ID Kelas not found'
			]);
		} else {
			$data = $this->getStudentByClassWithResponse($idKelas);

			if (!$data) {
				$return = json_encode([
					'success' => false,
					'message' => 'Data not found'
				]);
			} else {
				$studentSelected = [];

				foreach ($data as $index => $row) {
					if ($row['pilihan']) {
						if ($row['pilihan_negatif']) {
							$connections = $row['pilihan'];
							array_push($connections, $row['pilihan_negatif']);

							$row['connections'] = $connections;
						} else {
							$row['connections'] = $row['pilihan'];
						}

						foreach ($row['connections'] as $id) {
							if (isset($studentSelected[$id])) {
								$studentSelected[$id] += 1;
							} else {
								$studentSelected[$id] = 1;
							}
						}
					} else {
						$row['connections'] = [];
					}

					$data[$index] = $row;
				}

				$return = json_encode([
					'success' => true,
					'occurrences' => $studentSelected,
					'data' => $data,
				]);
			}
		}

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output($return);
	}

	public function calculateBobotPenilaian($data)
	{
		if (!$data) {
			return [];
		}

		foreach ($data as $i => $row) {
			$bobotPemilih = 0;
			if ($row['pilihan'] && $row['bobot_penilaian']) {
				foreach ($row['pilihan'] as $index => $id) {
					$bobotPemilih += $row['bobot_penilaian'][$index];
				}
			}

			$row['score_pemilih'] = $bobotPemilih;
			$row['score_penolak'] = $row['pilihan_negatif'] ? 1 : 0;

			$data[$i] = $row;
		}

		return $data;
	}

	public function getStudentByClassWithResponse($idKelas)
	{
		if (!$idKelas) {
			return [];
		}

		$data = $this->Main_model->join(
			'kelas_siswa',
			'kelas_siswa.*, 
			sosiometri_respon.pilihan as pilihan, 
			sosiometri_respon.pilihan_negatif as pilihan_negatif,
			sosiometri.bobot_penilaian as bobot_penilaian',
			[
				[
					'table' => 'sosiometri_respon',
					'parameter' => 'sosiometri_respon.id_siswa=kelas_siswa.id'
				],
				[
					'table' => 'sosiometri',
					'parameter' => 'sosiometri.id=sosiometri_respon.id_sosiometri'
				]
			],
			[
				'kelas_siswa.id_kelas' => $idKelas
			],
			'kelas_siswa.nis',
			'asc'
		);

		if (!$data) {
			return [];
		}

		// $data = $this->unserializeDecision($data);
		foreach ($data as $index => $row) {
			$data[$index]['pilihan'] = unserialize($row['pilihan']);
			$data[$index]['bobot_penilaian'] = unserialize($row['bobot_penilaian']);
		}

		$data = $this->calculateBobotPenilaian($data);

		// printA($data);

		return $data ? $data : [];
	}

	public function getStudentByClass($idKelas)
	{
		if (!$idKelas) {
			return [];
		}

		$data = $this->Main_model->get_where('kelas_siswa', ['id_kelas' => $idKelas]);

		return $data ? $data : [];
	}

	public function getGenderCount($data)
	{
		if (!$data) {
			return [
				'girls' => 0,
				'boys' => 0
			];
		}

		$girls = $boys = [];
		foreach ($data as $row) {
			if ($row['jk'] == 'P') {
				$girls[] = $row;
			} else {
				$boys[] = $row;
			}
		}

		return [
			'girls' => $girls ? count($girls) : 0,
			'boys' => $boys ? count($boys) : 0,
		];
	}

	public function unserializeDecision($data)
	{
		if (!$data) {
			return [];
		}

		foreach ($data as &$row) {
			$tempDecision = [];

			$decisions = unserialize($row['pilihan']);

			if ($decisions) {
				foreach ($decisions as $decision) {
					$tempDecision[] = $this->getSiswaDetail($decision);
				}
			}

			$row['pilihan'] = $tempDecision;
		}

		return $data;
	}

	public function getSiswaDetail($idSiswa)
	{
		if (!$idSiswa) {
			return [];
		}

		$data = $this->Main_model->get_where('kelas_siswa', ['id' => $idSiswa]);

		return $data ? $data[0] : [];
	}
}
