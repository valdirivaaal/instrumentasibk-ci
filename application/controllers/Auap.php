<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auap extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	public function GenerateWord()
	{
		// Get a random word
		$nb = rand(3, 10);
		$w = '';
		for ($i = 1; $i <= $nb; $i++)
			$w .= chr(rand(ord('a'), ord('z')));
		return $w;
	}

	public function GenerateSentence($words = 500)
	{
		// Get a random sentence
		$nb = rand(20, $words);
		$s = '';
		for ($i = 1; $i <= $nb; $i++)
			$s .= $this->GenerateWord() . ' ';
		return substr($s, 0, -1);
	}

	public function index($jenjang = "")
	{
		// $this->load->library('encrypt');
		$this->load->library('encryption');
		$get_ticket = $this->Main_model->get_where('ticket', array('user_id' => $this->session->userdata('id')));
		$data['get_profil'] = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));

		$data['get_kelompok'] = $this->Main_model->get_where('kelompok', array('user_id' => $this->session->userdata('id')));

		if ($jenjang) {
			$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $jenjang));
		} else {
			$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $data['get_profil'][0]['jenjang']));
		}

		$data['get_auap'] = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => @$get_instrumen[0]['id']));

		$data['get_kode'] = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => @$data['get_auap'][0]['instrumen_id']));

		$data['jenjang'] =  $jenjang;

		if ($get_ticket) {
			if ($jenjang) {
				$data['kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $this->session->userdata('id'), 'jenjang' => $jenjang), 'kelas', 'asc');
			} else {
				$data['kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $this->session->userdata('id')), 'kelas', 'asc');
			}
			$data['content'] = 'auap.php';
		} else {
			$data['content'] = 'key';
		}


		$this->load->view('main.php', $data, FALSE);
	}

	public function view($id = "")
	{
		$data['get_profil'] = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$data['id'] = $id;
		if ($data['get_profil'][0]['jenjang'] == 'SMA') {
			$jenjang = 1;
		} elseif ($data['get_profil'][0]['jenjang'] == 'SMP') {
			$jenjang = 2;
		} elseif ($data['get_profil'][0]['jenjang'] == 'SD') {
			$jenjang = 3;
		}

		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $data['get_profil'][0]['jenjang']));
		$get_auap = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $get_instrumen[0]['id']));
		$data['get_jawaban'] = $this->Main_model->get_where('instrumen_jawaban', array('instrumen_id' => $get_auap[0]['id'], 'kelas' => $id));
		$data['get_kelas'] = $this->Main_model->get_where('kelas', array('id' => $id));
		$data['content'] = 'auap_detail.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function laporan_kelompok($id = "")
	{
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $get_user[0]['jenjang']));
		$get_kode = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $get_instrumen[0]['id']));
		$get_kelompok = $this->Main_model->get_where('kelompok', array('id' => $id));

		$get_data = $this->Main_model->get_where_in('instrumen_jawaban', 'kelas', explode(",", $get_kelompok[0]['kelas']), array('instrumen_id' => $get_kode[0]['id']));
		$get_surat = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$get_kelas = $this->Main_model->get_where_in('kelas', 'id', explode(",", $get_kelompok[0]['kelas']));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id']));

		if ($get_surat[0]['logo'] != 'other' || $get_surat[0]['logo'] != '') {
			$get_data_logo = $this->Main_model->get_where('logo_daerah', ['id' => $get_surat[0]['logo']]);
			if (!$get_data_logo) {
				$get_data_logo = '';
			}
		} else {
			$get_data_logo = '';
		}

		$total_peserta = 0;
		foreach ($get_kelas as $key => $value) {
			$total_peserta += $value['jumlah_siswa'];
		}

		foreach ($get_data as $key => $value) {
			$jawaban[] = unserialize($value['jawaban']);
		}

		$array_lampiran = array();
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (isset($jawaban[$value['id']])) {
					if ($jawaban[$value['id']] == 'SD') {
						$skor = 3;
					} elseif ($jawaban[$value['id']] == 'DS') {
						$skor = 2;
					} elseif ($jawaban[$value['id']] == 'KD') {
						$skor = 1;
					} else {
						$skor = 0;
					}
					$array_lampiran[$value_aspek['id']][$value['kode_pernyataan']]['jawaban'] = $jawaban[$value['id']];
					$array_lampiran[$value_aspek['id']][$value['kode_pernyataan']]['skor'] = $skor;
				}
			}
			$count_all_lampiran[$value_aspek['id']] = count($get_butir);
		}

		$count_j = 0;
		$array_pernyataan = array();
		foreach ($jawaban as $key => $value) {
			$array_jawaban[] = $value;
			foreach ($value as $key2 => $value2) {
				if ($value2 == 'SD') {
					$value2 = 3;
				} elseif ($value2 == 'DS') {
					$value2 = 2;
				} elseif ($value2 == 'KD') {
					$value2 = 1;
				} elseif ($value2 == 'TD' || $value2 == 'TPH') {
					$value2 = 0;
				}

				@$array_pernyataan[$key2] += $value2;
			}
		}


		foreach ($array_jawaban as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				$count_total_bidang[$get_butir[0]['aspek_id']][] = $get_butir[0]['id'];
			}
		}

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));

			foreach ($get_butir as $key => $value) {
				if (isset($array_pernyataan[$value['id']])) {
					@$total_skor[$value_aspek['kode_aspek']]['butir'] = count($get_butir);
					@$total_skor[$value_aspek['kode_aspek']]['skor'] += $array_pernyataan[$value['id']];
					@$total_skor[$value_aspek['kode_aspek']]['aspek'] = $value_aspek['aspek'];
					@$total_skor[$value_aspek['kode_aspek']]['total_butir'] = count($count_total_bidang[$value_aspek['id']]) * 3;
					@$total_skor[$value_aspek['kode_aspek']]['persentase'] += round(($array_pernyataan[$value['id']] / (count($count_total_bidang[$value_aspek['id']]) * 3) * 100), 2);
				}
			}
		}

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);
		$pdf->SetFont('Arial', '', 11);
		if (!empty($get_data_logo)) {
			$pdf->Image('./uploads/logo/' . $get_data_logo[0]['path'], 4, 10, 35, 27);
		} else if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image('./uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kiri'], 4, 10, 35, 27);
		} else {
			$pdf->Image('./assets/img/logo_iki.png', 8, 6, 30, 27);
		}

		if (@$get_surat[0]['logo_kanan']) {
			$pdf->Image(base_url('uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kanan']), 170, 10, 35, 27);
		} else {
			$pdf->Image(base_url('assets/img/logo_adebk.jpeg'), 170, 6, 30, 27);
		}
		$pdf->Ln(1);
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_pertama']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 11);
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_kedua']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_ketiga']), 0, 0, 'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln();
		}
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(205, 6, @$get_surat[0]['baris_keempat'], 0, 0, 'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(4);
			$pdf->Cell(205, 6, @$get_surat[0]['baris_kelima'], 0, 0, 'C');
		}

		$pdf->Ln();
		$pdf->SetLineWidth(1);
		$pdf->Line(10, 38, 200, 38);
		$pdf->SetLineWidth(0);
		$pdf->Line(10, 39, 200, 39);
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
		// setting jenis font yang akan digunakan

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(35, 7, 'Rahasia', 1, 0, 'C');
		$pdf->Cell(3);
		$pdf->Ln(10);
		$pdf->Cell(185, 6, 'LAPORAN KELOMPOK', 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'INSTRUMEN ALAT UNGKAP - ARAH PEMINATAN ' . $get_user[0]['jenjang'], 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, strtoupper(getField('user_info', 'instansi', array('id' => $this->session->userdata('id')))), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'TAHUN AJARAN 2019/2020', 0, 0, 'C');

		$pdf->SetFont('Arial', '', 12);
		$pdf->Ln(10);
		$pdf->Cell(50, 6, 'Identitas Kelompok', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $get_kelompok[0]['nama_kelompok'], 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Tanggal Pengadministrasian', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, date('d/m/Y'), 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(50, 6, 'Jumlah Peserta', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $total_peserta . ' Siswa', 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Ini buat apa', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, 'Apa ya', 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);

		$bidang['SBD'] = array('A');
		$bidang['BSN'] = array('B');
		$bidang['MIPA'] = array('C');
		$bidang['BHS'] = array('D');
		$bidang['SENI'] = array('E', 'F', 'G', 'H', 'I', 'J');
		$bidang['TEK'] = array('K', 'L', 'M', 'N', 'O');
		$bidang['ORG'] = array('P');
		$bidang['AGA'] = array('Q');
		$bidang['MMP'] = array('R', 'S');
		$bidang['KDP'] = array('T', 'U');
		$bidang['NAT'] = array('V', 'W', 'X');
		$bidang['KDK'] = array('Y', 'Z');
		$bidang['APD'] = array('AA', 'BB', 'CC');

		foreach ($bidang as $key => $value) {
			foreach ($value as $key2 => $value2) {
				if (isset($total_skor[$value2])) {
					$array_bidang[$key][] = $total_skor[$value2]['persentase'];
				}
			}
		}


		$pdf->Cell(50, 6, 'A. DATA ARAH PEMINATAN', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50, 6, '1. Grafik Skor Rata-Rata Pilihan Bidang Peminatan', 0, 0, 'L');

		$textColour = array(0, 0, 0);
		$rowLabels = array("SBD", "BSN", "MIPA", "BHS", "SENI", "TEK", "ORG", "AGA", "MMP", "KDP", "NAT", "KDK", "APD");
		$chartXPos = -5;
		$chartYPos = 200;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array(255, 100, 100),
			array(100, 255, 100),
			array(100, 100, 255),
			array(255, 255, 100),
		);

		$pdf->SetFont('Arial', '', 12);

		$data = array(
			array((isset($array_bidang['SBD'])) ? array_sum($array_bidang['SBD']) / count($array_bidang['SBD']) : 0),
			array((isset($array_bidang['BSN'])) ? array_sum($array_bidang['BSN']) / count($array_bidang['BSN']) : 0),
			array((isset($array_bidang['MIPA'])) ? array_sum($array_bidang['MIPA']) / count($array_bidang['MIPA']) : 0),
			array((isset($array_bidang['BHS'])) ? array_sum($array_bidang['BHS']) / count($array_bidang['BHS']) : 0),
			array((isset($array_bidang['SENI'])) ? array_sum($array_bidang['SENI']) / count($array_bidang['SENI']) : 0),
			array((isset($array_bidang['TEK'])) ? array_sum($array_bidang['TEK']) / count($array_bidang['TEK']) : 0),
			array((isset($array_bidang['ORG'])) ? array_sum($array_bidang['ORG']) / count($array_bidang['ORG']) : 0),
			array((isset($array_bidang['AGA'])) ? array_sum($array_bidang['AGA']) / count($array_bidang['AGA']) : 0),
			array((isset($array_bidang['MMP'])) ? array_sum($array_bidang['MMP']) / count($array_bidang['MMP']) : 0),
			array((isset($array_bidang['KDP'])) ? array_sum($array_bidang['KDP']) / count($array_bidang['KDP']) : 0),
			array((isset($array_bidang['NAT'])) ? array_sum($array_bidang['NAT']) / count($array_bidang['NAT']) : 0),
			array((isset($array_bidang['KDK'])) ? array_sum($array_bidang['KDK']) / count($array_bidang['KDK']) : 0),
			array((isset($array_bidang['APD'])) ? array_sum($array_bidang['APD']) / count($array_bidang['APD']) : 0),
		);


		// Compute the X scale
		$xScale = count($rowLabels) / ($chartWidth - 40);

		// Compute the Y scale

		$maxTotal = 100;

		foreach ($data as $dataRow) {
			$totalSales = 0;
			foreach ($dataRow as $dataCell) $totalSales += $dataCell;
			$maxTotal = ($totalSales > $maxTotal) ? $totalSales : $maxTotal;
		}

		$yScale = $maxTotal / $chartHeight;

		// Compute the bar width
		$barWidth = (1 / $xScale) / 1.5;

		// Add the axes:

		$pdf->SetFont('Arial', '', 9);

		// X axis
		$pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + $chartWidth, $chartYPos);

		for ($i = 0; $i < count($rowLabels); $i++) {
			$pdf->SetXY($chartXPos + 40 +  $i / $xScale, $chartYPos);
			$pdf->Cell($barWidth, 10, $rowLabels[$i], 0, 0, 'C');
		}

		// Y axis
		$pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + 30, $chartYPos - $chartHeight - 8);

		for ($i = 0; $i <= $maxTotal; $i++) {
			if ($i % 10 == 0) {
				$pdf->SetXY($chartXPos + 7, $chartYPos - 5 - $i / $yScale);
				$pdf->Cell(20, 10, $i, 0, 1, 'R');
				$pdf->Line($chartXPos + 28, $chartYPos - $i / $yScale, $chartXPos + 30, $chartYPos - $i / $yScale);
			}
		}

		// Create the bars
		$xPos = $chartXPos + 40;
		$bar = 0;

		foreach ($data as $dataRow) {

			// Total up the sales figures for this product
			$totalSales = 0;
			foreach ($dataRow as $dataCell) $totalSales += $dataCell;

			// Create the bar
			$colourIndex = $bar % count($chartColours);
			$pdf->SetFillColor($chartColours[$colourIndex][0], $chartColours[$colourIndex][1], $chartColours[$colourIndex][2]);
			$pdf->Rect($xPos, $chartYPos - ($totalSales / $yScale), $barWidth, $totalSales / $yScale, 'DF');
			$xPos += (1 / $xScale);
			$bar++;
		}

		$pdf->Ln(85);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6);
		$pdf->Cell(50, 6, '2. Tabulasi Data Kelompok Arah Peminatan', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'Tabel 1. Tabulasi Kelompok Peminatan', 0, 0, 'C');
		$pdf->Ln();

		$nama_bidang['SBD'] = array('SOSIAL BUDAYA');
		$nama_bidang['BSN'] = array('BISNIS');
		$nama_bidang['MIPA'] = array('MIPA');
		$nama_bidang['BHS'] = array('BAHASA DAN SASTRA');
		$nama_bidang['SENI'] = array('SENI');
		$nama_bidang['TEK'] = array('TEKNOLOGI');
		$nama_bidang['ORG'] = array('OLAHRAGA');
		$nama_bidang['AGA'] = array('KEAGAMAAN');
		$nama_bidang['MMP'] = array('MAKANAN-MINUMAN/GIZI DAN PARIWISATA');
		$nama_bidang['KDP'] = array('KESEHATAN DAN PENGOBATAN');
		$nama_bidang['NAT'] = array('USAHA NATURAL');
		$nama_bidang['KDK'] = array('KEMILITERAN DAN KEPOLISIAN');
		$nama_bidang['APD'] = array('ANALISIS PERILAKU DAN PENGEMBANGAN DIRI');

		$pdf->Cell(12);
		$pdf->Cell(90, 7, 'Bidang Peminatan', 1, 0, 'C');
		$pdf->Cell(40, 7, 'Skor Keseluruhan', 'R,B,T', 0, 'C');
		$pdf->Cell(36, 7, 'Skor Rata-Rata', 'R,B,T', 1, 'C');

		$pdf->SetWidths(array(90, 40, 36));
		// srand(microtime() * 1000000);
		$pdf->SetAligns(array('L', 'C', 'C'));

		$pdf->SetLeftMargin(22);
		$count_alphabet = 'A';
		foreach ($nama_bidang as $key => $value) {
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->MultiCell(166, 7, $count_alphabet++ . ". " . $value[0] . " (" . $key . ")", 'R,L', 'L');
			foreach ($bidang[$key] as $key2 => $value2) {
				$pdf->SetFont('Arial', '', 12);
				$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id'], 'kode_aspek' => $value2));
				$pdf->Row(array($get_aspek[0]['aspek'], (isset($total_skor[$get_aspek[0]['kode_aspek']]['skor'])) ? $total_skor[$get_aspek[0]['kode_aspek']]['skor'] . " (" . $total_skor[$get_aspek[0]['kode_aspek']]['persentase'] . " %)" : "0 (0%)", (isset($total_skor[$get_aspek[0]['kode_aspek']]['skor'])) ? $total_skor[$get_aspek[0]['kode_aspek']]['skor'] / ($total_skor[$get_aspek[0]['kode_aspek']]['total_butir'] / ($total_skor[$get_aspek[0]['kode_aspek']]['butir'] * 3)) : '-'));

				if ((isset($total_skor[$get_aspek[0]['kode_aspek']]['skor']))) {
					$tabulasi_peminatan[$get_aspek[0]['kode_aspek']] = $total_skor[$get_aspek[0]['kode_aspek']]['persentase'];
				}
			}
		}

		$pdf->Ln(3);
		$pdf->SetFont('Arial', 'BI', 12);
		$pdf->Cell(178, 6, 'CATATAN :', 0, 1, 'L');

		asort($tabulasi_peminatan);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(55, 6, 'Sub-Peminatan Terendah', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->MultiCell(106, 6, $total_skor[key($tabulasi_peminatan)]['aspek'] . " dengan skor " . $total_skor[key($tabulasi_peminatan)]['skor'] . " (" . $total_skor[key($tabulasi_peminatan)]['persentase'] . "%)", 0, 'L');

		arsort($tabulasi_peminatan);
		$pdf->Ln(5);
		$pdf->Cell(55, 6, 'Sub-Peminatan Tertinggi', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->MultiCell(106, 6, $total_skor[key($tabulasi_peminatan)]['aspek'] . " dengan skor " . $total_skor[key($tabulasi_peminatan)]['skor'] . " (" . $total_skor[key($tabulasi_peminatan)]['persentase'] . "%)", 0, 'L');

		$pdf->SetLeftMargin(10);
		$pdf->Ln(8);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(178, 6, 'Jakarta, ' . konversi_tanggal(date('Y-m-d')), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(178, 6, 'Pengolah Data', 0, 0, 'R');
		$pdf->Ln(20);
		$pdf->Cell(178, 6, getField('user_konselor', 'nama_lengkap', array('id' => $get_kelas[0]['konselor_id'])), 0, 0, 'R');

		$pdf->SetTitle('LAPORAN AUM ' . $get_kelompok[0]['nama_kelompok'] . '.pdf');

		$pdf->Output('I', 'LAPORAN AUM ' . $get_kelompok[0]['nama_kelompok'] . '.pdf', FALSE);
	}

	public function laporan_kelas($id = "")
	{
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $get_user[0]['jenjang']));
		$get_kode = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $get_instrumen[0]['id']));
		$get_data = $this->Main_model->get_where('instrumen_jawaban', array('kelas' => $id, 'instrumen_id' => $get_kode[0]['id']));
		$get_surat = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$get_kelas = $this->Main_model->get_where('kelas', array('id' => $id));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id']));

		if ($get_surat[0]['logo'] != 'other' || $get_surat[0]['logo'] != '') {
			$get_data_logo = $this->Main_model->get_where('logo_daerah', ['id' => $get_surat[0]['logo']]);
			if (!$get_data_logo) {
				$get_data_logo = '';
			}
		} else {
			$get_data_logo = '';
		}

		foreach ($get_data as $key => $value) {
			$jawaban[] = unserialize($value['jawaban']);
		}

		$array_lampiran = array();
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (isset($jawaban[$value['id']])) {
					if ($jawaban[$value['id']] == 'SD') {
						$skor = 3;
					} elseif ($jawaban[$value['id']] == 'DS') {
						$skor = 2;
					} elseif ($jawaban[$value['id']] == 'KD') {
						$skor = 1;
					} else {
						$skor = 0;
					}
					$array_lampiran[$value_aspek['id']][$value['kode_pernyataan']]['jawaban'] = $jawaban[$value['id']];
					$array_lampiran[$value_aspek['id']][$value['kode_pernyataan']]['skor'] = $skor;
				}
			}
			$count_all_lampiran[$value_aspek['id']] = count($get_butir);
		}

		$count_j = 0;
		$array_pernyataan = array();
		foreach ($jawaban as $key => $value) {
			$array_jawaban[] = $value;
			foreach ($value as $key2 => $value2) {
				if ($value2 == 'SD') {
					$value2 = 3;
				} elseif ($value2 == 'DS') {
					$value2 = 2;
				} elseif ($value2 == 'KD') {
					$value2 = 1;
				} elseif ($value2 == 'TD' || $value2 == 'TPH') {
					$value2 = 0;
				}

				@$array_pernyataan[$key2] += $value2;
			}
		}


		foreach ($array_jawaban as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				$count_total_bidang[$get_butir[0]['aspek_id']][] = $get_butir[0]['id'];
			}
		}

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));

			foreach ($get_butir as $key => $value) {
				if (isset($array_pernyataan[$value['id']])) {
					@$total_skor[$value_aspek['kode_aspek']]['butir'] = count($get_butir);
					@$total_skor[$value_aspek['kode_aspek']]['skor'] += $array_pernyataan[$value['id']];
					@$total_skor[$value_aspek['kode_aspek']]['aspek'] = $value_aspek['aspek'];
					@$total_skor[$value_aspek['kode_aspek']]['total_butir'] = count($count_total_bidang[$value_aspek['id']]) * 3;
					@$total_skor[$value_aspek['kode_aspek']]['persentase'] += round(($array_pernyataan[$value['id']] / (count($count_total_bidang[$value_aspek['id']]) * 3) * 100), 2);
				}
			}
		}


		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);
		$pdf->SetFont('Arial', '', 11);
		if (!empty($get_data_logo)) {
			$pdf->Image('./uploads/logo/' . $get_data_logo[0]['path'], 4, 10, 35, 27);
		} else if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image('./uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kiri'], 4, 10, 35, 27);
		} else {
			$pdf->Image('./assets/img/logo_iki.png', 8, 6, 30, 27);
		}

		if (@$get_surat[0]['logo_kanan']) {
			$pdf->Image(base_url('uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kanan']), 170, 10, 35, 27);
		} else {
			$pdf->Image(base_url('assets/img/logo_adebk.jpeg'), 170, 6, 30, 27);
		}
		$pdf->Ln(1);
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_pertama']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 11);
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_kedua']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_ketiga']), 0, 0, 'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln();
		}
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(205, 6, @$get_surat[0]['baris_keempat'], 0, 0, 'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(4);
			$pdf->Cell(205, 6, @$get_surat[0]['baris_kelima'], 0, 0, 'C');
		}

		$pdf->Ln();
		$pdf->SetLineWidth(1);
		$pdf->Line(10, 38, 200, 38);
		$pdf->SetLineWidth(0);
		$pdf->Line(10, 39, 200, 39);
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
		// setting jenis font yang akan digunakan

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(35, 7, 'Rahasia', 1, 0, 'C');
		$pdf->Cell(3);
		$pdf->Ln(10);
		$pdf->Cell(185, 6, 'LAPORAN KELOMPOK', 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'INSTRUMEN ALAT UNGKAP - ARAH PEMINATAN ' . $get_user[0]['jenjang'], 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, strtoupper(getField('user_info', 'instansi', array('id' => $this->session->userdata('id')))), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'TAHUN AJARAN 2019/2020', 0, 0, 'C');

		$pdf->SetFont('Arial', '', 12);
		$pdf->Ln(10);
		$pdf->Cell(50, 6, 'Identitas Kelas/Kelompok', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $get_kelas[0]['kelas'], 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Tanggal Pengadministrasian', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, date('d/m/Y'), 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(50, 6, 'Jumlah Peserta', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $get_kelas[0]['jumlah_siswa'] . ' Siswa', 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Ini buat apa', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, 'Apa ya', 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);

		$bidang['SBD'] = array('A');
		$bidang['BSN'] = array('B');
		$bidang['MIPA'] = array('C');
		$bidang['BHS'] = array('D');
		$bidang['SENI'] = array('E', 'F', 'G', 'H', 'I', 'J');
		$bidang['TEK'] = array('K', 'L', 'M', 'N', 'O');
		$bidang['ORG'] = array('P');
		$bidang['AGA'] = array('Q');
		$bidang['MMP'] = array('R', 'S');
		$bidang['KDP'] = array('T', 'U');
		$bidang['NAT'] = array('V', 'W', 'X');
		$bidang['KDK'] = array('Y', 'Z');
		$bidang['APD'] = array('AA', 'BB', 'CC');

		foreach ($bidang as $key => $value) {
			foreach ($value as $key2 => $value2) {
				if (isset($total_skor[$value2])) {
					$array_bidang[$key][] = $total_skor[$value2]['persentase'];
				}
			}
		}

		$pdf->Cell(50, 6, 'A. DATA ARAH PEMINATAN', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50, 6, '1. Grafik Skor Rata-Rata Pilihan Bidang Peminatan', 0, 0, 'L');

		$textColour = array(0, 0, 0);
		$rowLabels = array("SBD", "BSN", "MIPA", "BHS", "SENI", "TEK", "ORG", "AGA", "MMP", "KDP", "NAT", "KDK", "APD");
		$chartXPos = -5;
		$chartYPos = 200;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array(255, 100, 100),
			array(100, 255, 100),
			array(100, 100, 255),
			array(255, 255, 100),
		);

		$pdf->SetFont('Arial', '', 12);

		$data = array(
			array((isset($array_bidang['SBD'])) ? array_sum($array_bidang['SBD']) / count($array_bidang['SBD']) : 0),
			array((isset($array_bidang['BSN'])) ? array_sum($array_bidang['BSN']) / count($array_bidang['BSN']) : 0),
			array((isset($array_bidang['MIPA'])) ? array_sum($array_bidang['MIPA']) / count($array_bidang['MIPA']) : 0),
			array((isset($array_bidang['BHS'])) ? array_sum($array_bidang['BHS']) / count($array_bidang['BHS']) : 0),
			array((isset($array_bidang['SENI'])) ? array_sum($array_bidang['SENI']) / count($array_bidang['SENI']) : 0),
			array((isset($array_bidang['TEK'])) ? array_sum($array_bidang['TEK']) / count($array_bidang['TEK']) : 0),
			array((isset($array_bidang['ORG'])) ? array_sum($array_bidang['ORG']) / count($array_bidang['ORG']) : 0),
			array((isset($array_bidang['AGA'])) ? array_sum($array_bidang['AGA']) / count($array_bidang['AGA']) : 0),
			array((isset($array_bidang['MMP'])) ? array_sum($array_bidang['MMP']) / count($array_bidang['MMP']) : 0),
			array((isset($array_bidang['KDP'])) ? array_sum($array_bidang['KDP']) / count($array_bidang['KDP']) : 0),
			array((isset($array_bidang['NAT'])) ? array_sum($array_bidang['NAT']) / count($array_bidang['NAT']) : 0),
			array((isset($array_bidang['KDK'])) ? array_sum($array_bidang['KDK']) / count($array_bidang['KDK']) : 0),
			array((isset($array_bidang['APD'])) ? array_sum($array_bidang['APD']) / count($array_bidang['APD']) : 0),
		);


		// Compute the X scale
		$xScale = count($rowLabels) / ($chartWidth - 40);

		// Compute the Y scale

		$maxTotal = 100;

		foreach ($data as $dataRow) {
			$totalSales = 0;
			foreach ($dataRow as $dataCell) $totalSales += $dataCell;
			$maxTotal = ($totalSales > $maxTotal) ? $totalSales : $maxTotal;
		}

		$yScale = $maxTotal / $chartHeight;

		// Compute the bar width
		$barWidth = (1 / $xScale) / 1.5;

		// Add the axes:

		$pdf->SetFont('Arial', '', 9);

		// X axis
		$pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + $chartWidth, $chartYPos);

		for ($i = 0; $i < count($rowLabels); $i++) {
			$pdf->SetXY($chartXPos + 40 +  $i / $xScale, $chartYPos);
			$pdf->Cell($barWidth, 10, $rowLabels[$i], 0, 0, 'C');
		}

		// Y axis
		$pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + 30, $chartYPos - $chartHeight - 8);

		for ($i = 0; $i <= $maxTotal; $i++) {
			if ($i % 10 == 0) {
				$pdf->SetXY($chartXPos + 7, $chartYPos - 5 - $i / $yScale);
				$pdf->Cell(20, 10, $i, 0, 1, 'R');
				$pdf->Line($chartXPos + 28, $chartYPos - $i / $yScale, $chartXPos + 30, $chartYPos - $i / $yScale);
			}
		}

		// Create the bars
		$xPos = $chartXPos + 40;
		$bar = 0;

		foreach ($data as $dataRow) {

			// Total up the sales figures for this product
			$totalSales = 0;
			foreach ($dataRow as $dataCell) $totalSales += $dataCell;

			// Create the bar
			$colourIndex = $bar % count($chartColours);
			$pdf->SetFillColor($chartColours[$colourIndex][0], $chartColours[$colourIndex][1], $chartColours[$colourIndex][2]);
			$pdf->Rect($xPos, $chartYPos - ($totalSales / $yScale), $barWidth, $totalSales / $yScale, 'DF');
			$xPos += (1 / $xScale);
			$bar++;
		}

		$pdf->Ln(85);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6);
		$pdf->Cell(50, 6, '2. Tabulasi Data Kelompok Arah Peminatan', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'Tabel 1. Tabulasi Kelompok Peminatan', 0, 0, 'C');
		$pdf->Ln();

		$nama_bidang['SBD'] = array('SOSIAL BUDAYA');
		$nama_bidang['BSN'] = array('BISNIS');
		$nama_bidang['MIPA'] = array('MIPA');
		$nama_bidang['BHS'] = array('BAHASA DAN SASTRA');
		$nama_bidang['SENI'] = array('SENI');
		$nama_bidang['TEK'] = array('TEKNOLOGI');
		$nama_bidang['ORG'] = array('OLAHRAGA');
		$nama_bidang['AGA'] = array('KEAGAMAAN');
		$nama_bidang['MMP'] = array('MAKANAN-MINUMAN/GIZI DAN PARIWISATA');
		$nama_bidang['KDP'] = array('KESEHATAN DAN PENGOBATAN');
		$nama_bidang['NAT'] = array('USAHA NATURAL');
		$nama_bidang['KDK'] = array('KEMILITERAN DAN KEPOLISIAN');
		$nama_bidang['APD'] = array('ANALISIS PERILAKU DAN PENGEMBANGAN DIRI');

		$pdf->Cell(12);
		$pdf->Cell(90, 7, 'Bidang Peminatan', 1, 0, 'C');
		$pdf->Cell(40, 7, 'Skor Keseluruhan', 'R,B,T', 0, 'C');
		$pdf->Cell(36, 7, 'Skor Rata-Rata', 'R,B,T', 1, 'C');

		$pdf->SetWidths(array(90, 40, 36));
		// srand(microtime() * 1000000);
		$pdf->SetAligns(array('L', 'C', 'C'));

		$pdf->SetLeftMargin(22);
		$count_alphabet = 'A';
		foreach ($nama_bidang as $key => $value) {
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->MultiCell(166, 7, $count_alphabet++ . ". " . $value[0] . " (" . $key . ")", 'R,L', 'L');
			foreach ($bidang[$key] as $key2 => $value2) {
				$pdf->SetFont('Arial', '', 12);
				$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id'], 'kode_aspek' => $value2));
				$pdf->Row(array($get_aspek[0]['aspek'], (isset($total_skor[$get_aspek[0]['kode_aspek']]['skor'])) ? $total_skor[$get_aspek[0]['kode_aspek']]['skor'] . " (" . $total_skor[$get_aspek[0]['kode_aspek']]['persentase'] . " %)" : "0 (0%)", (isset($total_skor[$get_aspek[0]['kode_aspek']]['skor'])) ? $total_skor[$get_aspek[0]['kode_aspek']]['skor'] / ($total_skor[$get_aspek[0]['kode_aspek']]['total_butir'] / ($total_skor[$get_aspek[0]['kode_aspek']]['butir'] * 3)) : '-'));

				if ((isset($total_skor[$get_aspek[0]['kode_aspek']]['skor']))) {
					$tabulasi_peminatan[$get_aspek[0]['kode_aspek']] = $total_skor[$get_aspek[0]['kode_aspek']]['persentase'];
				}
			}
		}


		$pdf->Ln(3);
		$pdf->SetFont('Arial', 'BI', 12);
		$pdf->Cell(178, 6, 'CATATAN :', 0, 1, 'L');

		asort($tabulasi_peminatan);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(55, 6, 'Sub-Peminatan Terendah', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->MultiCell(106, 6, $total_skor[key($tabulasi_peminatan)]['aspek'] . " dengan skor " . $total_skor[key($tabulasi_peminatan)]['skor'] . " (" . $total_skor[key($tabulasi_peminatan)]['persentase'] . "%)", 0, 'L');

		arsort($tabulasi_peminatan);
		$pdf->Ln(5);
		$pdf->Cell(55, 6, 'Sub-Peminatan Tertinggi', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->MultiCell(106, 6, $total_skor[key($tabulasi_peminatan)]['aspek'] . " dengan skor " . $total_skor[key($tabulasi_peminatan)]['skor'] . " (" . $total_skor[key($tabulasi_peminatan)]['persentase'] . "%)", 0, 'L');

		$pdf->SetLeftMargin(10);
		$pdf->Ln(8);
		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(178, 6, 'Jakarta, ' . konversi_tanggal(date('Y-m-d')), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(178, 6, 'Pengolah Data', 0, 0, 'R');
		$pdf->Ln(20);
		$pdf->Cell(178, 6, getField('user_konselor', 'nama_lengkap', array('id' => $get_kelas[0]['konselor_id'])), 0, 0, 'R');

		$pdf->SetTitle('LAPORAN AUAP ' . $get_kelas[0]['kelas'] . '.pdf');

		$pdf->Output('I', 'LAPORAN AUAP ' . $get_kelas[0]['kelas'] . '.pdf', FALSE);
	}

	public function laporan_individu($id = "")
	{
		$this->load->library('fpdf_diag');
		$get_profil = $this->Main_model->get_where('instrumen_jawaban', array('id' => $id));
		$get_kelas = $this->Main_model->get_where('kelas', array('id' => $get_profil[0]['kelas']));
		$get_user = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$get_surat = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $get_user[0]['jenjang']));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id']));
		$jawaban = unserialize($get_profil[0]['jawaban']);

		if ($get_surat[0]['logo'] != 'other' || $get_surat[0]['logo'] != '') {
			$get_data_logo = $this->Main_model->get_where('logo_daerah', ['id' => $get_surat[0]['logo']]);
			if (!$get_data_logo) {
				$get_data_logo = '';
			}
		} else {
			$get_data_logo = '';
		}

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);

		$pdf->SetFont('Arial', '', 11);
		if (!empty($get_data_logo)) {
			$pdf->Image('./uploads/logo/' . $get_data_logo[0]['path'], 4, 10, 35, 27);
		} else if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image('./uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kiri'], 4, 10, 35, 27);
		} else {
			$pdf->Image('./assets/img/logo_iki.png', 8, 6, 30, 27);
		}

		if (@$get_surat[0]['logo_kanan']) {
			$pdf->Image(base_url('uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kanan']), 170, 10, 35, 27);
		}
		$pdf->Ln(1);
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_pertama']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 11);
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_kedua']), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(205, 6, strtoupper(@$get_surat[0]['baris_ketiga']), 0, 0, 'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln();
		}
		$pdf->SetFont('Arial', '', 10);
		$pdf->Cell(205, 6, @$get_surat[0]['baris_keempat'], 0, 0, 'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(4);
			$pdf->Cell(205, 6, @$get_surat[0]['baris_kelima'], 0, 0, 'C');
		}

		$pdf->Ln();
		$pdf->SetLineWidth(1);
		$pdf->Line(10, 38, 200, 38);
		$pdf->SetLineWidth(0);
		$pdf->Line(10, 39, 200, 39);
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

		// setting jenis font yang akan digunakan
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(35, 7, 'Rahasia', 1, 0, 'C');
		$pdf->Cell(3);
		$pdf->Ln(10);
		$pdf->Cell(185, 6, 'LAPORAN INDIVIDU', 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'ALAT UNGKAP - ARAH PEMINATAN SLTA', 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, getField('user_info', 'instansi', array('id' => $this->session->userdata('id'))), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'TAHUN AJARAN 2019/2020', 0, 0, 'C');

		$pdf->SetFont('Arial', '', 12);
		$pdf->Ln(10);
		$pdf->Cell(50, 6, 'Nama Siswa', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $get_profil[0]['nama_lengkap'], 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Jenis Kelamin', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, $get_profil[0]['jenis_kelamin'], 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(50, 6, 'Kelas', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, getField('kelas', 'kelas', array('id' => $get_profil[0]['kelas'])), 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Tanggal Pengisian', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, date('d-m-Y', strtotime($get_profil[0]['date_created'])), 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, 'A. DATA ARAH PEMINATAN', 0, 0, 'L');
		$pdf->Ln();

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(129, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, 'Keterangan :', 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->Cell(130, 7, '', 0, 0, 'L');
		$pdf->Cell(20, 7, 'Skor', 1, 0, 'L');
		$pdf->Cell(30, 7, 'Makna', 'R,B,T', 1, 'L');

		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(130, 7, '', 0, 0, 'L');
		$pdf->Cell(20, 7, '>85%', 1, 0, 'L');
		$pdf->Cell(30, 7, 'Baik Sekali', 'R,B,T', 1, 'L');

		$pdf->Cell(130, 7, '', 0, 0, 'L');
		$pdf->Cell(20, 7, '75-84%', 1, 0, 'L');
		$pdf->Cell(30, 7, 'Baik', 'R,B,T', 1, 'L');

		$pdf->Cell(130, 7, '', 0, 0, 'L');
		$pdf->Cell(20, 7, '50-74%', 1, 0, 'L');
		$pdf->Cell(30, 7, 'Sedang', 'R,B,T', 1, 'L');

		$pdf->Cell(130, 7, '', 0, 0, 'L');
		$pdf->Cell(20, 7, '30-49%', 1, 0, 'L');
		$pdf->Cell(30, 7, 'Kurang', 'R,B,T', 1, 'L');

		$pdf->Cell(130, 7, '', 0, 0, 'L');
		$pdf->Cell(20, 7, '<30%', 1, 0, 'L');
		$pdf->Cell(30, 7, 'Kurang Sekali', 'R,B,T', 1, 'L');

		$kalkulasi_auap = array();
		$i = 0;
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			$skor = 0;
			$skor_1 = 0;
			$skor_2 = 0;
			$skor_3 = 0;
			$skor_4 = 0;
			$skor_5 = 0;

			if ($value_aspek['aspek'] == 'Sosial Budaya') {
				$value_aspek['singkatan'] = 'SBD';
			} elseif ($value_aspek['aspek'] == 'Ekonomi, Keuangan, dan Manajemen Bisnis') {
				$value_aspek['singkatan'] = 'EKM';
			} elseif ($value_aspek['aspek'] == 'Matematika dan Ilmu Pengetahuan Alam') {
				$value_aspek['singkatan'] = 'MIPA';
			} elseif ($value_aspek['aspek'] == 'Bahasa dan Sastra Nasional Indonesia, dan Bahasa Daerah dan Bahasa Asing') {
				$value_aspek['singkatan'] = 'BHS';
			} elseif ($value_aspek['aspek'] == 'Seni Suara / Musik') {
				$value_aspek['singkatan'] = 'MSK';
			} elseif ($value_aspek['aspek'] == 'Seni Rupa / Lukis') {
				$value_aspek['singkatan'] = 'SRP';
			} elseif ($value_aspek['aspek'] == 'Seni Sastra dan Drama') {
				$value_aspek['singkatan'] = 'SSD';
			} elseif ($value_aspek['aspek'] == 'Seni Tari') {
				$value_aspek['singkatan'] = 'STR';
			} elseif ($value_aspek['aspek'] == 'Seni Kerajinan Tangan') {
				$value_aspek['singkatan'] = 'KRT';
			} elseif ($value_aspek['aspek'] == 'Seni Karawitan Wayang') {
				$value_aspek['singkatan'] = 'KRW';
			} elseif ($value_aspek['aspek'] == 'Teknik Elektro') {
				$value_aspek['singkatan'] = 'ELK';
			} elseif ($value_aspek['aspek'] == 'Teknik Bangunan') {
				$value_aspek['singkatan'] = 'TBN';
			} elseif ($value_aspek['aspek'] == 'Teknik Mesin') {
				$value_aspek['singkatan'] = 'TMN';
			} elseif ($value_aspek['aspek'] == 'Teknik Pertambangan / Eksplorasi') {
				$value_aspek['singkatan'] = 'TPN';
			} elseif ($value_aspek['aspek'] == 'Teknologi Komunikasi / Informasi') {
				$value_aspek['singkatan'] = 'TKI';
			} elseif ($value_aspek['aspek'] == 'Ahli Olahraga / Pendidikan Olahraga') {
				$value_aspek['singkatan'] = 'ORG';
			} elseif ($value_aspek['aspek'] == 'Ahli Agama / Pendidikan Agama') {
				$value_aspek['singkatan'] = 'AGM';
			} elseif ($value_aspek['aspek'] == 'Makanan-Minuman / Gizi') {
				$value_aspek['singkatan'] = 'MMG';
			} elseif ($value_aspek['aspek'] == 'Pariwisata dan Perhotelan') {
				$value_aspek['singkatan'] = 'PDP';
			} elseif ($value_aspek['aspek'] == 'Ilmu dan Keterampilan Kesehatan') {
				$value_aspek['singkatan'] = 'IKK';
			} elseif ($value_aspek['aspek'] == 'Farmasi / Pengobatan') {
				$value_aspek['singkatan'] = 'FRM';
			} elseif ($value_aspek['aspek'] == 'Usaha Pertanian') {
				$value_aspek['singkatan'] = 'PRT';
			} elseif ($value_aspek['aspek'] == 'Usaha Peternakan') {
				$value_aspek['singkatan'] = 'PTN';
			} elseif ($value_aspek['aspek'] == 'Usaha Perikanan') {
				$value_aspek['singkatan'] = 'IKN';
			} elseif ($value_aspek['aspek'] == 'Kemiliteran') {
				$value_aspek['singkatan'] = 'MLT';
			} elseif ($value_aspek['aspek'] == 'Kepolisian') {
				$value_aspek['singkatan'] = 'PLS';
			} elseif ($value_aspek['aspek'] == 'Psikologi') {
				$value_aspek['singkatan'] = 'PSI';
			} elseif ($value_aspek['aspek'] == 'Pendidikan') {
				$value_aspek['singkatan'] = 'PND';
			} elseif ($value_aspek['aspek'] == 'Bimbingan dan Konseling') {
				$value_aspek['singkatan'] = 'BK';
			}

			foreach ($get_butir as $key => $value) {
				$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];

				if (@$jawaban[$value['id']] == 'SD') {
					$skor_3++;
				} elseif (@$jawaban[$value['id']] == 'DS') {
					$skor_2++;
				} elseif (@$jawaban[$value['id']] == 'KD') {
					$skor_1++;
				} elseif (@$jawaban[$value['id']] == 'TD') {
					$skor_4++;
				} elseif (@$jawaban[$value['id']] == 'TPH') {
					$skor_5++;
				}
			}


			if ($skor_1 > 0 || $skor_2 > 0 || $skor_3 > 0 || $skor_4 > 0 || $skor_5 > 0) {
				$skor = ($skor_3 * 3) + ($skor_2 * 2) + $skor_1;
				$kalkulasi_auap[$i]['skor'] = $skor;
				$kalkulasi_auap[$i]['persentase'] = round(($skor / (3 * count($get_butir))) * 100, 2);
				$kalkulasi_auap[$i]['skor_1'] = $skor_1;
				$kalkulasi_auap[$i]['skor_2'] = $skor_2;
				$kalkulasi_auap[$i]['skor_3'] = $skor_3;
				$kalkulasi_auap[$i]['skor_4'] = $skor_4;
				$kalkulasi_auap[$i]['skor_5'] = $skor_5;
				$kalkulasi_auap[$i]['singkatan'] = $value_aspek['singkatan'];
				$kalkulasi_auap[$i]['aspek'] = $value_aspek['aspek'];
				$kalkulasi_auap[$i]['id'] = $value_aspek['id'];
				$kalkulasi_auap[$i]['total_butir'] = count($get_butir);
				$i++;
			}
		}

		$textColour = array(0, 0, 0);
		$rowLabels = array($kalkulasi_auap[0]['singkatan'], @$kalkulasi_auap[1]['singkatan'], @$kalkulasi_auap[2]['singkatan']);
		$chartXPos = -3;
		$chartYPos = 172;
		$chartWidth = 144;
		$chartHeight = 60;
		$chartYStep = 100;

		$chartColours = array(
			array(255, 100, 100),
			array(100, 255, 100),
			array(100, 100, 255),
			array(255, 255, 100),
		);

		$pdf->SetFont('Arial', '', 12);

		$data = array(
			array($kalkulasi_auap[0]['persentase']),
			array(@$kalkulasi_auap[1]['persentase']),
			array(@$kalkulasi_auap[2]['persentase']),
		);


		// Compute the X scale
		$xScale = count($rowLabels) / ($chartWidth - 40);

		// Compute the Y scale

		$maxTotal = 100;

		foreach ($data as $dataRow) {
			$totalSales = 0;
			foreach ($dataRow as $dataCell) $totalSales += $dataCell;
			$maxTotal = ($totalSales > $maxTotal) ? $totalSales : $maxTotal;
		}

		$yScale = $maxTotal / $chartHeight;

		// Compute the bar width
		$barWidth = (1 / $xScale) / 1.5;

		// Add the axes:

		$pdf->SetFont('Arial', '', 9);

		// X axis
		$pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + $chartWidth, $chartYPos);

		for ($i = 0; $i < count($rowLabels); $i++) {
			$pdf->SetXY($chartXPos + 40 +  $i / $xScale, $chartYPos);
			$pdf->Cell($barWidth, 10, $rowLabels[$i], 0, 0, 'C');
		}

		// Y axis
		$pdf->Line($chartXPos + 30, $chartYPos, $chartXPos + 30, $chartYPos - $chartHeight - 8);

		for ($i = 0; $i <= $maxTotal; $i++) {
			if ($i % 10 == 0) {
				$pdf->SetXY($chartXPos + 7, $chartYPos - 5 - $i / $yScale);
				$pdf->Cell(20, 10, $i, 0, 1, 'R');
				$pdf->Line($chartXPos + 28, $chartYPos - $i / $yScale, $chartXPos + 30, $chartYPos - $i / $yScale);
			}
		}

		// Create the bars
		$xPos = $chartXPos + 40;
		$bar = 0;

		foreach ($data as $dataRow) {

			// Total up the sales figures for this product
			$totalSales = 0;
			foreach ($dataRow as $dataCell) $totalSales += $dataCell;

			// Create the bar
			$colourIndex = $bar % count($chartColours);
			$pdf->SetFillColor($chartColours[$colourIndex][0], $chartColours[$colourIndex][1], $chartColours[$colourIndex][2]);
			$pdf->Rect($xPos, $chartYPos - ($totalSales / $yScale), $barWidth, $totalSales / $yScale, 'DF');
			$xPos += (1 / $xScale);
			$bar++;
		}

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Ln(66);
		$pdf->Cell(185, 6, 'Tabel 1. Data Pilihan Sub Bidang Peminatan Siswa', 0, 0, 'C');
		$pdf->Ln(8);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(10, 14, 'No', 'L,R,T', 0, 'C');
		$pdf->Cell(50, 7, 'Pilihan Sub-Bidang', 'R,T', 0, 'C');
		$pdf->Cell(20, 14, 'Jumlah', 'R,T', 0, 'C');
		$pdf->Cell(60, 7, 'Jawaban', 'R,T', 0, 'C');
		$pdf->Cell(35, 7, 'Kualifikasi', 'R,T', 1, 'C');

		$pdf->Cell(16, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 7, 'Arah Peminatan', 'R,T', 0, 'C');
		$pdf->Cell(20, 7, '', 0, 0, 'L');
		$pdf->Cell(12, 7, 'SD', 'R,T', 0, 'C');
		$pdf->Cell(12, 7, 'DS', 'R,T', 0, 'C');
		$pdf->Cell(12, 7, 'KS', 'R,T', 0, 'C');
		$pdf->Cell(12, 7, 'TD', 'R,T', 0, 'C');
		$pdf->Cell(12, 7, 'TPH', 'R,T', 0, 'C');
		$pdf->Cell(15, 7, 'Skor', 'R,T', 0, 'C');
		$pdf->Cell(20, 7, 'Persen', 'R,T', 1, 'C');


		$pdf->SetFont('Arial', '', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(10, 7, '1', 'L,R,T', 0, 'C');
		$pdf->Cell(50, 7, $kalkulasi_auap[0]['singkatan'], 'R,T', 0, 'C');
		$pdf->Cell(20, 7, $kalkulasi_auap[0]['total_butir'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, $kalkulasi_auap[0]['skor_3'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, $kalkulasi_auap[0]['skor_2'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, $kalkulasi_auap[0]['skor_1'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, $kalkulasi_auap[0]['skor_4'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, $kalkulasi_auap[0]['skor_5'], 'R,T', 0, 'C');
		$pdf->Cell(15, 7, $kalkulasi_auap[0]['skor'], 'R,T', 0, 'C');
		$pdf->Cell(20, 7, $kalkulasi_auap[0]['persentase'], 'R,T', 1, 'C');

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(10, 7, '2', 'L,R,T', 0, 'C');
		$pdf->Cell(50, 7, @$kalkulasi_auap[1]['singkatan'], 'R,T', 0, 'C');
		$pdf->Cell(20, 7, @$kalkulasi_auap[1]['total_butir'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[1]['skor_3'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[1]['skor_2'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[1]['skor_1'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[1]['skor_4'], 'R,T', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[1]['skor_5'], 'R,T', 0, 'C');
		$pdf->Cell(15, 7, @$kalkulasi_auap[1]['skor'], 'R,T', 0, 'C');
		$pdf->Cell(20, 7, @$kalkulasi_auap[1]['persentase'], 'R,T', 1, 'C');

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(10, 7, '3', 'L,R,T,B', 0, 'C');
		$pdf->Cell(50, 7, @$kalkulasi_auap[2]['singkatan'], 'R,B,T', 0, 'C');
		$pdf->Cell(20, 7, @$kalkulasi_auap[2]['total_butir'], 'R,T,B', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[2]['skor_3'], 'R,T,B', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[2]['skor_2'], 'R,T,B', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[2]['skor_1'], 'R,T,B', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[2]['skor_4'], 'R,T,B', 0, 'C');
		$pdf->Cell(12, 7, @$kalkulasi_auap[2]['skor_5'], 'R,T,B', 0, 'C');
		$pdf->Cell(15, 7, @$kalkulasi_auap[2]['skor'], 'R,T,B', 0, 'C');
		$pdf->Cell(20, 7, @$kalkulasi_auap[2]['persentase'], 'R,T,B', 1, 'C');

		$pdf->SetFont('Arial', 'I', 8);
		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(180, 5, '*SD (Sangat Disukai) = 3, DS (Disukai) = 2, KD (Kurang Disukai) = 1, TD (Tidak Disukai) = 0, TP (Tidak Dipahami) = 0', 0, 1, 'L');

		$pdf->Cell(6, 4, '', 0, 0, 'L');
		$pdf->Cell(180, 4, '*Persentase = Jumlah Skor/Skor Ideal (3xJumlah Item) x 100', 0, 1, 'L');

		$pdf->SetFont('Arial', '', 12);

		$pdf->Ln();
		$pdf->Cell(183, 6, 'Jakarta, ' . date('d F Y'), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(183, 6, 'Pengolah Data', 0, 0, 'R');
		$pdf->Ln(25);
		$pdf->Cell(183, 6, getField('user_konselor', 'nama_lengkap', array('id' => $get_kelas[0]['konselor_id'])), 0, 0, 'R');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

		$pdf->Cell(6, 4, '', 0, 0, 'L');
		$pdf->Cell(185, 6, 'Lampiran', 0, 1, 'L');

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(185, 6, 'Tabulasi Jawaban Peserta Didik', 0, 1, 'C');

		$pdf->Ln(8);

		$pdf->SetFont('Arial', '', 12);

		$pdf->Cell(6, 4, '', 0, 0, 'L');
		$pdf->Cell(57.5, 6, 'Pilihan 1', 1, 0, 'C');
		$pdf->Cell(57.5, 6, 'Pilihan 2', 1, 0, 'C');
		$pdf->Cell(57.5, 6, 'Pilihan 3', 1, 1, 'C');

		$pdf->SetWidths(array(57.5, 57.5, 57.5));
		// srand(microtime() * 1000000);

		$pdf->Cell(6, 4, '', 0, 0, 'L');
		$pdf->SetAligns(array('C', 'C', 'C'));
		$pdf->Row(array($kalkulasi_auap[0]['singkatan'], @$kalkulasi_auap[1]['singkatan'], @$kalkulasi_auap[2]['singkatan']));

		$pdf->Cell(6, 4, '', 0, 0, 'L');
		$pdf->SetAligns(array('C', 'C', 'C'));
		$pdf->Row(array($kalkulasi_auap[0]['aspek'], @$kalkulasi_auap[1]['aspek'], @$kalkulasi_auap[2]['aspek']));

		$pdf->SetFont('Arial', '', 10);
		$pdf->SetWidths(array(10, 23.75, 23.75, 10, 23.75, 23.75, 10, 23.75, 23.75));
		// srand(microtime() * 1000000);

		$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('No', 'Jawaban', 'OPS', 'No', 'Jawaban', 'OPS', 'No', 'Jawaban', 'OPS'));

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (isset($jawaban[$value['id']])) {
					if ($jawaban[$value['id']] == 'SD') {
						$skor = 3;
					} elseif ($jawaban[$value['id']] == 'DS') {
						$skor = 2;
					} elseif ($jawaban[$value['id']] == 'KD') {
						$skor = 1;
					} else {
						$skor = 0;
					}
					$array_lampiran[$value_aspek['id']][$value['kode_pernyataan']]['jawaban'] = $jawaban[$value['id']];
					$array_lampiran[$value_aspek['id']][$value['kode_pernyataan']]['skor'] = $skor;
				}
			}
			$count_all_lampiran[$value_aspek['id']] = count($get_butir);
		}

		$count_array_lampiran = array($count_all_lampiran[$kalkulasi_auap[0]['id']], @$count_all_lampiran[$kalkulasi_auap[1]['id']], @$count_all_lampiran[$kalkulasi_auap[2]['id']]);

		$pdf->SetLeftMargin(16);
		$pdf->SetFont('Arial', '', 10);
		$pdf->tablewidths = array(10, 23.75, 23.75, 10, 23.75, 23.75, 10, 23.75, 23.75);
		for ($i = 1; $i <= max($count_array_lampiran); $i++) {
			$test[] = array($i, @$array_lampiran[$kalkulasi_auap[0]['id']][$i]['jawaban'], @$array_lampiran[$kalkulasi_auap[0]['id']][$i]['skor'], $i, @$array_lampiran[$kalkulasi_auap[1]['id']][$i]['jawaban'], @$array_lampiran[$kalkulasi_auap[1]['id']][$i]['skor'], $i, @$array_lampiran[$kalkulasi_auap[2]['id']][$i]['jawaban'], @$array_lampiran[$kalkulasi_auap[2]['id']][$i]['skor']);
		}
		$pdf->morepagestable($test);

		$pdf->Cell(33.75, 6, 'Total', 'L,R,T', 0, 'C');
		$pdf->Cell(23.75, 6, 'Persentase', 'R,T', 0, 'C');

		$pdf->Cell(33.75, 6, 'Total', 'R,T', 0, 'C');
		$pdf->Cell(23.75, 6, 'Persentase', 'R,T', 0, 'C');

		$pdf->Cell(33.75, 6, 'Total', 'R,T', 0, 'C');
		$pdf->Cell(23.75, 6, 'Persentase', 'R,T', 1, 'C');

		$pdf->Cell(33.75, 6, $kalkulasi_auap[0]['skor'], 1, 0, 'C');
		$pdf->Cell(23.75, 6, $kalkulasi_auap[0]['persentase'] . "%", 'R,T,B', 0, 'C');

		$pdf->Cell(33.75, 6, @$kalkulasi_auap[1]['skor'], 'R,T,B', 0, 'C');
		$pdf->Cell(23.75, 6, @$kalkulasi_auap[1]['persentase'] . "%", 'R,T,B', 0, 'C');

		$pdf->Cell(33.75, 6, @$kalkulasi_auap[2]['skor'], 'R,T,B', 0, 'C');
		$pdf->Cell(23.75, 6, @$kalkulasi_auap[2]['persentase'] . "%", 'R,T,B', 1, 'C');

		$pdf->SetTitle('LAPORAN AUAP ' . getField('kelas', 'kelas', array('id' => $get_profil[0]['kelas'])) . ' - ' . $get_profil[0]['nama_lengkap'] . '.pdf');

		$pdf->Output('I', 'LAPORAN AUAP ' . getField('kelas', 'kelas', array('id' => $get_profil[0]['kelas'])) . ' - ' . $get_profil[0]['nama_lengkap'] . '.pdf', FALSE);
	}

	public function kode($jenjang = "")
	{
		$getProfil = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		if ($getProfil[0]['jenjang'] == 'SMA') {
			$data['jenjang'] = 1;
		} elseif ($getProfil[0]['jenjang'] == 'SMP') {
			$data['jenjang'] = 2;
		} elseif ($getProfil[0]['jenjang'] == 'SD') {
			$data['jenjang'] = 3;
		} else {
			$data['jenjang'] = $jenjang;
		}

		if ($jenjang) {
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $jenjang));
		} else {
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen', array('nickname' => 'AUAP', 'jenjang' => $getProfil[0]['jenjang']));
		}

		$data['get_aum'] = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $data['get_instrumen'][0]['id']));

		$data['content'] = 'auap_kode.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function kode_save()
	{
		$post = $this->input->post();
		$get_aum = $this->Main_model->get_where('user_instrumen', array('instrumen_id' => $post['instrumen_id'], 'user_id' => $this->session->userdata('id')));
		$checkKode = $this->Main_model->get_where('user_instrumen', array('kode_singkat' => $post['kode_singkat'], 'instrumen_id !=' => $post['instrumen_id']));
		$getProfil = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$jenjang = $post['jenjang'];
		unset($post['jenjang']);
		if ($checkKode) {
			$this->session->set_flashdata('error', 'Kode telah digunakan. Silahkan coba kode lain.');
			redirect('auap/kode');
		} else {
			if ($get_aum) {
				$this->Main_model->update_data('user_instrumen', $post, array('id' => $get_aum[0]['id']));
			} else {
				$this->Main_model->insert_data('user_instrumen', $post);
			}
		}

		if ($getProfil[0]['jenjang'] == 'Konselor') {
			redirect('auap/index/' . $jenjang);
		} else {
			redirect('auap');
		}
	}
}

/* End of file auap.php */
/* Location: ./application/views/auap.php */
