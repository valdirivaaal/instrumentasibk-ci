<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ptsdl extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	public function index($jenjang = "")
	{
		// $this->load->library('encrypt');
		$this->load->library('encryption');
		$data['get_profil'] = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));

		if ($jenjang) {
			$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $jenjang));
		} else {
			$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $data['get_profil'][0]['jenjang']));
		}

		$data['get_kelompok'] = $this->Main_model->get_where('kelompok', array('user_id' => $this->session->userdata('id')));

		$data['get_aum'] = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => @$get_instrumen[0]['id']));

		$data['get_kode'] = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => @$data['get_aum'][0]['instrumen_id']));

		$data['jenjang'] =  $jenjang;

		$get_ticket = $this->Main_model->get_where('ticket', array('user_id' => $this->session->userdata('id')));

		if ($get_ticket) {
			if ($jenjang) {
				$data['kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $this->session->userdata('id'), 'jenjang' => $jenjang), 'kelas', 'asc');
			} else {
				$data['kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $this->session->userdata('id')), 'kelas', 'asc');
			}
			$data['content'] = 'aum_ptsdl.php';
		} else {
			$data['content'] = 'key';
		}


		$this->load->view('main.php', $data, FALSE);
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
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $jenjang));
		} else {
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $getProfil[0]['jenjang']));
		}

		$data['get_aum'] = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $data['get_instrumen'][0]['id']));

		$data['content'] = 'ptsdl_kode.php';

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
			redirect('ptsdl/kode');
		} else {
			if ($get_aum) {
				$this->Main_model->update_data('user_instrumen', $post, array('id' => $get_aum[0]['id']));
			} else {
				$this->Main_model->insert_data('user_instrumen', $post);
			}
		}

		if ($getProfil[0]['jenjang'] == 'Konselor') {
			redirect('ptsdl/index/' . $jenjang);
		} else {
			redirect('ptsdl');
		}
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

		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $data['get_profil'][0]['jenjang']));
		$get_aum = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $get_instrumen[0]['id']));
		$data['get_jawaban'] = $this->Main_model->get_where('instrumen_jawaban', array('instrumen_id' => $get_aum[0]['id'], 'kelas' => $id));
		$data['content'] = 'aum_ptsdl_detail.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function laporan_kelompok($id = "")
	{
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $get_user[0]['jenjang']));
		$get_kode = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $get_instrumen[0]['id']));
		$get_kelompok = $this->Main_model->get_where('kelompok', array('id' => $id));

		$get_data = $this->Main_model->get_where_in('instrumen_jawaban', 'kelas', explode(",", $get_kelompok[0]['kelas']), array('instrumen_id' => $get_kode[0]['id']));
		$get_surat = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$get_kelas = $this->Main_model->get_where_in('kelas', 'id', explode(",", $get_kelompok[0]['kelas']));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id']));

		$total_peserta = 0;
		foreach ($get_kelas as $key => $value) {
			$total_peserta += $value['jumlah_siswa'];
		}

		foreach ($get_data as $key => $value) {
			$jawaban[] = unserialize($value['jawaban']);
			$jawaban_berat = unserialize($value['jawaban_berat']);
		}

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);

		$pdf->SetFont('Arial', '', 11);
		if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image(base_url('uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kiri']), 4, 10, 35, 27);
		} else {
			$pdf->Image(base_url('assets/img/logo_iki.png'), 8, 6, 30, 27);
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
		$pdf->Cell(185, 6, 'INSTRUMEN ALAT UNGKAP MASALAH - PTSDL ' . $get_user[0]['jenjang'], 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, strtoupper(getField('user_info', 'instansi', array('id' => $this->session->userdata('id')))), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'TAHUN AJARAN 2020/2021', 0, 0, 'C');

		$pdf->SetFont('Arial', '', 12);
		$pdf->Ln(10);
		$pdf->Cell(50, 6, 'Identitas Kelas/Kelompok', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $get_kelompok[0]['nama_kelompok'], 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Tanggal Pengadministrasian', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, date('d/m/Y'), 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(50, 6, 'Jumlah Peserta', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(30, 6, $total_peserta, 0, 0, 'L');
		$pdf->Cell(20);
		$pdf->Cell(56, 6, 'Jumlah Responden', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, count($get_data), 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, 'A. DATA DASAR PERMASALAHAN YANG DIALAMI KELOMPOK', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '1. Grafik Permasalahan Tiap Bidang', 0, 0, 'L');

		$sumArray = array();
		$sumArrayMasalah = array();
		$count_j = 0;
		foreach ($jawaban as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				if ($get_butir[0]['jenis_pernyataan'] == 1) {
					if ($value2 == 'L') {
						$value2 = 2;
					} elseif ($value2 == 'U') {
						$value2 = 1;
					} elseif ($value2 == 'SR') {
						$value2 = 'SR';
					} elseif ($value2 == 'J' || $value2 == 'K') {
						$value2 = 'Masalah';
					}
				} elseif ($get_butir[0]['jenis_pernyataan'] == 2) {
					if ($value2 == 'J') {
						$value2 = 2;
					} elseif ($value2 == 'K') {
						$value2 = 1;
					} elseif ($value2 == 'SR') {
						$value2 = 'SR';
					} elseif ($value2 == 'U' || $value2 == 'L') {
						$value2 = 'Masalah';
					}
				}

				$array_pernyataan[$key2] = $value2;
			}
			$array_jawaban[$count_j++] = $array_pernyataan;
		}

		foreach ($array_jawaban as $k => $subArray) {

			foreach ($subArray as $id => $value) {
				if ($value == 'Masalah') {
					$value = 1;
					@$sumArrayMasalah[$id] += $value;
				} else {
					@$sumArray[$id] += $value;
				}
			}
		}

		$kalkulasi_ptsdl = array();
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (@$sumArray[$value['id']]) {
					$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor'][] = $sumArray[$value['id']];
				}

				if (@$sumArrayMasalah[$value['id']]) {
					$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah'][] = $sumArrayMasalah[$value['id']];
				}
			}
			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir'][] = count($get_butir) * count($get_data);
			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir_skor'][] = (count($get_butir) * 2) * count($get_data);
			$skor[$value_aspek['kode_aspek']] = (array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor']) / $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir_skor'][0]) * 100;
			$masalah[$value_aspek['kode_aspek']] = (array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah']) / $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir'][0]) * 100;
		}


		$rowLabels = array("P", "T", "S", "D", "L");

		$data[0] = array($skor['P'], $masalah['P']);
		$data[1] = array($skor['T'], $masalah['T']);
		$data[2] = array($skor['S'], $masalah['S']);
		$data[3] = array($skor['D'], $masalah['D']);
		$data[4] = array($skor['L'], $masalah['L']);

		$chartXPos = 0;
		$chartYPos = 195;
		$chartWidth = 170;
		$chartHeight = 80;
		$chartYStep = 100;

		$pdf->Ln(10);
		$pdf->Cell(10, 6, '', 0, 0, 'C');


		$xScale = count($rowLabels) / ($chartWidth - 40);
		$maxTotal = 100;
		$yScale = $maxTotal / $chartHeight;
		$barWidth = (1 / $xScale) / 1.5;

		$pdf->ColumnChart($chartWidth, $chartHeight, $data, null, array(255, 175, 100));

		for ($i = 0; $i < count($rowLabels); $i++) {
			$pdf->SetXY($chartXPos + 50 +  $i / $xScale, $chartYPos);
			$pdf->Cell(10, 0, $rowLabels[$i], 0, 0, 'C');
		}

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Ln(5);
		$pdf->Cell(10);
		$pdf->Cell(50, 6, 'Penafsiran :', 0, 1, 'L');
		$pdf->Cell(185, 6, 'Tabel 1. Penafsiran Persentase', 0, 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Skor Kondisi Kegiatan Belajar', 1, 0, 'C');
		$pdf->Cell(30, 7, 'Persentase', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Kondisi Permasalahan', 'R,B,T', 1, 'C');

		$pdf->SetFont('Arial', '', 12);

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Sangat Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '81-100%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Sangat Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '61-80%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Kurang Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '41-60%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Cukup Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Tidak Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '21-40%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Sangat Memerlukan Perhatian', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Sangat Tidak Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '0-20%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Memerlukan Perhatian', 'R,B,T', 1, 'C');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '2. Tabel Rekapitulasi Komponen PTSDL', 0, 0, 'L');
		$pdf->Ln(5);
		$pdf->Cell(185, 6, 'Tabel 2. Rekapitulasi Komponen PTSDL', 0, 0, 'C');
		$pdf->Ln();

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(25, 7, 'Kategori', 'L,R,T', 0, 'C');
		$pdf->Cell(40, 7, 'Skor', 'R,B,T', 0, 'C');
		$pdf->Cell(107, 7, 'Masalah', 'L,R,T', 1, 'C');

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(25, 7, 'Item', 'L,R,B', 0, 'C');
		$pdf->Cell(20, 7, 'Jml', 'R,B,T', 0, 'C');
		$pdf->Cell(20, 7, '%', 'R,B,T', 0, 'C');
		$pdf->Cell(71, 7, 'No Item Masalah', 'R,B,T', 0, 'C');
		$pdf->Cell(18, 7, 'Jml', 'R,B,T', 0, 'C');
		$pdf->Cell(18, 7, '%', 'R,B,T', 1, 'C');

		$pdf->SetFont('Arial', '', 12);
		$pdf->SetWidths(array(25, 20, 20, 71, 18, 18));
		$pdf->SetAligns(array('C', 'C', 'C', 'L', 'C', 'C'));
		srand(microtime() * 1000000);

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			$array_skor = array();
			$array_masalah = array();
			foreach ($get_butir as $key => $value) {
				if (@$sumArray[$value['id']]) {
					$array_skor[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}

				if (@$sumArrayMasalah[$value['id']]) {
					$array_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}
			}


			$pdf->Cell(6, 7, '', 0, 0, 'L');
			$pdf->Row(array($value_aspek['kode_aspek'], array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor']), round($skor[$value_aspek['kode_aspek']], 2), ($array_masalah) ? implode(",", $array_masalah[$value_aspek['kode_aspek']]) : '-', array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah']), round($masalah[$value_aspek['kode_aspek']], 2)));
		}

		$total_skor_mentah = array_sum($kalkulasi_ptsdl['P']['skor']) + array_sum($kalkulasi_ptsdl['T']['skor']) + array_sum($kalkulasi_ptsdl['S']['skor']) + array_sum($kalkulasi_ptsdl['D']['skor']) + array_sum($kalkulasi_ptsdl['L']['skor']);

		$total_skor = ($skor['P'] + $skor['T'] + $skor['S'] + $skor['D'] + $skor['L']) / 5;

		$total_masalah_mentah = array_sum($kalkulasi_ptsdl['P']['masalah']) + array_sum($kalkulasi_ptsdl['T']['masalah']) + array_sum($kalkulasi_ptsdl['S']['masalah']) + array_sum($kalkulasi_ptsdl['D']['masalah']) + array_sum($kalkulasi_ptsdl['L']['masalah']);

		$total_masalah = ($masalah['P'] + $masalah['T'] + $masalah['S'] + $masalah['D'] + $masalah['L']['masalah']) / 5;

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah', round($total_skor_mentah, 2), round($total_skor, 2), '-', $total_masalah_mentah, round($total_masalah, 2)));

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, 'B. SKOR DAN MASALAH ITEM PTSDL', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '1. Skor Item PTSDL Tertinggi Dalam Kelompok', 0, 0, 'L');
		$pdf->Ln(8);
		$pdf->Cell(185, 6, 'Tabel 3. Skor Item PTSDL Tertinggi Dalam Kelompok', 0, 0, 'C');
		$pdf->Ln(8);
		$pdf->SetWidths(array(20, 90, 30, 30));
		srand(microtime() * 1000000);

		arsort($sumArray);

		$pdf->SetAligns(array('C', 'C', 'C', 'C'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('No Item', 'Pernyataan', 'Bidang', 'Total'), 6);
		$pdf->SetFont('Arial', '', 12);

		$pdf->SetLeftMargin(16);
		$counter_masalah = 1;
		foreach ($sumArray as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan', '*', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_pernyataan.id' => $key));
			$height1 = $pdf->GetY();
			if ($counter_masalah < 11) {
				$pdf->SetAligns(array('C', 'L', 'C', 'C'));
				$pdf->Row(array($counter_masalah, $get_instrumen[0]['pernyataan'], $get_instrumen[0]['kode_aspek'], $sumArray[$key]), 6);
			}


			$counter_masalah++;
		}

		$pdf->SetLeftMargin(10);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Ln();
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '2. Item Masalah PTSDL Tertinggi Dalam Kelompok', 0, 0, 'L');
		$pdf->Ln(8);
		$pdf->Cell(185, 6, 'Tabel 4. Item Masalah PTSDL Tertinggi Dalam Kelompok', 0, 0, 'C');
		$pdf->Ln(8);
		$pdf->SetWidths(array(20, 90, 30, 30));
		srand(microtime() * 1000000);

		arsort($sumArrayMasalah);

		$pdf->SetAligns(array('C', 'C', 'C', 'C'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('No Item', 'Pernyataan', 'Bidang', 'Total'));
		$pdf->SetFont('Arial', '', 12);

		$pdf->SetLeftMargin(16);
		$counter_masalah = 1;
		foreach ($sumArrayMasalah as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan', '*', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_pernyataan.id' => $key));
			if ($counter_masalah < 11) {
				$pdf->SetAligns(array('C', 'L', 'C', 'C'));
				$pdf->Row(array($counter_masalah, $get_instrumen[0]['pernyataan'], $get_instrumen[0]['kode_aspek'], $sumArrayMasalah[$key]), 6);
			}


			$counter_masalah++;
		}

		$pdf->SetLeftMargin(10);

		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '2. Statistika Kelompok Skor Item Komponen PTSDL', 0, 0, 'L');
		$pdf->Ln(8);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetWidths(array(70, 100));
		srand(microtime() * 1000000);
		$pdf->SetAligns(array('L', 'L'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');

		if ($total_skor <= 20) {
			$kategori = 'Sangat Tidak Baik';
		} elseif ($total_skor <= 40) {
			$kategori = 'Tidak Baik';
		} elseif ($total_skor <= 60) {
			$kategori = 'Kurang Baik';
		} elseif ($total_skor <= 80) {
			$kategori = 'Baik';
		} else {
			$kategori = 'Sangat Baik';
		}

		foreach ($array_jawaban as $key => $value) {
			$jawaban_individu[] = array_sum($value);
			$count_jawaban[] = array_count_values($value);
		}

		foreach ($count_jawaban as $key => $value) {
			$count_masalah[] = $value['Masalah'];
		}

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Persentase', ''));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Kategori', $kategori));

		asort($sumArray);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Skor Terendah', reset($sumArray)));

		arsort($sumArray);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Skor Tertinggi', reset($sumArray)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Rata-Rata Jumlah Skor', $total_skor_mentah / count($get_data)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Modus', implode(",", modes($jawaban_individu))));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Median', calculate_median($jawaban_individu)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Standar Deviasi', standar_deviation($jawaban_individu)));

		arsort($jawaban_individu);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Skor Item Paling Banyak', reset($jawaban_individu)));

		asort($jawaban_individu);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Skor Item Paling Sedikit', reset($jawaban_individu)));

		$pdf->Ln();

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '2. Statistika Kelompok Item Masalah Komponen PTSDL', 0, 0, 'L');
		$pdf->Ln(8);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetWidths(array(70, 100));
		srand(microtime() * 1000000);
		$pdf->SetAligns(array('L', 'L'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');

		if ($total_masalah <= 20) {
			$kategori = 'Memerlukan Perhatian';
		} elseif ($total_skor <= 40) {
			$kategori = 'Sangat Memerlukan Perhatian';
		} elseif ($total_skor <= 60) {
			$kategori = 'Cukup Tinggi';
		} elseif ($total_skor <= 80) {
			$kategori = 'Tinggia';
		} else {
			$kategori = 'Sangat Tinggi';
		}

		$pdf->Row(array('Kategori', $kategori));

		asort($sumArrayMasalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Masalah Terendah', reset($sumArrayMasalah)));

		arsort($sumArrayMasalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Masalah Tertinggi', reset($sumArrayMasalah)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Rata-Rata Jumlah Masalah', $total_masalah_mentah / count($get_data)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Mode', implode(",", modes($count_masalah))));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Median', calculate_median($count_masalah)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Standar Deviasi', standar_deviation($count_masalah)));

		arsort($count_masalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Item Masalah Paling Banyak', reset($count_masalah)));

		asort($count_masalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Item Masalah Paling Sedikit', reset($count_masalah)));
		$pdf->SetFont('Arial', '', 12);
		$pdf->Ln(8);
		$pdf->Cell(178, 6, 'Jakarta, ' . konversi_tanggal(date('Y-m-d')), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(178, 6, 'Pengolah Data', 0, 0, 'R');
		$pdf->Ln(20);
		$pdf->Cell(178, 6, getField('user_konselor', 'nama_lengkap', array('id' => $get_kelas[0]['konselor_id'])), 0, 0, 'R');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(6, 5, 'Lampiran', 0, 1, 'L');
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(185, 6, 'Rekapitulasi Jawaban Peserta Didik', 0, 1, 'C');
		$pdf->Ln(5);

		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, 'Komponen', 'L,R,T', 0, 'C');
		$pdf->Cell(10, 10, 'No', 'R,B,T', 0, 'C');
		$pdf->Cell(20, 5, 'Jenis Item', 'T,B', 0, 'C');
		$pdf->Cell(30, 5, 'Jawaban', 1, 0, 'C');
		$pdf->Cell(36, 5, 'Skor', 'L,R,T', 0, 'C');
		$pdf->Cell(26, 5, 'Masalah', 'L,R,T', 0, 'C');
		$pdf->Cell(25, 10, 'Ket', 1, 0, 'C');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, 'Item', 'L,B,R', 0, 'C');
		$pdf->Cell(10, 5, '', 0, 0, 'L');
		$pdf->Cell(10, 5, 'P', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, 'N', 'B', 0, 'C');
		$pdf->Cell(6, 5, 'J', 'L,R,B', 0, 'C');
		$pdf->Cell(6, 5, 'K', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, 'SR', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, 'U', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, 'L', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '0', 'T,R,B', 0, 'C');
		$pdf->Cell(12, 5, '1', 'T,R,B', 0, 'C');
		$pdf->Cell(12, 5, '2', 'T,R,B', 0, 'C');
		$pdf->Cell(13, 5, 'Ya', 'T,R,B', 0, 'C');
		$pdf->Cell(13, 5, 'Bukan', 'T,B', 0, 'C');
		$pdf->Cell(25, 5, '', 0, 0, 'L');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, '1', 'L,B,R', 0, 'C');
		$pdf->Cell(10, 5, '2', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, '3', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, '4', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '5', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '6', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '7', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '8', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '9', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '10', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '11', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '12', 'R,B', 0, 'C');
		$pdf->Cell(13, 5, '13', 'R,B', 0, 'C');
		$pdf->Cell(13, 5, '14', 'R,B', 0, 'C');
		$pdf->Cell(25, 5, '15', 'R,B', 0, 'C');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->SetFont('Arial', '', 8);
		$pdf->SetWidths(array(25, 10, 10, 10, 6, 6, 6, 6, 6, 12, 12, 12, 13, 13, 25));
		$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
		srand(microtime() * 1000000);

		foreach ($array_jawaban as $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				foreach ($get_butir as $key_butir => $value_butir) {
					$array_convert_jawaban[$key2][] = $value[$key2];
				}
			}
		}

		foreach ($jawaban as $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				foreach ($get_butir as $key_butir => $value_butir) {
					$array_hitung_jawaban[$key2][] = $value[$key2];
				}
			}
		}

		$pdf->SetLeftMargin(16);
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			$butir_positif = 1;
			$butir_negatif = 1;
			$butir_j = 0;
			$butir_k = 0;
			$butir_sr = 0;
			$butir_u = 0;
			$butir_l = 0;
			$skor_1 = 0;
			$skor_2 = 0;
			$butir_masalah = 0;

			foreach ($get_butir as $key => $value) {
				$total_butir_isi = array_count_values($array_hitung_jawaban[$value['id']]);
				$butir_convert_isi = array_count_values($array_convert_jawaban[$value['id']]);
				$pdf->Row(array($value_aspek['kode_aspek'], $value['kode_pernyataan'], $value['jenis_pernyataan'] == 1 ? 'V' : '', $value['jenis_pernyataan'] == 2 ? 'V' : '', @$total_butir_isi['J'], @$total_butir_isi['K'], @$total_butir_isi['SR'], @$total_butir_isi['U'], @$total_butir_isi['L'], @$butir_convert_isi['SR'], @$butir_convert_isi['1'], @$butir_convert_isi['2'], @$butir_convert_isi['Masalah'], ((count($jawaban) - @$butir_convert_isi['Masalah']) - @$total_butir_isi['SR']) != 0 ? (count($jawaban) - @$butir_convert_isi['Masalah']) - @$total_butir_isi['SR'] : '', ''));
				if ($value['jenis_pernyataan'] == 1) {
					$skor_total[$value_aspek['aspek']]['butir_positif'] = $butir_positif++;
				} elseif ($value['jenis_pernyataan'] == 2) {
					$skor_total[$value_aspek['aspek']]['butir_negatif'] = $butir_negatif++;
				}

				$butir_j += @$total_butir_isi['J'];
				$butir_k += @$total_butir_isi[''];
				$butir_sr += @$total_butir_isi['SR'];
				$butir_u += @$total_butir_isi['U'];
				$butir_l += @$total_butir_isi['L'];
				$skor_1 += @$butir_convert_isi['1'];
				$skor_2 += @$butir_convert_isi['2'];
				$butir_masalah += @$butir_convert_isi['Masalah'];
				$butir_tidak_masalah = (count($get_butir) * count($jawaban)) - $butir_sr - $butir_masalah;

				$skor_total[$value_aspek['aspek']]['j'] = $butir_j;
				$skor_total[$value_aspek['aspek']]['k'] = $butir_k;
				$skor_total[$value_aspek['aspek']]['sr'] = $butir_sr;
				$skor_total[$value_aspek['aspek']]['u'] = $butir_u;
				$skor_total[$value_aspek['aspek']]['l'] = $butir_l;
				$skor_total[$value_aspek['aspek']]['skor_1'] = $skor_1;
				$skor_total[$value_aspek['aspek']]['skor_2'] = $skor_2;
				$skor_total[$value_aspek['aspek']]['butir_masalah'] = $butir_masalah;
				$skor_total[$value_aspek['aspek']]['butir_tidak_masalah'] = $butir_tidak_masalah;
			}

			$pdf->Cell(35, 5, 'JUMLAH', 1, 0, 'C');
			$pdf->Cell(10, 5, $skor_total[$value_aspek['aspek']]['butir_positif'], 1, 0, 'C');
			$pdf->Cell(10, 5, $skor_total[$value_aspek['aspek']]['butir_negatif'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['j'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['k'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['sr'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['u'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['l'], 1, 0, 'C');
			$pdf->Cell(12, 5, $skor_total[$value_aspek['aspek']]['sr'], 1, 0, 'C');
			$pdf->Cell(12, 5, $skor_total[$value_aspek['aspek']]['skor_1'], 1, 0, 'C');
			$pdf->Cell(12, 5, $skor_total[$value_aspek['aspek']]['skor_2'], 1, 0, 'C');
			$pdf->Cell(13, 5, $skor_total[$value_aspek['aspek']]['butir_masalah'], 1, 0, 'C');
			$pdf->Cell(13, 5, $skor_total[$value_aspek['aspek']]['butir_tidak_masalah'], 1, 0, 'C');
			$pdf->Cell(25, 5, '', 1, 1, 'C');
		}

		$pdf->SetTitle('LAPORAN PTSDL ' . $get_kelompok[0]['nama_kelompok'] . '.pdf');

		$pdf->Output('I', 'LAPORAN PTSDL ' . $get_kelompok[0]['nama_kelompok'] . '.pdf', FALSE);
	}

	public function laporan_kelas($id = "")
	{
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $get_user[0]['jenjang']));
		$get_kode = $this->Main_model->get_where('user_instrumen', array('user_id' => $this->session->userdata('id'), 'instrumen_id' => $get_instrumen[0]['id']));
		$get_data = $this->Main_model->get_where('instrumen_jawaban', array('kelas' => $id, 'instrumen_id' => $get_kode[0]['id']));
		$get_surat = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$get_kelas = $this->Main_model->get_where('kelas', array('id' => $id));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id']));
		foreach ($get_data as $key => $value) {
			$jawaban[] = unserialize($value['jawaban']);
			$jawaban_berat = unserialize($value['jawaban_berat']);
		}

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);

		$pdf->SetFont('Arial', '', 11);
		if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image(base_url('uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kiri']), 4, 10, 35, 27);
		} else {
			$pdf->Image(base_url('assets/img/logo_iki.png'), 8, 6, 30, 27);
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
		$pdf->Cell(185, 6, 'INSTRUMEN ALAT UNGKAP MASALAH - PTSDL ' . $get_user[0]['jenjang'], 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, strtoupper(getField('user_info', 'instansi', array('id' => $this->session->userdata('id')))), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'TAHUN AJARAN 2020/2021', 0, 0, 'C');

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
		$pdf->Cell(56, 6, 'Jumlah Responden', 0, 0, 'L');
		$pdf->Cell(2, 6, ':', 0, 0, 'C');
		$pdf->Cell(10, 6, count($get_data), 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, 'A. DATA DASAR PERMASALAHAN YANG DIALAMI KELOMPOK', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '1. Grafik Permasalahan Tiap Bidang', 0, 0, 'L');

		$sumArray = array();
		$sumArrayMasalah = array();

		$count_j = 0;
		foreach ($jawaban as $key => $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				if ($get_butir[0]['jenis_pernyataan'] == 1) {
					if ($value2 == 'L') {
						$value2 = 2;
					} elseif ($value2 == 'U') {
						$value2 = 1;
					} elseif ($value2 == 'SR') {
						$value2 = 'SR';
					} elseif ($value2 == 'J' || $value2 == 'K') {
						$value2 = 'Masalah';
					}
				} elseif ($get_butir[0]['jenis_pernyataan'] == 2) {
					if ($value2 == 'J') {
						$value2 = 2;
					} elseif ($value2 == 'K') {
						$value2 = 1;
					} elseif ($value2 == 'SR') {
						$value2 = 'SR';
					} elseif ($value2 == 'U' || $value2 == 'L') {
						$value2 = 'Masalah';
					}
				}

				$array_pernyataan[$key2] = $value2;
			}
			$array_jawaban[$count_j++] = $array_pernyataan;
		}

		foreach ($array_jawaban as $k => $subArray) {

			foreach ($subArray as $id => $value) {
				if ($value == 'Masalah') {
					$value = 1;
					@$sumArrayMasalah[$id] += $value;
				} else {
					@$sumArray[$id] += $value;
				}
			}
		}

		$kalkulasi_ptsdl = array();
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (@$sumArray[$value['id']]) {
					$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor'][] = $sumArray[$value['id']];
				}

				if (@$sumArrayMasalah[$value['id']]) {
					$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah'][] = $sumArrayMasalah[$value['id']];
				}
			}
			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir'][] = count($get_butir) * count($get_data);
			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir_skor'][] = (count($get_butir) * 2) * count($get_data);
			$skor[$value_aspek['kode_aspek']] = (array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor']) / $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir_skor'][0]) * 100;
			$masalah[$value_aspek['kode_aspek']] = (array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah']) / $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['butir'][0]) * 100;
		}


		$rowLabels = array("P", "T", "S", "D", "L");

		$data[0] = array($skor['P'], $masalah['P']);
		$data[1] = array($skor['T'], $masalah['T']);
		$data[2] = array($skor['S'], $masalah['S']);
		$data[3] = array($skor['D'], $masalah['D']);
		$data[4] = array($skor['L'], $masalah['L']);

		$chartXPos = 0;
		$chartYPos = 195;
		$chartWidth = 170;
		$chartHeight = 80;
		$chartYStep = 100;

		$pdf->Ln(10);
		$pdf->Cell(10, 6, '', 0, 0, 'C');


		$xScale = count($rowLabels) / ($chartWidth - 40);
		$maxTotal = 100;
		$yScale = $maxTotal / $chartHeight;
		$barWidth = (1 / $xScale) / 1.5;

		$pdf->ColumnChart2($chartWidth, $chartHeight, $data, null, array(255, 175, 100));

		for ($i = 0; $i < count($rowLabels); $i++) {
			$pdf->SetXY($chartXPos + 50 +  $i / $xScale, $chartYPos);
			$pdf->Cell(10, 0, $rowLabels[$i], 0, 0, 'C');
		}

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Ln(5);
		$pdf->Cell(10);
		$pdf->Cell(50, 6, 'Penafsiran :', 0, 0, 'L');
		$pdf->Ln(5);
		$pdf->Cell(185, 6, 'Tabel 1. Penafsiran Persentase', 0, 1, 'C');
		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Skor Kondisi Kegiatan Belajar', 1, 0, 'C');
		$pdf->Cell(30, 7, 'Persentase', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Kondisi Permasalahan', 'R,B,T', 1, 'C');

		$pdf->SetFont('Arial', '', 12);

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Sangat Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '81-100%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Sangat Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '61-80%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Kurang Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '41-60%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Cukup Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Tidak Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '21-40%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Sangat Memerlukan Perhatian', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Sangat Tidak Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '0-20%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Memerlukan Perhatian', 'R,B,T', 1, 'C');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '2. Tabel Rekapitulasi Komponen PTSDL', 0, 0, 'L');
		$pdf->Ln(5);
		$pdf->Cell(185, 6, 'Tabel 2. Rekapitulasi Komponen PTSDL', 0, 1, 'C');
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(25, 7, 'Kategori', 'L,R,T', 0, 'C');
		$pdf->Cell(40, 7, 'Skor', 'R,B,T', 0, 'C');
		$pdf->Cell(107, 7, 'Masalah', 'L,R,T', 1, 'C');

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(25, 7, 'Item', 'L,R,B', 0, 'C');
		$pdf->Cell(20, 7, 'Jml', 'R,B,T', 0, 'C');
		$pdf->Cell(20, 7, '%', 'R,B,T', 0, 'C');
		$pdf->Cell(71, 7, 'No Item Masalah', 'R,B,T', 0, 'C');
		$pdf->Cell(18, 7, 'Jml', 'R,B,T', 0, 'C');
		$pdf->Cell(18, 7, '%', 'R,B,T', 1, 'C');

		$pdf->SetFont('Arial', '', 12);
		$pdf->SetWidths(array(25, 20, 20, 71, 18, 18));
		$pdf->SetAligns(array('C', 'C', 'C', 'L', 'C', 'C'));
		srand(microtime() * 1000000);

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			$array_skor = array();
			$array_masalah = array();
			foreach ($get_butir as $key => $value) {
				if (@$sumArray[$value['id']]) {
					$array_skor[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}

				if (@$sumArrayMasalah[$value['id']]) {
					$array_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}
			}


			$pdf->Cell(6, 7, '', 0, 0, 'L');
			$pdf->Row(array($value_aspek['kode_aspek'], array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor']), round($skor[$value_aspek['kode_aspek']], 2), ($array_masalah) ? implode(",", $array_masalah[$value_aspek['kode_aspek']]) : '-', array_sum($kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah']), round($masalah[$value_aspek['kode_aspek']], 2)));
		}

		$total_skor_mentah = array_sum($kalkulasi_ptsdl['P']['skor']) + array_sum($kalkulasi_ptsdl['T']['skor']) + array_sum($kalkulasi_ptsdl['S']['skor']) + array_sum($kalkulasi_ptsdl['D']['skor']) + array_sum($kalkulasi_ptsdl['L']['skor']);

		$total_skor = ($skor['P'] + $skor['T'] + $skor['S'] + $skor['D'] + $skor['L']) / 5;

		$total_masalah_mentah = array_sum($kalkulasi_ptsdl['P']['masalah']) + array_sum($kalkulasi_ptsdl['T']['masalah']) + array_sum($kalkulasi_ptsdl['S']['masalah']) + array_sum($kalkulasi_ptsdl['D']['masalah']) + array_sum($kalkulasi_ptsdl['L']['masalah']);

		$total_masalah = ($masalah['P'] + $masalah['T'] + $masalah['S'] + $masalah['D'] + $masalah['L']['masalah']) / 5;

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah', round($total_skor_mentah, 2), round($total_skor, 2), '-', $total_masalah_mentah, round($total_masalah, 2)));

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, 'C. SKOR ITEM KOMPONEN PTSDL', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '1. Skor Item Komponen PTSDL Tertinggi Dalam Kelompok', 0, 1, 'L');
		$pdf->Cell(185, 6, 'Tabel 3. Skor Item Komponen PTSDL Tertinggi Dalam Kelompok', 0, 0, 'C');
		$pdf->Ln();
		$pdf->SetWidths(array(20, 90, 30, 30));
		srand(microtime() * 1000000);

		arsort($sumArray);

		$pdf->SetAligns(array('C', 'C', 'C', 'C'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('No Item', 'Pernyataan', 'Bidang', 'Total'), 6);
		$pdf->SetFont('Arial', '', 12);

		$pdf->SetLeftMargin(16);
		$counter_masalah = 1;
		foreach ($sumArray as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan', '*', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_pernyataan.id' => $key));
			$height1 = $pdf->GetY();
			if ($counter_masalah < 11) {
				$pdf->SetAligns(array('C', 'L', 'C', 'C'));
				$pdf->Row(array($counter_masalah, $get_instrumen[0]['pernyataan'], $get_instrumen[0]['kode_aspek'], $sumArray[$key]), 6);
			}


			$counter_masalah++;
		}

		$pdf->SetLeftMargin(10);

		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '2. Statistika Kelompok Skor Item Komponen PTSDL', 0, 1, 'L');
		$pdf->Cell(185, 6, 'Tabel 4. Skor Item Komponen PTSDL Tertinggi Dalam Kelompok', 0, 1, 'C');

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetWidths(array(70, 100));
		srand(microtime() * 1000000);
		$pdf->SetAligns(array('L', 'L'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');

		if ($total_skor <= 20) {
			$kategori = 'Sangat Tidak Baik';
		} elseif ($total_skor <= 40) {
			$kategori = 'Tidak Baik';
		} elseif ($total_skor <= 60) {
			$kategori = 'Kurang Baik';
		} elseif ($total_skor <= 80) {
			$kategori = 'Baik';
		} else {
			$kategori = 'Sangat Baik';
		}

		foreach ($array_jawaban as $key => $value) {
			$jawaban_individu[] = array_sum($value);
			$count_jawaban[] = array_count_values($value);
		}

		foreach ($count_jawaban as $key => $value) {
			$count_masalah[] = $value['Masalah'];
		}

		$pdf->Row(array('Kategori', $kategori));

		asort($sumArray);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Skor Terendah', reset($sumArray)));

		arsort($sumArray);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Skor Tertinggi', reset($sumArray)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Rata-Rata Jumlah Skor', $total_skor_mentah / count($get_data)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Modus', implode(",", modes($jawaban_individu))));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Median', calculate_median($jawaban_individu)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Standar Deviasi', standar_deviation($jawaban_individu)));

		arsort($jawaban_individu);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Skor Item Paling Banyak', reset($jawaban_individu)));

		asort($jawaban_individu);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Skor Item Paling Sedikit', reset($jawaban_individu)));

		$pdf->Ln(8);
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, 'D. ITEM MASALAH KOMPONEN PTSDL', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '1. Item Masalah Komponen PTSDL Paling Dominan Dalam Kelompok', 0, 1, 'L');
		$pdf->Cell(185, 6, 'Tabel 5. Item Masalah Komponen PTSDL Paling Dominan Dalam Kelompok', 0, 1, 'C');
		$pdf->SetWidths(array(20, 90, 30, 30));
		srand(microtime() * 1000000);

		arsort($sumArrayMasalah);

		$pdf->SetAligns(array('C', 'C', 'C', 'C'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('No Item', 'Pernyataan', 'Bidang', 'Total'));
		$pdf->SetFont('Arial', '', 12);

		$pdf->SetLeftMargin(16);
		$counter_masalah = 1;
		foreach ($sumArrayMasalah as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan', '*', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_pernyataan.id' => $key));
			if ($counter_masalah < 11) {
				$pdf->SetAligns(array('C', 'L', 'C', 'C'));
				$pdf->Row(array($counter_masalah, $get_instrumen[0]['pernyataan'], $get_instrumen[0]['kode_aspek'], $sumArrayMasalah[$key]), 6);
			}


			$counter_masalah++;
		}

		$pdf->SetLeftMargin(10);

		$pdf->Ln();
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '2. Statistika Kelompok Item Masalah Komponen PTSDL', 0, 0, 'L');
		$pdf->Ln(8);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->SetWidths(array(70, 100));
		srand(microtime() * 1000000);
		$pdf->SetAligns(array('L', 'L'));
		$pdf->Cell(6, 7, '', 0, 0, 'L');

		if ($total_masalah <= 20) {
			$kategori = 'Memerlukan Perhatian';
		} elseif ($total_skor <= 40) {
			$kategori = 'Sangat Memerlukan Perhatian';
		} elseif ($total_skor <= 60) {
			$kategori = 'Cukup Tinggi';
		} elseif ($total_skor <= 80) {
			$kategori = 'Tinggia';
		} else {
			$kategori = 'Sangat Tinggi';
		}

		$pdf->Row(array('Kategori', $kategori));

		asort($sumArrayMasalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Masalah Terendah', reset($sumArrayMasalah)));

		arsort($sumArrayMasalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah Masalah Tertinggi', reset($sumArrayMasalah)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Rata-Rata Jumlah Masalah', $total_masalah_mentah / count($get_data)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Mode', implode(",", modes($count_masalah))));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Median', calculate_median($count_masalah)));

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Standar Deviasi', standar_deviation($count_masalah)));

		arsort($count_masalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Item Masalah Paling Banyak', reset($count_masalah)));

		asort($count_masalah);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Item Masalah Paling Sedikit', reset($count_masalah)));
		$pdf->SetFont('Arial', '', 12);
		$pdf->Ln(8);
		$pdf->Cell(178, 6, 'Jakarta, ' . konversi_tanggal(date('Y-m-d')), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(178, 6, 'Pengolah Data', 0, 0, 'R');
		$pdf->Ln(20);
		$pdf->Cell(178, 6, getField('user_konselor', 'nama_lengkap', array('id' => $get_kelas[0]['konselor_id'])), 0, 0, 'R');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(6, 5, 'Lampiran', 0, 1, 'L');
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(185, 6, 'Rekapitulasi Jawaban Peserta Didik', 0, 1, 'C');
		$pdf->Ln(5);

		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, 'Komponen', 'L,R,T', 0, 'C');
		$pdf->Cell(10, 10, 'No', 'R,B,T', 0, 'C');
		$pdf->Cell(20, 5, 'Jenis Item', 'T,B', 0, 'C');
		$pdf->Cell(30, 5, 'Jawaban', 1, 0, 'C');
		$pdf->Cell(36, 5, 'Skor', 'L,R,T', 0, 'C');
		$pdf->Cell(26, 5, 'Masalah', 'L,R,T', 0, 'C');
		$pdf->Cell(25, 10, 'Ket', 1, 0, 'C');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, 'Item', 'L,B,R', 0, 'C');
		$pdf->Cell(10, 5, '', 0, 0, 'L');
		$pdf->Cell(10, 5, 'P', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, 'N', 'B', 0, 'C');
		$pdf->Cell(6, 5, 'J', 'L,R,B', 0, 'C');
		$pdf->Cell(6, 5, 'K', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, 'SR', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, 'U', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, 'L', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '0', 'T,R,B', 0, 'C');
		$pdf->Cell(12, 5, '1', 'T,R,B', 0, 'C');
		$pdf->Cell(12, 5, '2', 'T,R,B', 0, 'C');
		$pdf->Cell(13, 5, 'Ya', 'T,R,B', 0, 'C');
		$pdf->Cell(13, 5, 'Bukan', 'T,B', 0, 'C');
		$pdf->Cell(25, 5, '', 0, 0, 'L');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, '1', 'L,B,R', 0, 'C');
		$pdf->Cell(10, 5, '2', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, '3', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, '4', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '5', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '6', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '7', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '8', 'R,B', 0, 'C');
		$pdf->Cell(6, 5, '9', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '10', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '11', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '12', 'R,B', 0, 'C');
		$pdf->Cell(13, 5, '13', 'R,B', 0, 'C');
		$pdf->Cell(13, 5, '14', 'R,B', 0, 'C');
		$pdf->Cell(25, 5, '15', 'R,B', 0, 'C');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->SetFont('Arial', '', 8);
		$pdf->SetWidths(array(25, 10, 10, 10, 6, 6, 6, 6, 6, 12, 12, 12, 13, 13, 25));
		$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
		srand(microtime() * 1000000);

		foreach ($array_jawaban as $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				foreach ($get_butir as $key_butir => $value_butir) {
					$array_convert_jawaban[$key2][] = $value[$key2];
				}
			}
		}

		foreach ($jawaban as $value) {
			foreach ($value as $key2 => $value2) {
				$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('id' => $key2));
				foreach ($get_butir as $key_butir => $value_butir) {
					$array_hitung_jawaban[$key2][] = $value[$key2];
				}
			}
		}

		$pdf->SetLeftMargin(16);
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			$butir_positif = 1;
			$butir_negatif = 1;
			$butir_j = 0;
			$butir_k = 0;
			$butir_sr = 0;
			$butir_u = 0;
			$butir_l = 0;
			$skor_1 = 0;
			$skor_2 = 0;
			$butir_masalah = 0;

			foreach ($get_butir as $key => $value) {
				$total_butir_isi = array_count_values($array_hitung_jawaban[$value['id']]);
				$butir_convert_isi = array_count_values($array_convert_jawaban[$value['id']]);
				$pdf->Row(array($value_aspek['kode_aspek'], $value['kode_pernyataan'], $value['jenis_pernyataan'] == 1 ? 'V' : '', $value['jenis_pernyataan'] == 2 ? 'V' : '', @$total_butir_isi['J'], @$total_butir_isi['K'], @$total_butir_isi['SR'], @$total_butir_isi['U'], @$total_butir_isi['L'], @$butir_convert_isi['SR'], @$butir_convert_isi['1'], @$butir_convert_isi['2'], @$butir_convert_isi['Masalah'], ((count($jawaban) - @$butir_convert_isi['Masalah']) - @$total_butir_isi['SR']) != 0 ? (count($jawaban) - @$butir_convert_isi['Masalah']) - @$total_butir_isi['SR'] : '', ''));
				if ($value['jenis_pernyataan'] == 1) {
					$skor_total[$value_aspek['aspek']]['butir_positif'] = $butir_positif++;
				} elseif ($value['jenis_pernyataan'] == 2) {
					$skor_total[$value_aspek['aspek']]['butir_negatif'] = $butir_negatif++;
				}

				$butir_j += @$total_butir_isi['J'];
				$butir_k += @$total_butir_isi[''];
				$butir_sr += @$total_butir_isi['SR'];
				$butir_u += @$total_butir_isi['U'];
				$butir_l += @$total_butir_isi['L'];
				$skor_1 += @$butir_convert_isi['1'];
				$skor_2 += @$butir_convert_isi['2'];
				$butir_masalah += @$butir_convert_isi['Masalah'];
				$butir_tidak_masalah = (count($get_butir) * count($jawaban)) - $butir_sr - $butir_masalah;

				$skor_total[$value_aspek['aspek']]['j'] = $butir_j;
				$skor_total[$value_aspek['aspek']]['k'] = $butir_k;
				$skor_total[$value_aspek['aspek']]['sr'] = $butir_sr;
				$skor_total[$value_aspek['aspek']]['u'] = $butir_u;
				$skor_total[$value_aspek['aspek']]['l'] = $butir_l;
				$skor_total[$value_aspek['aspek']]['skor_1'] = $skor_1;
				$skor_total[$value_aspek['aspek']]['skor_2'] = $skor_2;
				$skor_total[$value_aspek['aspek']]['butir_masalah'] = $butir_masalah;
				$skor_total[$value_aspek['aspek']]['butir_tidak_masalah'] = $butir_tidak_masalah;
			}

			$pdf->Cell(35, 5, 'JUMLAH', 1, 0, 'C');
			$pdf->Cell(10, 5, $skor_total[$value_aspek['aspek']]['butir_positif'], 1, 0, 'C');
			$pdf->Cell(10, 5, $skor_total[$value_aspek['aspek']]['butir_negatif'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['j'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['k'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['sr'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['u'], 1, 0, 'C');
			$pdf->Cell(6, 5, $skor_total[$value_aspek['aspek']]['l'], 1, 0, 'C');
			$pdf->Cell(12, 5, $skor_total[$value_aspek['aspek']]['sr'], 1, 0, 'C');
			$pdf->Cell(12, 5, $skor_total[$value_aspek['aspek']]['skor_1'], 1, 0, 'C');
			$pdf->Cell(12, 5, $skor_total[$value_aspek['aspek']]['skor_2'], 1, 0, 'C');
			$pdf->Cell(13, 5, $skor_total[$value_aspek['aspek']]['butir_masalah'], 1, 0, 'C');
			$pdf->Cell(13, 5, $skor_total[$value_aspek['aspek']]['butir_tidak_masalah'], 1, 0, 'C');
			$pdf->Cell(25, 5, '', 1, 1, 'C');
		}
		$pdf->SetTitle('LAPORAN PTSDL ' . $get_kelas[0]['kelas'] . '.pdf');

		$pdf->Output('I', 'LAPORAN PTSDL ' . $get_kelas[0]['kelas'] . '.pdf', FALSE);
	}

	public function laporan_individu($id = "")
	{
		$this->load->library('fpdf_diag');
		$get_profil = $this->Main_model->get_where('instrumen_jawaban', array('id' => $id));
		$get_kelas = $this->Main_model->get_where('kelas', array('id' => $get_profil[0]['kelas']));
		$get_user = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$get_surat = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('nickname' => 'AUM PTSDL', 'jenjang' => $get_user[0]['jenjang']));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $get_instrumen[0]['id']));
		$jawaban = unserialize($get_profil[0]['jawaban']);
		$jawaban_mentah = unserialize($get_profil[0]['jawaban']);

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));

			foreach ($get_butir as $key => $value) {

				if ($value['jenis_pernyataan'] == 1) {
					if ($jawaban[$value['id']] == 'L') {
						$jawaban[$value['id']] = 2;
					} elseif ($jawaban[$value['id']] == 'U') {
						$jawaban[$value['id']] = 1;
					} elseif ($jawaban[$value['id']] == 'SR') {
						$jawaban[$value['id']] = 'SR';
					} elseif ($jawaban[$value['id']] == 'J' || $jawaban[$value['id']] == 'K') {
						$jawaban[$value['id']] = 'Masalah';
					}
					$array_pernyataan[$value['id']] = $jawaban[$value['id']];
				} elseif ($value['jenis_pernyataan'] == 2) {
					if ($jawaban[$value['id']] == 'J') {
						$jawaban[$value['id']] = 2;
					} elseif ($jawaban[$value['id']] == 'K') {
						$jawaban[$value['id']] = 1;
					} elseif ($jawaban[$value['id']] == 'SR') {
						$jawaban[$value['id']] = 'SR';
					} elseif ($jawaban[$value['id']] == 'U' || $jawaban[$value['id']] == 'L') {
						$jawaban[$value['id']] = 'Masalah';
					}
					$array_pernyataan[$value['id']] = $jawaban[$value['id']];
				}
			}
		}


		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			$masalah = 0;
			$skor = 0;

			foreach ($get_butir as $key => $value) {
				if (@$array_pernyataan[$value['id']] == 'Masalah') {
					$masalah++;
				} elseif (@$array_pernyataan[$value['id']] != 'SR') {
					$skor += @$array_pernyataan[$value['id']];
				}

				$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
			}

			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah'] = round(($masalah / count($get_butir)) * 100, 2);
			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor'] = round(($skor / (2 * count($get_butir))) * 100, 2);
			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah_mentah'] = $masalah;
			$kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor_mentah'] = $skor;
		}


		$pdf->SetFont('Arial', '', 11);
		if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image(base_url('uploads/logo/' . $get_surat[0]['user_id'] . '/' . $get_surat[0]['logo_kiri']), 4, 10, 35, 27);
		} else {
			$pdf->Image(base_url('assets/img/logo_iki.png'), 8, 6, 30, 27);
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
		$pdf->Cell(185, 6, 'LAPORAN INDIVIDU', 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'INSTRUMEN ALAT UNGKAP MASALAH - PTSDL ' . $get_user[0]['jenjang'], 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, strtoupper(getField('user_info', 'instansi', array('id' => $this->session->userdata('id')))), 0, 0, 'C');
		$pdf->Ln();
		$pdf->Cell(185, 6, 'TAHUN AJARAN 2020/2021', 0, 0, 'C');

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
		$pdf->Cell(50, 6, 'A. DATA DASAR PERMASALAHAN YANG DIALAMI INDIVIDU', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(50, 6, '1. Grafik Permasalahan Tiap Bidang', 0, 0, 'L');

		$rowLabels = array("P", "T", "S", "D", "L");

		$data[0] = array($kalkulasi_ptsdl['P']['skor'], $kalkulasi_ptsdl['P']['masalah']);
		$data[1] = array($kalkulasi_ptsdl['T']['skor'], $kalkulasi_ptsdl['T']['masalah']);
		$data[2] = array($kalkulasi_ptsdl['S']['skor'], $kalkulasi_ptsdl['S']['masalah']);
		$data[3] = array($kalkulasi_ptsdl['D']['skor'], $kalkulasi_ptsdl['D']['masalah']);
		$data[4] = array($kalkulasi_ptsdl['L']['skor'], $kalkulasi_ptsdl['L']['masalah']);

		$chartXPos = 0;
		$chartYPos = 195;
		$chartWidth = 170;
		$chartHeight = 80;
		$chartYStep = 100;

		$pdf->Ln(10);
		$pdf->Cell(10, 6, '', 0, 0, 'C');


		$xScale = count($rowLabels) / ($chartWidth - 40);
		$maxTotal = 100;
		$yScale = $maxTotal / $chartHeight;
		$barWidth = (1 / $xScale) / 1.5;

		$pdf->ColumnChart2($chartWidth, $chartHeight, $data, null, array(255, 175, 100));

		for ($i = 0; $i < count($rowLabels); $i++) {
			$pdf->SetXY($chartXPos + 50 +  $i / $xScale, $chartYPos);
			$pdf->Cell(10, 0, $rowLabels[$i], 0, 0, 'C');
		}

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Ln(5);
		$pdf->Cell(10);
		$pdf->Cell(50, 6, 'Penafsiran :', 0, 1, 'L');
		$pdf->Cell(185, 6, 'Tabel 1. Penafsiran Persentase', 0, 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Skor Kondisi Kegiatan Belajar', 1, 0, 'C');
		$pdf->Cell(30, 7, 'Persentase', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Kondisi Permasalahan', 'R,B,T', 1, 'C');

		$pdf->SetFont('Arial', '', 12);

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Sangat Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '81-100%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Sangat Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '61-80%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Kurang Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '41-60%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Cukup Tinggi', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Tidak Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '21-40%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Sangat Memerlukan Perhatian', 'R,B,T', 1, 'C');

		$pdf->Cell(10, 7, '', 0, 0, 'L');
		$pdf->Cell(80, 7, 'Sangat Tidak Baik', 1, 0, 'C');
		$pdf->Cell(30, 7, '0-20%', 'R,B,T', 0, 'C');
		$pdf->Cell(60, 7, 'Memerlukan Perhatian', 'R,B,T', 1, 'C');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(50, 6, '2. Tabel Masalah Tiap Bidang', 0, 0, 'L');
		$pdf->Ln(5);
		$pdf->Cell(185, 6, 'Tabel 2. Masalah Tiap Bidang', 0, 0, 'C');
		$pdf->Ln();

		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(25, 7, 'Kategori', 'L,R,T', 0, 'C');
		$pdf->Cell(40, 7, 'Skor', 'R,B,T', 0, 'C');
		$pdf->Cell(107, 7, 'Masalah', 'L,R,T', 1, 'C');

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Cell(25, 7, 'Item', 'L,R,B', 0, 'C');
		$pdf->Cell(20, 7, 'Jml', 'R,B,T', 0, 'C');
		$pdf->Cell(20, 7, '%', 'R,B,T', 0, 'C');
		$pdf->Cell(71, 7, 'No Item Masalah', 'R,B,T', 0, 'C');
		$pdf->Cell(18, 7, 'Jml', 'R,B,T', 0, 'C');
		$pdf->Cell(18, 7, '%', 'R,B,T', 1, 'C');

		$pdf->SetFont('Arial', '', 12);
		$pdf->SetWidths(array(25, 20, 20, 71, 18, 18));
		$pdf->SetAligns(array('C', 'C', 'C', 'L', 'C', 'C'));
		srand(microtime() * 1000000);

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			$array_no_masalah = array();

			foreach ($get_butir as $key => $value) {
				if ($jawaban[$value['id']] == 'Masalah') {
					$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}
			}

			$pdf->Cell(6, 7, '', 0, 0, 'L');
			$pdf->Row(array($value_aspek['kode_aspek'], $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor_mentah'], $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['skor'], ($array_no_masalah) ? implode(",", $array_no_masalah[$value_aspek['kode_aspek']]) : '-', $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah_mentah'], $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah']));

			$total_butir[$value_aspek['kode_aspek']] = count($get_butir);
		}


		$total_skor_mentah = $kalkulasi_ptsdl['P']['skor_mentah'] + $kalkulasi_ptsdl['T']['skor_mentah'] + $kalkulasi_ptsdl['S']['skor_mentah'] + $kalkulasi_ptsdl['D']['skor_mentah'] + $kalkulasi_ptsdl['L']['skor_mentah'];
		$total_skor = ($kalkulasi_ptsdl['P']['skor'] + $kalkulasi_ptsdl['T']['skor'] + $kalkulasi_ptsdl['S']['skor'] + $kalkulasi_ptsdl['D']['skor'] + $kalkulasi_ptsdl['L']['skor']) / 5;

		$total_masalah_mentah = ($kalkulasi_ptsdl['P']['masalah_mentah'] + $kalkulasi_ptsdl['T']['masalah_mentah'] + $kalkulasi_ptsdl['S']['masalah_mentah'] + $kalkulasi_ptsdl['D']['masalah_mentah'] + $kalkulasi_ptsdl['L']['masalah_mentah']) / 5;
		$total_masalah = ($kalkulasi_ptsdl['P']['masalah'] + $kalkulasi_ptsdl['T']['masalah'] + $kalkulasi_ptsdl['S']['masalah'] + $kalkulasi_ptsdl['D']['masalah'] + $kalkulasi_ptsdl['L']['masalah']) / 5;

		$pdf->Cell(6, 7, '', 0, 0, 'L');
		$pdf->Row(array('Jumlah', round($total_skor_mentah, 2), round($total_skor, 2), '-', $total_masalah_mentah, round($total_masalah, 2)));

		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50, 6, 'Catatan :', 0, 0, 'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50, 6, 'Skor ideal untuk :', 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(16, 6, 'P', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->Cell(20, 6, '2 x ' . $total_butir['P'] . ' = ' . $total_butir['P'] * 2, 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(16, 6, 'T', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->Cell(20, 6, '2 x ' . $total_butir['T'] . ' = ' . $total_butir['T'] * 2, 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(16, 6, 'S', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->Cell(20, 6, '2 x ' . $total_butir['S'] . ' = ' . $total_butir['S'] * 2, 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(16, 6, 'D', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->Cell(20, 6, '2 x ' . $total_butir['D'] . ' = ' . $total_butir['D'] * 2, 0, 0, 'L');

		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(16, 6, 'L', 0, 0, 'L');
		$pdf->Cell(5, 6, ':', 0, 0, 'L');
		$pdf->Cell(20, 6, '2 x ' . $total_butir['L'] . ' = ' . $total_butir['L'] * 2, 0, 0, 'L');

		$pdf->Ln(8);
		$pdf->Cell(176, 6, 'Jakarta, ' . konversi_tanggal(date('Y-m-d')), 0, 0, 'R');
		$pdf->Ln();
		$pdf->Cell(176, 6, 'Pengolah Data', 0, 0, 'R');
		$pdf->Ln(20);
		$pdf->Cell(176, 6, getField('user_konselor', 'nama_lengkap', array('id' => $get_kelas[0]['konselor_id'])), 0, 0, 'R');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(6, 5, 'Lampiran', 0, 1, 'L');
		$pdf->SetFont('Arial', 'B', 12);
		$pdf->Cell(185, 6, 'Rekapitulasi Jawaban Peserta Didik', 0, 1, 'C');
		$pdf->Ln(5);
		$pdf->SetFont('Arial', 'B', 8);
		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, 'Komponen', 'L,R,T', 0, 'C');
		$pdf->Cell(10, 10, 'No', 'R,B,T', 0, 'C');
		$pdf->Cell(20, 5, 'Jenis Item', 'T,B', 0, 'C');
		$pdf->Cell(30, 10, 'Jawaban', 1, 0, 'C');
		$pdf->Cell(36, 5, 'Skor', 'L,R,T', 0, 'C');
		$pdf->Cell(26, 5, 'Masalah', 'L,R,T', 0, 'C');
		$pdf->Cell(25, 10, 'Ket', 1, 0, 'C');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, 'Item', 'L,B,R', 0, 'C');
		$pdf->Cell(10, 5, '', 0, 0, 'L');
		$pdf->Cell(10, 5, 'P', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, 'N', 'B', 0, 'C');
		$pdf->Cell(30, 5, '', 0, 0, 'L');
		$pdf->Cell(12, 5, '0', 'T,R,B', 0, 'C');
		$pdf->Cell(12, 5, '1', 'T,R,B', 0, 'C');
		$pdf->Cell(12, 5, '2', 'T,R,B', 0, 'C');
		$pdf->Cell(13, 5, 'Ya', 'T,R,B', 0, 'C');
		$pdf->Cell(13, 5, 'Bukan', 'T,B', 0, 'C');
		$pdf->Cell(25, 5, '', 0, 0, 'L');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->Cell(6, 5, '', 0, 0, 'L');
		$pdf->Cell(25, 5, '1', 'L,B,R', 0, 'C');
		$pdf->Cell(10, 5, '2', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, '3', 'R,B', 0, 'C');
		$pdf->Cell(10, 5, '4', 'R,B', 0, 'C');
		$pdf->Cell(30, 5, '5', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '6', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '7', 'R,B', 0, 'C');
		$pdf->Cell(12, 5, '8', 'R,B', 0, 'C');
		$pdf->Cell(13, 5, '9', 'R,B', 0, 'C');
		$pdf->Cell(13, 5, '10', 'R,B', 0, 'C');
		$pdf->Cell(25, 5, '11', 'R,B', 0, 'C');
		$pdf->Cell(1, 5, '', 0, 1, 'C');

		$pdf->SetFont('Arial', '', 8);
		$pdf->SetWidths(array(25, 10, 10, 10, 30, 12, 12, 12, 13, 13, 25));
		$pdf->SetAligns(array('C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C', 'C'));
		srand(microtime() * 1000000);

		$pdf->SetLeftMargin(16);
		foreach ($get_aspek as $value_aspek) {
			$butir_positif = 1;
			$butir_negatif = 1;
			$skor_sr = 1;
			$skor_1 = 1;
			$skor_2 = 1;
			$butir_masalah = 1;
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan', array('aspek_id' => $value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				$pdf->Row(array($value_aspek['kode_aspek'], $value['kode_pernyataan'], $value['jenis_pernyataan'] == 1 ? 'V' : '', $value['jenis_pernyataan'] == 2 ? 'V' : '', $jawaban_mentah[$value['id']], $jawaban[$value['id']] == 'SR' ? 'V' : '', $jawaban[$value['id']] == 1 ? 'V' : '', $jawaban[$value['id']] == 2 ? 'V' : '', $jawaban[$value['id']] == 'Masalah' ? 'V' : '', ($jawaban[$value['id']] != 'Masalah' && $jawaban[$value['id']] != 'SR') ? 'V' : '', ''));

				if ($value['jenis_pernyataan'] == 1) {
					$skor_total[$value_aspek['aspek']]['butir_positif'] = $butir_positif++;
				} elseif ($value['jenis_pernyataan'] == 2) {
					$skor_total[$value_aspek['aspek']]['butir_negatif'] = $butir_negatif++;
				}

				if ($jawaban[$value['id']] == '2') {
					$skor_total[$value_aspek['aspek']]['skor_2'] = $skor_2++;
				} elseif ($jawaban[$value['id']] == '1') {
					$skor_total[$value_aspek['aspek']]['skor_1'] = $skor_1++;
				} elseif ($jawaban[$value['id']] == 'SR') {
					$skor_total[$value_aspek['aspek']]['skor_sr'] = $skor_sr++;
				} else {
					$skor_total[$value_aspek['aspek']]['masalah'] = $butir_masalah++;
				}
			}

			$butir_tidak_masalah = count($get_butir) - @$skor_total[$value_aspek['aspek']]['masalah'] - @$skor_total[$value_aspek['aspek']]['skor_sr'];

			$pdf->Cell(35, 5, 'JUMLAH', 1, 0, 'C');
			$pdf->Cell(10, 5, $skor_total[$value_aspek['aspek']]['butir_positif'], 1, 0, 'C');
			$pdf->Cell(10, 5, $skor_total[$value_aspek['aspek']]['butir_negatif'], 1, 0, 'C');
			$pdf->Cell(30, 5, '-', 1, 0, 'C');
			$pdf->Cell(12, 5, (isset($skor_total[$value_aspek['aspek']]['skor_sr'])) ? $skor_total[$value_aspek['aspek']]['skor_sr'] : 0, 1, 0, 'C');
			$pdf->Cell(12, 5, (isset($skor_total[$value_aspek['aspek']]['skor_1'])) ? $skor_total[$value_aspek['aspek']]['skor_1'] : 0, 1, 0, 'C');
			$pdf->Cell(12, 5, (isset($skor_total[$value_aspek['aspek']]['skor_2'])) ? $skor_total[$value_aspek['aspek']]['skor_2'] : 0, 1, 0, 'C');
			$pdf->Cell(13, 5, (isset($skor_total[$value_aspek['aspek']]['masalah'])) ? $kalkulasi_ptsdl[$value_aspek['kode_aspek']]['masalah_mentah'] : 0, 1, 0, 'C');
			$pdf->Cell(13, 5, $butir_tidak_masalah, 1, 0, 'C');
			$pdf->Cell(25, 5, '', 1, 1, 'C');
		}

		$pdf->SetTitle('LAPORAN PTSDL ' . getField('kelas', 'kelas', array('id' => $get_profil[0]['kelas'])) . ' - ' . $get_profil[0]['nama_lengkap'] . '.pdf');

		$pdf->Output('I', 'LAPORAN PTSDL ' . getField('kelas', 'kelas', array('id' => $get_profil[0]['kelas'])) . ' - ' . $get_profil[0]['nama_lengkap'] . '.pdf', FALSE);
	}
}

/* End of file Ptsdl.php */
/* Location: ./application/controllers/Ptsdl.php */
