<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dcm extends CI_Controller {

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
    //Get a random word
		$nb=rand(3,10);
		$w='';
		for($i=1;$i<=$nb;$i++)
			$w.=chr(rand(ord('a'),ord('z')));
		return $w;
	}

	public function GenerateSentence()
	{
    //Get a random sentence
		$nb=rand(1,10);
		$s='';
		for($i=1;$i<=$nb;$i++)
			$s.= $this->GenerateWord().' ';
		return substr($s,0,-1);
	}


	public function array_sort($array, $on, $order=SORT_ASC){

		$new_array = array();
		$sortable_array = array();

		if (count($array) > 0) {
			foreach ($array as $k => $v) {
				if (is_array($v)) {
					foreach ($v as $k2 => $v2) {
						if ($k2 == $on) {
							$sortable_array[$k] = $v2;
						}
					}
				} else {
					$sortable_array[$k] = $v;
				}
			}

			switch ($order) {
				case SORT_ASC:
				asort($sortable_array);
				break;
				case SORT_DESC:
				arsort($sortable_array);
				break;
			}

			foreach ($sortable_array as $k => $v) {
				$new_array[$k] = $array[$k];
			}
		}

		return $new_array;
	}

	public function index($jenjang="")
	{
		$this->load->library('encrypt');

		$get_ticket = $this->Main_model->join('ticket','*',array(array('table'=>'event_key','parameter'=>'ticket.event_key=event_key.id')),array('ticket.user_id'=>$this->session->userdata('id'),'event_key.tipe'=>3));
		$data['get_profil'] = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));


		if ($jenjang) {
			$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$jenjang));
		} else {
			$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$data['get_profil'][0]['jenjang']));
		}


		$data['get_kelompok'] = $this->Main_model->get_where('kelompok',array('user_id'=>$this->session->userdata('id')));
		
		$data['get_dcm'] = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>@$get_instrumen[0]['id']));

		$data['get_kode'] = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>@$data['get_dcm'][0]['instrumen_id']));
		
		$data['jenjang'] =  $jenjang;

		if ($get_ticket) {
			if ($jenjang) {
				$data['kelas'] = $this->Main_model->get_where('kelas',array('user_id'=>$this->session->userdata('id'),'jenjang'=>$jenjang),'kelas','asc');
			} else {
				$data['kelas'] = $this->Main_model->get_where('kelas',array('user_id'=>$this->session->userdata('id')),'kelas','asc');
			}
			$data['content'] = 'dcm.php';
		} else {
			$data['content'] = 'key_dcm';
		}
		

		$this->load->view('main.php', $data, FALSE);
	}

	public function view($id=""){
		$data['get_profil'] = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$data['id'] = $id;
		if ($data['get_profil'][0]['jenjang']=='SMA') {
			$jenjang = 1;
		} elseif ($data['get_profil'][0]['jenjang']=='SMP') {
			$jenjang = 2;
		} elseif ($data['get_profil'][0]['jenjang']=='SD') {
			$jenjang = 3;
		}

		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$data['get_profil'][0]['jenjang']));

		$get_dcm = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>$get_instrumen[0]['id']));
		
		$data['get_jawaban'] = $this->Main_model->get_where('instrumen_jawaban',array('instrumen_id'=>$get_dcm[0]['id'],'kelas'=>$id));
		$data['content'] = 'dcm_detail.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function kode($jenjang=""){
		$getProfil = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));

		if ($getProfil[0]['jenjang']=='SMA') {
			$data['jenjang'] = 1;
		} elseif ($getProfil[0]['jenjang']=='SMP') {
			$data['jenjang'] = 2;
		} elseif ($getProfil[0]['jenjang']=='SD') {
			$data['jenjang'] = 3;
		} else {
			$data['jenjang'] = $jenjang;
		}

		if ($jenjang) {
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$jenjang));
		} else {
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$getProfil[0]['jenjang']));
		}

		$data['get_dcm'] = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>@$data['get_instrumen'][0]['id']));

		$data['content'] = 'dcm_kode.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function kode_save(){
		$post = $this->input->post();
		$getProfil = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$get_dcm = $this->Main_model->get_where('user_instrumen',array('instrumen_id'=>$post['instrumen_id'],'user_id'=>$this->session->userdata('id')));
		$checkKode = $this->Main_model->get_where('user_instrumen',array('kode_singkat'=>$post['kode_singkat'],'instrumen_id !='=>$post['instrumen_id']));
		$jenjang = $post['jenjang'];
		unset($post['jenjang']);
		if ($checkKode) {
			$this->session->set_flashdata('error','Kode telah digunakan. Silahkan coba kode lain.');
			redirect('dcm/kode');
		} else {
			if ($get_dcm) {
				$this->Main_model->update_data('user_instrumen',$post,array('id'=>$get_dcm[0]['id']));
			} else {
				$this->Main_model->insert_data('user_instrumen',$post);
			}
		}
		
		if ($getProfil[0]['jenjang']=='Konselor') {
			redirect('dcm/index/'.$jenjang);
		} else{
			redirect('dcm');
		}
	}

	public function laporan_kelompok($id=""){
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$get_user[0]['jenjang']));
		$get_kode = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>$get_instrumen[0]['id']));
		$get_kelompok = $this->Main_model->get_where('kelompok',array('id'=>$id));

		$get_data = $this->Main_model->get_where_in('instrumen_jawaban','kelas',explode(",", $get_kelompok[0]['kelas']),array('instrumen_id'=>$get_kode[0]['id']));

		$get_surat = $this->Main_model->get_where('user_surat',array('user_id'=>$this->session->userdata('id')));
		$get_kelas = $this->Main_model->get_where_in('kelas','id',explode(",", $get_kelompok[0]['kelas']));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek',array('instrumen_id'=>$get_instrumen[0]['id']));

		$total_peserta = 0;
		foreach ($get_kelas as $key => $value) {
			$total_peserta += $value['jumlah_siswa'];
		}

		foreach ($get_data as $key => $value) {
			$jawaban = unserialize($value['jawaban']);
			$jawaban_deskriptif = unserialize($value['jawaban_deskriptif']);

			$search = ['Ya'];
			$replace = [1];
			$result[] = str_replace($search, $replace, $jawaban);
			$result_deskriptif[] = $jawaban_deskriptif[223];
		}

		$sumArray = array();
		foreach ($result as $k=>$subArray) {
			foreach ($subArray as $id=>$value) {
				@$sumArray[$id]+=$value;
			}
		}


		$skor_masalah = array();
		$i = 0;
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (@$sumArray[$value['id']]) {
					$skor_masalah[$value_aspek['kode_aspek']]['butir'][] = $sumArray[$value['id']];
				}
			}

			$persentase_masalah[$i]['persentase'] = (array_sum($skor_masalah[$value_aspek['kode_aspek']]['butir'])/(count($get_butir)*count($get_data)))*100;
			$persentase_masalah[$i]['jumlah_skor'] = array_sum($skor_masalah[$value_aspek['kode_aspek']]['butir']);
			$persentase_masalah[$i++]['aspek'] = $value_aspek['aspek'];
			$skor_masalah[$value_aspek['kode_aspek']]['total_butir'][] = count($get_butir)*count($get_data);
		}

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);
		$pdf->SetFont('Arial','',11);
		if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image(base_url('uploads/logo/'.$get_surat[0]['user_id'].'/'.$get_surat[0]['logo_kiri']),4,10,35,27);
		} else {
			$pdf->Image(base_url('assets/img/logo_iki.png'),8,6,30,27);
		}

		if (@$get_surat[0]['logo_kanan']) {
			$pdf->Image(base_url('uploads/logo/'.$get_surat[0]['user_id'].'/'.$get_surat[0]['logo_kanan']),170,10,35,27);
		} else {
			$pdf->Image(base_url('assets/img/logo_adebk.jpeg'),170,6,30,27);
		}
		$pdf->Ln(1);
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_pertama']),0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_kedua']),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_ketiga']),0,0,'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln();
		}
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(205,6,@$get_surat[0]['baris_keempat'],0,0,'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(4);
			$pdf->Cell(205,6,@$get_surat[0]['baris_kelima'],0,0,'C');
		}

		$pdf->Ln();
		$pdf->SetLineWidth(1);
		$pdf->Line(10,38,200,38);
		$pdf->SetLineWidth(0);
		$pdf->Line(10,39,200,39);
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
        // setting jenis font yang akan digunakan

		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'Rahasia',1,0,'C');
		$pdf->Cell(3);
		$pdf->Ln(7);
		$pdf->Cell(185,6,'LAPORAN KELOMPOK',0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'INSTRUMEN DAFTAR CEK MASALAH '.$get_user[0]['jenjang'],0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,strtoupper(getField('user_info','instansi',array('id'=>$this->session->userdata('id')))),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'TAHUN AJARAN 2020/2021',0,0,'C');

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(10);
		$pdf->Cell(50,6,'Identitas Kelompok',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(30,6,$get_kelompok[0]['nama_kelompok'],0,0,'L');
		$pdf->Cell(20);
		$pdf->Cell(56,6,'Tanggal Pengadministrasian',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(10,6,date('d/m/Y'),0,0,'L');

		$pdf->Ln();
		$pdf->Cell(50,6,'Jumlah Peserta',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(30,6,$total_peserta.' Siswa',0,0,'L');
		$pdf->Cell(20);
		$pdf->Cell(56,6,'Jumlah Responden',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(10,6,count($get_data).' Siswa',0,0,'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'A. Grafik dan Tabel DCM Setiap Bidang',0,0,'L');
		$pdf->Ln();

		$bidang_pribadi = array_sum($skor_masalah['KES']['butir'])+array_sum($skor_masalah['KEA']['butir'])+array_sum($skor_masalah['RKH']['butir'])+array_sum($skor_masalah['KHK']['butir'])+array_sum($skor_masalah['AGM']['butir']);
		$bidang_sosial = array_sum($skor_masalah['HPR']['butir'])+array_sum($skor_masalah['KSO']['butir'])+array_sum($skor_masalah['MDI']['butir']);
		$bidang_karir = array_sum($skor_masalah['MDP']['butir']);
		$bidang_belajar = array_sum($skor_masalah['PTS']['butir'])+array_sum($skor_masalah['PTK']['butir']);

		$bidang_total = $bidang_pribadi+$bidang_sosial+$bidang_belajar+$bidang_karir;

		$persentase_pribadi = number_format((float)($bidang_pribadi/$bidang_total)*100, 2, '.', '');
		$persentase_sosial = number_format((float)($bidang_sosial/$bidang_total)*100, 2, '.', '');
		$persentase_belajar = number_format((float)($bidang_belajar/$bidang_total)*100, 2, '.', '');
		$persentase_karir = number_format((float)($bidang_karir/$bidang_total)*100, 2, '.', '');

		$data = array('Pribadi' => $bidang_pribadi, 'Sosial' => $bidang_sosial, 'Belajar' => $bidang_belajar, 'Karir'=> $bidang_karir);

		$pdf->SetFont('Arial', 'BIU', 12);
		$pdf->Ln(8);

		$pdf->SetFont('Arial', '', 10);
		$valX = $pdf->GetX();
		$valY = $pdf->GetY();

		$pdf->SetXY(60, $valY);
		$col1=array(100,100,255);
		$col2=array(255,100,100);
		$col3=array(255,255,100);
		$col4=array(100,100,100);
		$pdf->PieChart(100, 100, $data, '%l (%p)', array($col1,$col2,$col3,$col4));
		$pdf->SetXY($valX, $valY + 40);

		$bidang_array = array();
		$bidang_array[0]['aspek'] = 'Pribadi';
		$bidang_array[1]['aspek'] = 'Sosial';
		$bidang_array[2]['aspek'] = 'Belajar';
		$bidang_array[3]['aspek'] = 'Karir';

		$bidang_array[0]['persentase'] = $bidang_pribadi;
		$bidang_array[1]['persentase'] = $bidang_sosial;
		$bidang_array[2]['persentase'] = $bidang_belajar;
		$bidang_array[3]['persentase'] = $bidang_karir;

		$listBidang = $this->array_sort($bidang_array, 'persentase', SORT_DESC);

		$maxBidang = max(array_column($bidang_array, 'persentase'));
		$minBidang = min(array_column($bidang_array, 'persentase'));

		foreach ($listBidang as $keyBidang => $valueBidang) {
			if ($valueBidang['persentase'] == $minBidang) {
				$bidangMin[] = $valueBidang['aspek'];
			}
		}

		foreach ($listBidang as $keyBidang => $valueBidang) {
			if ($valueBidang['persentase'] == $maxBidang) {
				$bidangMax[] = $valueBidang['aspek'];
			}
		}

		if (count($bidangMin)>1) {
			$last_element_min = array_pop($bidangMin);
			array_push($bidangMin, 'dan '.$last_element_min);
		}
		$textMinBidang = implode(", ",$bidangMin);

		if (count($bidangMax)>1) {
			$last_element_max = array_pop($bidangMin);
			array_push($bidangMax, 'dan '.$last_element_max);
		}
		$textMaxBidang = implode(", ",$bidangMax);

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(20);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Bidang',1,0,'C');
		$pdf->Cell(57,7,'Persentase','R,B,T',0,'C');
		$pdf->Cell(57,7,'Derajat Masalah','R,B,T',1,'C');

		if ($persentase_pribadi <= 20) {
			$keterangan_pribadi = 'A';
		} elseif ($persentase_pribadi > 20 && $persentase_pribadi <= 40){
			$keterangan_pribadi = 'B';
		} elseif ($persentase_pribadi > 40 && $persentase_pribadi <= 60){
			$keterangan_pribadi = 'C';
		} elseif ($persentase_pribadi > 60 && $persentase_pribadi <= 80){
			$keterangan_pribadi = 'D';
		} elseif ($persentase_pribadi > 80 && $persentase_pribadi <= 100){
			$keterangan_pribadi = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Pribadi',1,0,'L');
		$pdf->Cell(57,7,$persentase_pribadi.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_pribadi,'R,B,T',1,'C');

		if ($persentase_sosial <= 20) {
			$keterangan_sosial = 'A';
		} elseif ($persentase_sosial > 20 && $persentase_sosial <= 40){
			$keterangan_sosial = 'B';
		} elseif ($persentase_sosial > 40 && $persentase_sosial <= 60){
			$keterangan_sosial = 'C';
		} elseif ($persentase_sosial > 60 && $persentase_sosial <= 80){
			$keterangan_sosial = 'D';
		} elseif ($persentase_sosial > 80 && $persentase_sosial <= 100){
			$keterangan_sosial = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Sosial',1,0,'L');
		$pdf->Cell(57,7,$persentase_sosial.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_sosial,'R,B,T',1,'C');

		if ($persentase_belajar <= 20) {
			$keterangan_belajar = 'A';
		} elseif ($persentase_belajar > 20 && $persentase_belajar <= 40){
			$keterangan_belajar = 'B';
		} elseif ($persentase_belajar > 40 && $persentase_belajar <= 60){
			$keterangan_belajar = 'C';
		} elseif ($persentase_belajar > 60 && $persentase_belajar <= 80){
			$keterangan_belajar = 'D';
		} elseif ($persentase_belajar > 80 && $persentase_belajar <= 100){
			$keterangan_belajar = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Belajar',1,0,'L');
		$pdf->Cell(57,7,$persentase_belajar.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_belajar,'R,B,T',1,'C');

		if ($persentase_karir <= 20) {
			$keterangan_karir = 'A';
		} elseif ($persentase_karir > 20 && $persentase_karir <= 40){
			$keterangan_karir = 'B';
		} elseif ($persentase_karir > 40 && $persentase_karir <= 60){
			$keterangan_karir = 'C';
		} elseif ($persentase_karir > 60 && $persentase_karir <= 80){
			$keterangan_karir = 'D';
		} elseif ($persentase_karir > 80 && $persentase_karir <= 100){
			$keterangan_karir = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Karir',1,0,'L');
		$pdf->Cell(57,7,$persentase_karir.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_karir,'R,B,T',1,'C');

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan grafik dan tabel diatas dapat diketahui bahwa bidang '.$textMaxBidang.' merupakan permasalahan yang paling banyak dimiliki Peserta Didik. Sementara itu bidang '.$textMinBidang.' merupakan permasalahan yang sedikit dialami Peserta Didik.',1);

		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'B. Grafik dan Tabel DCM per Aspek',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50,6,'1. Grafik DCM per Aspek',0,0,'L');

		$textColour = array( 0, 0, 0 );
		$rowLabels = array( "1-20", "21-40", "41-60", "61-80", "81-100", "101-120", "121-140", "141-160", "161-180", "181-200", "201-220");
		$chartXPos = 0;
		$chartYPos = 115;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array( 255, 100, 100 ),
			array( 100, 255, 100 ),
			array( 100, 100, 255 ),
			array( 255, 255, 100 ),
		);

		$data = array(
			array((array_sum($skor_masalah['KES']['butir'])/$skor_masalah['KES']['total_butir'][0])*100),
			array((array_sum($skor_masalah['KEA']['butir'])/$skor_masalah['KEA']['total_butir'][0])*100),
			array((array_sum($skor_masalah['RKH']['butir'])/$skor_masalah['RKH']['total_butir'][0])*100),
			array((array_sum($skor_masalah['KSO']['butir'])/$skor_masalah['KSO']['total_butir'][0])*100),
			array((array_sum($skor_masalah['HPR']['butir'])/$skor_masalah['HPR']['total_butir'][0])*100),
			array((array_sum($skor_masalah['MDI']['butir'])/$skor_masalah['MDI']['total_butir'][0])*100),
			array((array_sum($skor_masalah['KHK']['butir'])/$skor_masalah['KHK']['total_butir'][0])*100),
			array((array_sum($skor_masalah['AGM']['butir'])/$skor_masalah['AGM']['total_butir'][0])*100),
			array((array_sum($skor_masalah['PTS']['butir'])/$skor_masalah['PTS']['total_butir'][0])*100),
			array((array_sum($skor_masalah['MDP']['butir'])/$skor_masalah['MDP']['total_butir'][0])*100),
			array((array_sum($skor_masalah['PTK']['butir'])/$skor_masalah['PTK']['total_butir'][0])*100),
		);

		$pdf->SetFont( 'Arial', '', 12 );


// Compute the X scale
		$xScale = count($rowLabels) / ( $chartWidth - 40 );

// Compute the Y scale

		$maxTotal = 100;

		foreach ( $data as $dataRow ) {
			$totalSales = 0;
			foreach ( $dataRow as $dataCell ) $totalSales += $dataCell;
			$maxTotal = ( $totalSales > $maxTotal ) ? $totalSales : $maxTotal;
		}

		$yScale = $maxTotal / $chartHeight;

// Compute the bar width
		$barWidth = ( 1 / $xScale ) / 1.5;

// Add the axes:

		$pdf->SetFont( 'Arial', '', 9);

// X axis
		$pdf->Line( $chartXPos + 30, $chartYPos, $chartXPos + $chartWidth, $chartYPos );

		for ( $i=0; $i < count( $rowLabels ); $i++ ) {
			$pdf->SetXY( $chartXPos + 40 +  $i / $xScale, $chartYPos );
			$pdf->Cell( $barWidth, 10, $rowLabels[$i], 0, 0, 'C' );
		}

// Y axis
		$pdf->Line( $chartXPos + 30, $chartYPos, $chartXPos + 30, $chartYPos - $chartHeight - 8 );

		for ( $i=0; $i <= $maxTotal; $i ++ ) {
			if ($i % 10 == 0) {
				$pdf->SetXY( $chartXPos + 7, $chartYPos - 5 - $i / $yScale );
				$pdf->Cell( 20, 10, $i, 0, 1, 'R' );
				$pdf->Line( $chartXPos + 28, $chartYPos - $i / $yScale, $chartXPos + 30, $chartYPos - $i / $yScale );
			}
		}

// Create the bars
		$xPos = $chartXPos + 40;
		$bar = 0;

		foreach ( $data as $dataRow ) {

  // Total up the sales figures for this product
			$totalSales = 0;
			foreach ( $dataRow as $dataCell ) $totalSales += $dataCell;

  // Create the bar
			$colourIndex = $bar % count( $chartColours );
			$pdf->SetFillColor( $chartColours[$colourIndex][0], $chartColours[$colourIndex][1], $chartColours[$colourIndex][2] );
			$pdf->Rect( $xPos, $chartYPos - ( $totalSales / $yScale ), $barWidth, $totalSales / $yScale, 'DF' );
			$xPos += ( 1 / $xScale );
			$bar++;
		}

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(85);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 1',1,0,'L');
		$pdf->Cell(25,7,'1-20','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kesehatan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 2',1,0,'L');
		$pdf->Cell(25,7,'21-40','R,B,T',0,'L');
		$pdf->Cell(120,7,'Keadaan Kehidupan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 3',1,0,'L');
		$pdf->Cell(25,7,'41-60','R,B,T',0,'L');
		$pdf->Cell(120,7,'Rekreasi dan Hobi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 4',1,0,'L');
		$pdf->Cell(25,7,'61-80','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kehidupan Sosial - Keaktifan Berorganisasi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 5',1,0,'L');
		$pdf->Cell(25,7,'81-100','R,B,T',0,'L');
		$pdf->Cell(120,7,'Hubungan Pribadi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 6',1,0,'L');
		$pdf->Cell(25,7,'101-120','R,B,T',0,'L');
		$pdf->Cell(120,7,'Muda Mudi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 7',1,0,'L');
		$pdf->Cell(25,7,'121-140','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kehidupan Keluarga','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 8',1,0,'L');
		$pdf->Cell(25,7,'141-160','R,B,T',0,'L');
		$pdf->Cell(120,7,'Agama dan Moral','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 9',1,0,'L');
		$pdf->Cell(25,7,'161-180','R,B,T',0,'L');
		$pdf->Cell(120,7,'Penyesuaian Terhadap Sekolah','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 10',1,0,'L');
		$pdf->Cell(25,7,'181-200','R,B,T',0,'L');
		$pdf->Cell(120,7,'Masa Depan dan Cita-Cita Pendidikan/Jabatan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 11',1,0,'L');
		$pdf->Cell(25,7,'201-220','R,B,T',0,'L');
		$pdf->Cell(120,7,'Penyesuaian Terhadap Kurikulum','R,B,T',1,'L');

		$list = $this->array_sort($persentase_masalah, 'persentase', SORT_DESC);
		$max = max(array_column($persentase_masalah, 'persentase'));
		$min = min(array_column($persentase_masalah, 'persentase'));

		foreach ($list as $key => $value) {
			if ($value['persentase'] == $min) {
				$valueMin[] = $value['aspek'];
			}
		}

		foreach ($list as $key => $value) {
			if ($value['persentase'] == $max) {
				$valueMax[] = $value['aspek'];
			}
		}

		if (count($valueMin)>1) {
			$last_element_min = array_pop($valueMin);
			array_push($valueMin, 'dan '.$last_element_min);
		}
		$textMin = implode(", ",$valueMin);

		if (count($valueMax)>1) {
			$last_element_max = array_pop($valueMax);
			array_push($valueMax, 'dan '.$last_element_max);
		}
		$textMax = implode(", ",$valueMax);

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan Grafik diatas dapat diketahui bahwa aspek '.$textMax.' merupakan permasalahan yang paling banyak dimiliki Peserta Didik. Sementara itu aspek '.$textMin.' merupakan permasalahan yang sedikit dialami Peserta Didik.',1);

		$pdf->AddPage();
		$pdf->Ln(3);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'2. Tabel Analisis DCM per Aspek',0,1,'L');
		$pdf->Ln(2);

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek',1,0,'C');
		$pdf->Cell(30,7,'No Item','R,B,T',0,'C');
		$pdf->Cell(40,7,'Jumlah Skor','R,B,T',0,'C');
		$pdf->Cell(35,7,'Persentase','R,B,T',0,'C');
		$pdf->Cell(40,7,'Keterangan','R,B,T',1,'C');

		$i = 0;
		$no = 1;
		$range1 = 1;
		$range2 = 20;
		foreach ($get_aspek as $key => $value) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));

			if ($persentase_masalah[$i]['persentase'] <= 20) {
				$keterangan = 'A';
			} elseif ($persentase_masalah[$i]['persentase'] > 20 && $persentase_masalah[$i]['persentase'] <= 40){
				$keterangan = 'B';
			} elseif ($persentase_masalah[$i]['persentase'] > 40 && $persentase_masalah[$i]['persentase'] <= 60){
				$keterangan = 'C';
			} elseif ($persentase_masalah[$i]['persentase'] > 60 && $persentase_masalah[$i]['persentase'] <= 80){
				$keterangan = 'C';
			} elseif ($persentase_masalah[$i]['persentase'] > 80 && $persentase_masalah[$i]['persentase'] <= 100){
				$keterangan = 'C';
			}

			$pdf->Cell(6,7,'',0,0,'L');
			$pdf->Cell(25,7,$no++,1,0,'C');
			$pdf->Cell(30,7,$range1.'-'.$range2,'R,B,T',0,'C');
			$pdf->Cell(40,7,$persentase_masalah[$i]['jumlah_skor'],'R,B,T',0,'C');
			$pdf->Cell(35,7,round($persentase_masalah[$i]['persentase'],2)."%",'R,B,T',0,'C');
			$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

			$range1+=20;
			$range2+=20;
			$i++;
		}

		$pdf->Ln(3);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');
		$pdf->Ln(2);

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'A',1,0,'C');
		$pdf->Cell(50,7,'0-20%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Baik Sekali','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'B',1,0,'C');
		$pdf->Cell(50,7,'21-40%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Baik','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'C',1,0,'C');
		$pdf->Cell(50,7,'41-60%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Cukup','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'D',1,0,'C');
		$pdf->Cell(50,7,'61-80%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Tidak Baik','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'E',1,0,'C');
		$pdf->Cell(50,7,'81-100%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Tidak Sangat Baik','R,B,T',1,'C');


		if ($max <= 20) {
			$keteranganMax = 'A';
		} elseif ($max > 20 && $max <= 40){
			$keteranganMax = 'B';
		} elseif ($max > 40 && $max <= 60){
			$keteranganMax = 'C';
		} elseif ($max > 60 && $max <= 80){
			$keteranganMax = 'D';
		} elseif ($max > 80 && $max <= 100){
			$keteranganMax = 'E';
		}

		if ($min <= 20) {
			$keteranganMin = 'A';
		} elseif ($min > 20 && $min <= 40){
			$keteranganMin = 'B';
		} elseif ($min > 40 && $min <= 60){
			$keteranganMin = 'C';
		} elseif ($min > 60 && $min <= 80){
			$keteranganMin = 'D';
		} elseif ($min > 80 && $min <= 100){
			$keteranganMin = 'E';
		}

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan Tabel diatas dapat diketahui bahwa aspek '.$textMax.' merupakan masalah yang paling banyak dialami peserta didik dengan derajat masalah '.$keteranganMax.'. Sementara itu '.$textMin.' merupakan permasalah yang sedikit dialami peserta didik dengan derajat masalah '.$keteranganMin.'.',1);

		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'C. 10 ITEM MASALAH YANG DOMINAN DALAM KELOMPOK',0,0,'L');

		$pdf->Ln(8);
		$pdf->SetWidths(array(10,90,50,20));
		srand(microtime()*1000000);

		arsort($sumArray);
		$pdf->Cell(8,7,'',0,0,'L');
		$pdf->SetAligns(array('C','C','C','C'));
		$pdf->Row(array('No','Pernyataan','Bidang','%'));

		$pdf->SetFont('Arial','',12);

		$counter_masalah = 1;

		$pdf->SetLeftMargin(18);
		foreach ($sumArray as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan','*',array(array('table'=>'instrumen_aspek','parameter'=>'instrumen_pernyataan.aspek_id=instrumen_aspek.id')),array('instrumen_pernyataan.id'=>$key));
			if ($counter_masalah<11) {
				$pdf->SetAligns(array('C','L','L','C'));
				$pdf->Row(array($counter_masalah,$get_instrumen[0]['pernyataan'],$get_instrumen[0]['aspek'],$sumArray[$key]));
			}

			$counter_masalah++;
		}

		$pdf->SetLeftMargin(12);
		$pdf->Ln(8);
		$pdf->Cell(178,6,'Jakarta, '.konversi_tanggal(date('Y-m-d')),0,0,'R');
		$pdf->Ln();
		$pdf->Cell(178,6,'Pengolah Data',0,0,'R');
		$pdf->Ln(20);
		$pdf->Cell(178,6,getField('user_konselor','nama_lengkap',array('id'=>$get_kelas[0]['konselor_id'])),0,0,'R');

		$pdf->SetTitle('LAPORAN DCM '.$get_kelompok[0]['nama_kelompok'].'.pdf');

		$pdf->Output('I','LAPORAN DCM '.$get_kelompok[0]['nama_kelompok'].'.pdf',FALSE);
	}



	public function laporan_kelas($id=""){
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$get_user[0]['jenjang']));
		$get_kode = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>$get_instrumen[0]['id']));
		$get_data = $this->Main_model->get_where('instrumen_jawaban',array('kelas'=>$id,'instrumen_id'=>$get_kode[0]['id']));
		$get_surat = $this->Main_model->get_where('user_surat',array('user_id'=>$this->session->userdata('id')));
		$get_kelas = $this->Main_model->get_where('kelas',array('id'=>$id));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek',array('instrumen_id'=>$get_instrumen[0]['id']));

		foreach ($get_data as $key => $value) {
			$jawaban = unserialize($value['jawaban']);
			$jawaban_deskriptif = unserialize($value['jawaban_deskriptif']);

			$search = ['Ya'];
			$replace = [1];
			$result[] = str_replace($search, $replace, $jawaban);
			$result_deskriptif[] = $jawaban_deskriptif[223];

			if (@$jawaban_deskriptif[224]=='Ya') {
				$result_konseling[] = $value['nama_lengkap'];
			}

			$value['jawaban'] = unserialize($value['jawaban']);


			if (@$value['jawaban'][6313]=='Ya') {
				$result_bunuh[] = $value['nama_lengkap'];
			}

		}


		$sumArray = array();
		foreach ($result as $k=>$subArray) {
			foreach ($subArray as $id=>$value) {
				@$sumArray[$id]+=$value;
			}
		}

		$skor_masalah = array();
		$i = 0;
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (isset($sumArray[$value['id']])) {
					$skor_masalah[$value_aspek['kode_aspek']]['butir'][] = $sumArray[$value['id']];
				} else {
					$skor_masalah[$value_aspek['kode_aspek']]['butir'][] = 0;
				}
			}
			$skor_masalah[$value_aspek['kode_aspek']]['total_butir'][] = count($get_butir)*count($get_data);
			$persentase_masalah[$i]['persentase'] = (array_sum($skor_masalah[$value_aspek['kode_aspek']]['butir'])/(count($get_butir)*count($get_data)))*100;
			$persentase_masalah[$i]['jumlah_skor'] = array_sum($skor_masalah[$value_aspek['kode_aspek']]['butir']);
			
			$persentase_masalah[$i++]['aspek'] = $value_aspek['aspek'];
		}


		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);
		$pdf->SetFont('Arial','',11);
		if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image(base_url('uploads/logo/'.$get_surat[0]['user_id'].'/'.$get_surat[0]['logo_kiri']),4,10,35,27);
		} else {
			$pdf->Image(base_url('assets/img/logo_iki.png'),8,6,30,27);
		}

		if (@$get_surat[0]['logo_kanan']) {
			$pdf->Image(base_url('uploads/logo/'.$get_surat[0]['user_id'].'/'.$get_surat[0]['logo_kanan']),170,10,35,27);
		} else {
			$pdf->Image(base_url('assets/img/logo_adebk.jpeg'),170,6,30,27);
		}
		$pdf->Ln(1);
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_pertama']),0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_kedua']),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_ketiga']),0,0,'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln();
		}
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(205,6,@$get_surat[0]['baris_keempat'],0,0,'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(4);
			$pdf->Cell(205,6,@$get_surat[0]['baris_kelima'],0,0,'C');
		}

		$pdf->Ln();
		$pdf->SetLineWidth(1);
		$pdf->Line(10,38,200,38);
		$pdf->SetLineWidth(0);
		$pdf->Line(10,39,200,39);
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
        // setting jenis font yang akan digunakan

		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(185,6,'LAPORAN KELOMPOK',0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'INSTRUMEN DAFTAR CEK MASALAH '.$get_user[0]['jenjang'],0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,strtoupper(getField('user_info','instansi',array('id'=>$this->session->userdata('id')))),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'TAHUN AJARAN 2020/2021',0,0,'C');

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(10);
		$pdf->Cell(50,6,'Identitas Kelas/Kelompok',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(30,6,$get_kelas[0]['kelas'],0,0,'L');
		$pdf->Cell(20);
		$pdf->Cell(56,6,'Tanggal Pengadministrasian',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(10,6,date('d/m/Y'),0,0,'L');

		$pdf->Ln();
		$pdf->Cell(50,6,'Jumlah Peserta',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(30,6,$get_kelas[0]['jumlah_siswa'].' Siswa',0,0,'L');
		$pdf->Cell(20);
		$pdf->Cell(56,6,'Jumlah Responden',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(10,6,count($get_data). ' Siswa',0,0,'L');

		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'A. Grafik dan Tabel DCM Setiap Bidang',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50,6,'1. Grafik DCM per Bidang',0,0,'L');

		$bidang_pribadi = array_sum($skor_masalah['KES']['butir'])+array_sum($skor_masalah['KEA']['butir'])+array_sum($skor_masalah['RKH']['butir'])+array_sum($skor_masalah['KHK']['butir'])+array_sum($skor_masalah['AGM']['butir']);
		$bidang_sosial = array_sum($skor_masalah['HPR']['butir'])+array_sum($skor_masalah['KSO']['butir'])+array_sum($skor_masalah['MDI']['butir']);
		$bidang_karir = array_sum($skor_masalah['MDP']['butir']);
		$bidang_belajar = array_sum($skor_masalah['PTS']['butir'])+array_sum($skor_masalah['PTK']['butir']);

		$bidang_total = $bidang_pribadi+$bidang_sosial+$bidang_belajar+$bidang_karir;

		$persentase_pribadi = number_format((float)($bidang_pribadi/$bidang_total)*100, 2, '.', '');
		$persentase_sosial = number_format((float)($bidang_sosial/$bidang_total)*100, 2, '.', '');
		$persentase_belajar = number_format((float)($bidang_belajar/$bidang_total)*100, 2, '.', '');
		$persentase_karir = number_format((float)($bidang_karir/$bidang_total)*100, 2, '.', '');

		$data = array('Pribadi' => $bidang_pribadi, 'Sosial' => $bidang_sosial, 'Belajar' => $bidang_belajar, 'Karir'=> $bidang_karir);

		$pdf->SetFont('Arial', 'BIU', 12);
		$pdf->Ln(8);

		$pdf->SetFont('Arial', '', 10);
		$valX = $pdf->GetX();
		$valY = $pdf->GetY();

		$pdf->SetXY(60, $valY);
		$col1=array(100,100,255);
		$col2=array(255,100,100);
		$col3=array(255,255,100);
		$col4=array(100,100,100);
		$pdf->PieChart(100, 100, $data, '%l (%p)', array($col1,$col2,$col3,$col4));
		$pdf->SetXY($valX, $valY + 40);

		$bidang_array = array();
		$bidang_array[0]['aspek'] = 'Pribadi';
		$bidang_array[1]['aspek'] = 'Sosial';
		$bidang_array[2]['aspek'] = 'Belajar';
		$bidang_array[3]['aspek'] = 'Karir';

		$bidang_array[0]['persentase'] = $bidang_pribadi;
		$bidang_array[1]['persentase'] = $bidang_sosial;
		$bidang_array[2]['persentase'] = $bidang_belajar;
		$bidang_array[3]['persentase'] = $bidang_karir;

		$listBidang = $this->array_sort($bidang_array, 'persentase', SORT_DESC);

		$maxBidang = max(array_column($bidang_array, 'persentase'));
		$minBidang = min(array_column($bidang_array, 'persentase'));

		foreach ($listBidang as $keyBidang => $valueBidang) {
			if ($valueBidang['persentase'] == $minBidang) {
				$bidangMin[] = $valueBidang['aspek'];
			}
		}

		foreach ($listBidang as $keyBidang => $valueBidang) {
			if ($valueBidang['persentase'] == $maxBidang) {
				$bidangMax[] = $valueBidang['aspek'];
			}
		}

		if (count($bidangMin)>1) {
			$last_element_min = array_pop($bidangMin);
			array_push($bidangMin, 'dan '.$last_element_min);
		}
		$textMinBidang = implode(", ",$bidangMin);

		if (count($bidangMax)>1) {
			$last_element_max = array_pop($bidangMin);
			array_push($bidangMax, 'dan '.$last_element_max);
		}
		$textMaxBidang = implode(", ",$bidangMax);

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(20);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Bidang',1,0,'C');
		$pdf->Cell(57,7,'Persentase','R,B,T',0,'C');
		$pdf->Cell(57,7,'Derajat Masalah','R,B,T',1,'C');

		if ($persentase_pribadi <= 20) {
			$keterangan_pribadi = 'A';
		} elseif ($persentase_pribadi > 20 && $persentase_pribadi <= 40){
			$keterangan_pribadi = 'B';
		} elseif ($persentase_pribadi > 40 && $persentase_pribadi <= 60){
			$keterangan_pribadi = 'C';
		} elseif ($persentase_pribadi > 60 && $persentase_pribadi <= 80){
			$keterangan_pribadi = 'D';
		} elseif ($persentase_pribadi > 80 && $persentase_pribadi <= 100){
			$keterangan_pribadi = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Pribadi',1,0,'L');
		$pdf->Cell(57,7,$persentase_pribadi.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_pribadi,'R,B,T',1,'C');

		if ($persentase_sosial <= 20) {
			$keterangan_sosial = 'A';
		} elseif ($persentase_sosial > 20 && $persentase_sosial <= 40){
			$keterangan_sosial = 'B';
		} elseif ($persentase_sosial > 40 && $persentase_sosial <= 60){
			$keterangan_sosial = 'C';
		} elseif ($persentase_sosial > 60 && $persentase_sosial <= 80){
			$keterangan_sosial = 'D';
		} elseif ($persentase_sosial > 80 && $persentase_sosial <= 100){
			$keterangan_sosial = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Sosial',1,0,'L');
		$pdf->Cell(57,7,$persentase_sosial.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_sosial,'R,B,T',1,'C');

		if ($persentase_belajar <= 20) {
			$keterangan_belajar = 'A';
		} elseif ($persentase_belajar > 20 && $persentase_belajar <= 40){
			$keterangan_belajar = 'B';
		} elseif ($persentase_belajar > 40 && $persentase_belajar <= 60){
			$keterangan_belajar = 'C';
		} elseif ($persentase_belajar > 60 && $persentase_belajar <= 80){
			$keterangan_belajar = 'D';
		} elseif ($persentase_belajar > 80 && $persentase_belajar <= 100){
			$keterangan_belajar = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Belajar',1,0,'L');
		$pdf->Cell(57,7,$persentase_belajar.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_belajar,'R,B,T',1,'C');

		if ($persentase_karir <= 20) {
			$keterangan_karir = 'A';
		} elseif ($persentase_karir > 20 && $persentase_karir <= 40){
			$keterangan_karir = 'B';
		} elseif ($persentase_karir > 40 && $persentase_karir <= 60){
			$keterangan_karir = 'C';
		} elseif ($persentase_karir > 60 && $persentase_karir <= 80){
			$keterangan_karir = 'D';
		} elseif ($persentase_karir > 80 && $persentase_karir <= 100){
			$keterangan_karir = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Karir',1,0,'L');
		$pdf->Cell(57,7,$persentase_karir.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_karir,'R,B,T',1,'C');

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan grafik dan tabel diatas dapat diketahui bahwa bidang '.$textMaxBidang.' merupakan permasalahan yang paling banyak dimiliki Peserta Didik. Sementara itu bidang '.$textMinBidang.' merupakan permasalahan yang sedikit dialami Peserta Didik.',1);

		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'B. Grafik dan Tabel DCM per Aspek',0,1,'L');

		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'1. Grafik DCM per Aspek',0,1,'L');

		$textColour = array( 0, 0, 0 );
		$rowLabels = array( "1-20", "21-40", "41-60", "61-80", "81-100", "101-120", "121-140", "141-160", "161-180", "181-200", "201-220");
		$chartXPos = 0;
		$chartYPos = 115;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array( 255, 100, 100 ),
			array( 100, 255, 100 ),
			array( 100, 100, 255 ),
			array( 255, 255, 100 ),
		);

		$data = array(
			array((array_sum($skor_masalah['KES']['butir'])/$skor_masalah['KES']['total_butir'][0])*100),
			array((array_sum($skor_masalah['KEA']['butir'])/$skor_masalah['KEA']['total_butir'][0])*100),
			array((array_sum($skor_masalah['RKH']['butir'])/$skor_masalah['RKH']['total_butir'][0])*100),
			array((array_sum($skor_masalah['KSO']['butir'])/$skor_masalah['KSO']['total_butir'][0])*100),
			array((array_sum($skor_masalah['HPR']['butir'])/$skor_masalah['HPR']['total_butir'][0])*100),
			array((array_sum($skor_masalah['MDI']['butir'])/$skor_masalah['MDI']['total_butir'][0])*100),
			array((array_sum($skor_masalah['KHK']['butir'])/$skor_masalah['KHK']['total_butir'][0])*100),
			array((array_sum($skor_masalah['AGM']['butir'])/$skor_masalah['AGM']['total_butir'][0])*100),
			array((array_sum($skor_masalah['PTS']['butir'])/$skor_masalah['PTS']['total_butir'][0])*100),
			array((array_sum($skor_masalah['MDP']['butir'])/$skor_masalah['MDP']['total_butir'][0])*100),
			array((array_sum($skor_masalah['PTK']['butir'])/$skor_masalah['PTK']['total_butir'][0])*100),
		);

		$pdf->SetFont( 'Arial', '', 12 );


// Compute the X scale
		$xScale = count($rowLabels) / ( $chartWidth - 40 );

// Compute the Y scale

		$maxTotal = 100;

		foreach ( $data as $dataRow ) {
			$totalSales = 0;
			foreach ( $dataRow as $dataCell ) $totalSales += $dataCell;
			$maxTotal = ( $totalSales > $maxTotal ) ? $totalSales : $maxTotal;
		}

		$yScale = $maxTotal / $chartHeight;

// Compute the bar width
		$barWidth = ( 1 / $xScale ) / 1.5;

// Add the axes:

		$pdf->SetFont( 'Arial', '', 9);

// X axis
		$pdf->Line( $chartXPos + 30, $chartYPos, $chartXPos + $chartWidth, $chartYPos );

		for ( $i=0; $i < count( $rowLabels ); $i++ ) {
			$pdf->SetXY( $chartXPos + 40 +  $i / $xScale, $chartYPos );
			$pdf->Cell( $barWidth, 10, $rowLabels[$i], 0, 0, 'C' );
		}

// Y axis
		$pdf->Line( $chartXPos + 30, $chartYPos, $chartXPos + 30, $chartYPos - $chartHeight - 8 );

		for ( $i=0; $i <= $maxTotal; $i ++ ) {
			if ($i % 10 == 0) {
				$pdf->SetXY( $chartXPos + 7, $chartYPos - 5 - $i / $yScale );
				$pdf->Cell( 20, 10, $i, 0, 1, 'R' );
				$pdf->Line( $chartXPos + 28, $chartYPos - $i / $yScale, $chartXPos + 30, $chartYPos - $i / $yScale );
			}
		}

// Create the bars
		$xPos = $chartXPos + 40;
		$bar = 0;

		foreach ( $data as $dataRow ) {

  // Total up the sales figures for this product
			$totalSales = 0;
			foreach ( $dataRow as $dataCell ) $totalSales += $dataCell;

  // Create the bar
			$colourIndex = $bar % count( $chartColours );
			$pdf->SetFillColor( $chartColours[$colourIndex][0], $chartColours[$colourIndex][1], $chartColours[$colourIndex][2] );
			$pdf->Rect( $xPos, $chartYPos - ( $totalSales / $yScale ), $barWidth, $totalSales / $yScale, 'DF' );
			$xPos += ( 1 / $xScale );
			$bar++;
		}

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(85);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 1',1,0,'L');
		$pdf->Cell(25,7,'1-20','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kesehatan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 2',1,0,'L');
		$pdf->Cell(25,7,'21-40','R,B,T',0,'L');
		$pdf->Cell(120,7,'Keadaan Kehidupan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 3',1,0,'L');
		$pdf->Cell(25,7,'41-60','R,B,T',0,'L');
		$pdf->Cell(120,7,'Rekreasi dan Hobi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 4',1,0,'L');
		$pdf->Cell(25,7,'61-80','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kehidupan Sosial - Keaktifan Berorganisasi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 5',1,0,'L');
		$pdf->Cell(25,7,'81-100','R,B,T',0,'L');
		$pdf->Cell(120,7,'Hubungan Pribadi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 6',1,0,'L');
		$pdf->Cell(25,7,'101-120','R,B,T',0,'L');
		$pdf->Cell(120,7,'Muda Mudi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 7',1,0,'L');
		$pdf->Cell(25,7,'121-140','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kehidupan Keluarga','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 8',1,0,'L');
		$pdf->Cell(25,7,'141-160','R,B,T',0,'L');
		$pdf->Cell(120,7,'Agama dan Moral','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 9',1,0,'L');
		$pdf->Cell(25,7,'161-180','R,B,T',0,'L');
		$pdf->Cell(120,7,'Penyesuaian Terhadap Sekolah','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 10',1,0,'L');
		$pdf->Cell(25,7,'181-200','R,B,T',0,'L');
		$pdf->Cell(120,7,'Masa Depan dan Cita-Cita Pendidikan/Jabatan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 11',1,0,'L');
		$pdf->Cell(25,7,'201-220','R,B,T',0,'L');
		$pdf->Cell(120,7,'Penyesuaian Terhadap Kurikulum','R,B,T',1,'L');

		$list = $this->array_sort($persentase_masalah, 'persentase', SORT_DESC);
		$max = max(array_column($persentase_masalah, 'persentase'));
		$min = min(array_column($persentase_masalah, 'persentase'));

		foreach ($list as $key => $value) {
			if ($value['persentase'] == $min) {
				$valueMin[] = $value['aspek'];
			}
		}

		foreach ($list as $key => $value) {
			if ($value['persentase'] == $max) {
				$valueMax[] = $value['aspek'];
			}
		}

		if (count($valueMin)>1) {
			$last_element_min = array_pop($valueMin);
			array_push($valueMin, 'dan '.$last_element_min);
		}
		$textMin = implode(", ",$valueMin);

		if (count($valueMax)>1) {
			$last_element_max = array_pop($valueMax);
			array_push($valueMax, 'dan '.$last_element_max);
		}
		$textMax = implode(", ",$valueMax);

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan Grafik diatas dapat diketahui bahwa aspek '.$textMax.' merupakan permasalahan yang paling banyak dimiliki Peserta Didik. Sementara itu aspek '.$textMin.' merupakan permasalahan yang sedikit dialami Peserta Didik.',1);

		$pdf->AddPage();
		$pdf->Ln(3);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'2. Tabel Analisis DCM per Aspek',0,1,'L');
		$pdf->Ln(2);

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek',1,0,'C');
		$pdf->Cell(30,7,'No Item','R,B,T',0,'C');
		$pdf->Cell(40,7,'Jumlah Skor','R,B,T',0,'C');
		$pdf->Cell(35,7,'Persentase','R,B,T',0,'C');
		$pdf->Cell(40,7,'Keterangan','R,B,T',1,'C');

		$i = 0;
		$no = 1;
		$range1 = 1;
		$range2 = 20;
		foreach ($get_aspek as $key => $value) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));

			if ($persentase_masalah[$i]['persentase'] <= 20) {
				$keterangan = 'A';
			} elseif ($persentase_masalah[$i]['persentase'] > 20 && $persentase_masalah[$i]['persentase'] <= 40){
				$keterangan = 'B';
			} elseif ($persentase_masalah[$i]['persentase'] > 40 && $persentase_masalah[$i]['persentase'] <= 60){
				$keterangan = 'C';
			} elseif ($persentase_masalah[$i]['persentase'] > 60 && $persentase_masalah[$i]['persentase'] <= 80){
				$keterangan = 'C';
			} elseif ($persentase_masalah[$i]['persentase'] > 80 && $persentase_masalah[$i]['persentase'] <= 100){
				$keterangan = 'C';
			}

			$pdf->Cell(6,7,'',0,0,'L');
			$pdf->Cell(25,7,$no++,1,0,'C');
			$pdf->Cell(30,7,$range1.'-'.$range2,'R,B,T',0,'C');
			$pdf->Cell(40,7,$persentase_masalah[$i]['jumlah_skor'],'R,B,T',0,'C');
			$pdf->Cell(35,7,round($persentase_masalah[$i]['persentase'],2)."%",'R,B,T',0,'C');
			$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');
			$i++;
		}

		$pdf->Ln(3);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');
		$pdf->Ln(2);

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'A',1,0,'C');
		$pdf->Cell(50,7,'0-20%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Baik Sekali','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'B',1,0,'C');
		$pdf->Cell(50,7,'21-40%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Baik','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'C',1,0,'C');
		$pdf->Cell(50,7,'41-60%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Cukup','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'D',1,0,'C');
		$pdf->Cell(50,7,'61-80%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Tidak Baik','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'E',1,0,'C');
		$pdf->Cell(50,7,'81-100%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Tidak Sangat Baik','R,B,T',1,'C');


		if ($max <= 20) {
			$keteranganMax = 'A';
		} elseif ($max > 20 && $max <= 40){
			$keteranganMax = 'B';
		} elseif ($max > 40 && $max <= 60){
			$keteranganMax = 'C';
		} elseif ($max > 60 && $max <= 80){
			$keteranganMax = 'D';
		} elseif ($max > 80 && $max <= 100){
			$keteranganMax = 'E';
		}

		if ($min <= 20) {
			$keteranganMin = 'A';
		} elseif ($min > 20 && $min <= 40){
			$keteranganMin = 'B';
		} elseif ($min > 40 && $min <= 60){
			$keteranganMin = 'C';
		} elseif ($min > 60 && $min <= 80){
			$keteranganMin = 'D';
		} elseif ($min > 80 && $min <= 100){
			$keteranganMin = 'E';
		}

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan Tabel diatas dapat diketahui bahwa aspek '.$textMax.' merupakan masalah yang paling banyak dialami peserta didik dengan derajat masalah '.$keteranganMax.'. Sementara itu '.$textMin.' merupakan permasalah yang sedikit dialami peserta didik dengan derajat masalah '.$keteranganMin.'.',1);

		$pdf->Ln(8);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'C. 10 Butir Pernyataan Populer',0,1,'L');
		$pdf->SetWidths(array(10,90,50,20));
		srand(microtime()*1000000);
		$pdf->Cell(8,7,'',0,0,'L');
		$pdf->SetAligns(array('C','C','C','C'));
		$pdf->Row(array('No','Pernyataan','Bidang','%'));
		$pdf->SetFont('Arial','',12);

		$highestScore = $this->getHighestValueOfArray($sumArray, 10);
		$counter_masalah = 1;

		$pdf->SetLeftMargin(18);
		foreach ($highestScore as $key => $value) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('id'=>$key));
			foreach ($get_butir as $value_butir) {
				$get_aspek = $this->Main_model->get_where('instrumen_aspek',array('id'=>$value_butir['aspek_id']));
				if ($counter_masalah<11) {
					$pdf->SetAligns(array('C','L','L','C'));
					$pdf->Row(array($counter_masalah,ucfirst(strtolower($value_butir['pernyataan'])),$get_aspek[0]['aspek'],round(((intval($value)/count($get_data))*100),2).'%'));
				}

				$counter_masalah++;
			}
		}

		$pdf->SetLeftMargin(10);
		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'D. Bersedia Konseling',0,1,'L');
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Siswa yang ingin melakukan konseling ada '.count($result_konseling).' orang, dengan nama sebagai berikut :',0,1,'L');

		$no_konseling = 1;

		foreach ($result_konseling as $key => $value) {
			$pdf->Cell(5,7,'',0,0,'L');
			$pdf->Cell(35,7,$no_konseling++.'. '.$value,0,1,'L');
		}

		$pdf->Cell(35,7,'E. Potensi Bunuh Diri',0,1,'L');
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Siswa yang berpotensi bunuh diri ada '.count($result_bunuh).' orang, dengan nama sebagai berikut :',0,1,'L');

		$no_bundir = 1;

		foreach ($result_bunuh as $key => $value) {
			$pdf->Cell(5,7,'',0,0,'L');
			$pdf->Cell(35,7,$no_bundir++.'. '.$value,0,1,'L');
		}


		$pdf->SetLeftMargin(12);
		$pdf->Ln(8);
		$pdf->Cell(178,6,'Jakarta, '.konversi_tanggal(date('Y-m-d')),0,0,'R');
		$pdf->Ln();
		$pdf->Cell(178,6,'Pengolah Data',0,0,'R');
		$pdf->Ln(20);
		$pdf->Cell(178,6,getField('user_konselor','nama_lengkap',array('id'=>$get_kelas[0]['konselor_id'])),0,0,'R');
		
		$pdf->SetTitle('LAPORAN DCM '.$get_kelas[0]['kelas'].'.pdf');

		$pdf->Output('I','LAPORAN DCM '.$get_kelas[0]['kelas'].'.pdf',FALSE);
	}

	public function laporan_individu($id=""){
		$this->load->library('fpdf_diag');
		$get_profil = $this->Main_model->get_where('instrumen_jawaban',array('id'=>$id));
		$get_kelas = $this->Main_model->get_where('kelas',array('id'=>$get_profil[0]['kelas']));
		$get_user = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$get_surat = $this->Main_model->get_where('user_surat',array('user_id'=>$this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'DCM','jenjang'=>$get_user[0]['jenjang']));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek',array('instrumen_id'=>$get_instrumen[0]['id']));
		$jawaban = unserialize($get_profil[0]['jawaban']);
		$jawaban_deskriptif = unserialize($get_profil[0]['jawaban_deskriptif']);

		$pdf = new PDF_Diag();
		$pdf->AddPage();
		$pdf->SetLeftMargin(0);
		$pdf->SetFont('Arial','',11);
		if (@$get_surat[0]['logo_kiri']) {
			$pdf->Image(base_url('uploads/logo/'.$get_surat[0]['user_id'].'/'.$get_surat[0]['logo_kiri']),4,10,35,27);
		} else {
			$pdf->Image(base_url('assets/img/logo_iki.png'),8,6,30,27);
		}

		if (@$get_surat[0]['logo_kanan']) {
			$pdf->Image(base_url('uploads/logo/'.$get_surat[0]['user_id'].'/'.$get_surat[0]['logo_kanan']),170,10,35,27);
		} else {
			$pdf->Image(base_url('assets/img/logo_adebk.jpeg'),170,6,30,27);
		}
		$pdf->Ln(1);
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_pertama']),0,0,'C');
		$pdf->Ln();
		$pdf->SetFont('Arial','B',11);
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_kedua']),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(205,6,strtoupper(@$get_surat[0]['baris_ketiga']),0,0,'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(5);
		} else {
			$pdf->Ln();
		}
		$pdf->SetFont('Arial','',10);
		$pdf->Cell(205,6,@$get_surat[0]['baris_keempat'],0,0,'C');
		if (@$get_surat[0]['baris_kelima']) {
			$pdf->Ln(4);
			$pdf->Cell(205,6,@$get_surat[0]['baris_kelima'],0,0,'C');
		}

		$pdf->Ln();
		$pdf->SetLineWidth(1);
		$pdf->Line(10,38,200,38);
		$pdf->SetLineWidth(0);
		$pdf->Line(10,39,200,39);
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

        // setting jenis font yang akan digunakan
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'Rahasia',1,0,'C');
		$pdf->Cell(3);
		if (@$jawaban[6313]) {
			$pdf->Cell(50,7,'Potensi Bunuh Diri',1,0,'C');
			$pdf->Cell(3);
		}

		if (@$jawaban_deskriptif[224]=='Ya') {
			$pdf->Cell(50,7,'Bersedia Konseling',1,1,'C');
		}

		$pdf->Ln(10);
		$pdf->Cell(185,6,'LAPORAN INDIVIDU',0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'DAFTAR CEK MASALAH '.$get_user[0]['jenjang'],0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,strtoupper(getField('user_info','instansi',array('id'=>$this->session->userdata('id')))),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'TAHUN AJARAN 2020/2021',0,0,'C');

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(10);
		$pdf->Cell(50,6,'Nama Siswa',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(30,6,$get_profil[0]['nama_lengkap'],0,0,'L');
		$pdf->Cell(20);
		$pdf->Cell(56,6,'Jenis Kelamin',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(10,6,$get_profil[0]['jenis_kelamin'],0,0,'L');

		$pdf->Ln();
		$pdf->Cell(50,6,'Kelas',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(30,6,getField('kelas','kelas',array('id'=>$get_profil[0]['kelas'])),0,0,'L');
		$pdf->Cell(20);
		$pdf->Cell(56,6,'Tanggal Pengisian',0,0,'L');
		$pdf->Cell(2,6,':',0,0,'C');
		$pdf->Cell(10,6,date('d-m-Y',strtotime($get_profil[0]['date_created'])),0,0,'L');

		$pdf->Ln(8);

		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'A. Grafik dan Tabel DCM per Bidang',0,1,'L');

		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'1. Grafik DCM per Bidang',0,1,'L');

		$i = 0;
		foreach ($get_aspek as $value_aspek) {
			$array_no_masalah = array();
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (isset($jawaban[$value['id']])) {
					$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}
			}

			$skor_masalah[] = count(@$array_no_masalah[$value_aspek['kode_aspek']]);
			$persentase_masalah[$i]['persentase'] = round((count(@$array_no_masalah[$value_aspek['kode_aspek']])/count($get_butir))*100,2);
			$persentase_masalah[$i]['aspek'] = $value_aspek['aspek'];
			$persentase_masalah[$i++]['jumlah_skor'] = count(@$array_no_masalah[$value_aspek['kode_aspek']]);

		}


		$bidang_pribadi = $skor_masalah[0]+$skor_masalah[1]+$skor_masalah[2]+$skor_masalah[6]+$skor_masalah[7];
		$bidang_sosial = $skor_masalah[4]+$skor_masalah[3]+$skor_masalah[5];
		$bidang_karir = $skor_masalah[9];
		$bidang_belajar = $skor_masalah[8]+$skor_masalah[10];

		$bidang_total = $bidang_pribadi+$bidang_sosial+$bidang_belajar+$bidang_karir;

		if ($bidang_total==0) {
			$persentase_pribadi = 0;
			$persentase_sosial = 0;
			$persentase_belajar = 0;
			$persentase_karir = 0;
		} else {
			$persentase_pribadi = number_format((float)($bidang_pribadi/$bidang_total)*100, 2, '.', '');
			$persentase_sosial = number_format((float)($bidang_sosial/$bidang_total)*100, 2, '.', '');
			$persentase_belajar = number_format((float)($bidang_belajar/$bidang_total)*100, 2, '.', '');
			$persentase_karir = number_format((float)($bidang_karir/$bidang_total)*100, 2, '.', '');
		}


		$data = array('Pribadi' => $bidang_pribadi, 'Sosial' => $bidang_sosial, 'Belajar' => $bidang_belajar, 'Karir'=> $bidang_karir);

		$pdf->SetFont('Arial', 'BIU', 12);
		$pdf->Ln(8);

		$pdf->SetFont('Arial', '', 10);
		$valX = $pdf->GetX();
		$valY = $pdf->GetY();


		if ($bidang_total!=0) {
			$pdf->SetXY(60, $valY);
			$col1=array(100,100,255);
			$col2=array(255,100,100);
			$col3=array(255,255,100);
			$col4=array(100,100,100);
			$pdf->PieChart(100, 100, $data, '%l (%p)', array($col1,$col2,$col3,$col4));
			$pdf->SetXY($valX, $valY + 40);
		}
		

		$bidang_array = array();
		$bidang_array[0]['aspek'] = 'Pribadi';
		$bidang_array[1]['aspek'] = 'Sosial';
		$bidang_array[2]['aspek'] = 'Belajar';
		$bidang_array[3]['aspek'] = 'Karir';

		$bidang_array[0]['persentase'] = $bidang_pribadi;
		$bidang_array[1]['persentase'] = $bidang_sosial;
		$bidang_array[2]['persentase'] = $bidang_belajar;
		$bidang_array[3]['persentase'] = $bidang_karir;

		$listBidang = $this->array_sort($bidang_array, 'persentase', SORT_DESC);

		$maxBidang = max(array_column($bidang_array, 'persentase'));
		$minBidang = min(array_column($bidang_array, 'persentase'));

		foreach ($listBidang as $keyBidang => $valueBidang) {
			if ($valueBidang['persentase'] == $minBidang) {
				$bidangMin[] = $valueBidang['aspek'];
			}
		}

		foreach ($listBidang as $keyBidang => $valueBidang) {
			if ($valueBidang['persentase'] == $maxBidang) {
				$bidangMax[] = $valueBidang['aspek'];
			}
		}

		if (count($bidangMin)>1) {
			$last_element_min = array_pop($bidangMin);
			array_push($bidangMin, 'dan '.$last_element_min);
		}
		$textMinBidang = implode(", ",$bidangMin);

		if (count($bidangMax)>1) {
			$last_element_max = array_pop($bidangMin);
			array_push($bidangMax, 'dan '.$last_element_max);
		}
		$textMaxBidang = implode(", ",$bidangMax);
		
		$data = array(
			array($bidang_pribadi),
			array($bidang_sosial),
			array($bidang_belajar),
			array($bidang_karir),
		);

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(20);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Bidang',1,0,'C');
		$pdf->Cell(57,7,'Persentase','R,B,T',0,'C');
		$pdf->Cell(57,7,'Derajat Masalah','R,B,T',1,'C');

		if ($persentase_pribadi <= 20) {
			$keterangan_pribadi = 'A';
		} elseif ($persentase_pribadi > 20 && $persentase_pribadi <= 40){
			$keterangan_pribadi = 'B';
		} elseif ($persentase_pribadi > 40 && $persentase_pribadi <= 60){
			$keterangan_pribadi = 'C';
		} elseif ($persentase_pribadi > 60 && $persentase_pribadi <= 80){
			$keterangan_pribadi = 'D';
		} elseif ($persentase_pribadi > 80 && $persentase_pribadi <= 100){
			$keterangan_pribadi = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Pribadi',1,0,'L');
		$pdf->Cell(57,7,$persentase_pribadi.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_pribadi,'R,B,T',1,'C');

		if ($persentase_sosial <= 20) {
			$keterangan_sosial = 'A';
		} elseif ($persentase_sosial > 20 && $persentase_sosial <= 40){
			$keterangan_sosial = 'B';
		} elseif ($persentase_sosial > 40 && $persentase_sosial <= 60){
			$keterangan_sosial = 'C';
		} elseif ($persentase_sosial > 60 && $persentase_sosial <= 80){
			$keterangan_sosial = 'D';
		} elseif ($persentase_sosial > 80 && $persentase_sosial <= 100){
			$keterangan_sosial = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Sosial',1,0,'L');
		$pdf->Cell(57,7,$persentase_sosial.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_sosial,'R,B,T',1,'C');

		if ($persentase_belajar <= 20) {
			$keterangan_belajar = 'A';
		} elseif ($persentase_belajar > 20 && $persentase_belajar <= 40){
			$keterangan_belajar = 'B';
		} elseif ($persentase_belajar > 40 && $persentase_belajar <= 60){
			$keterangan_belajar = 'C';
		} elseif ($persentase_belajar > 60 && $persentase_belajar <= 80){
			$keterangan_belajar = 'D';
		} elseif ($persentase_belajar > 80 && $persentase_belajar <= 100){
			$keterangan_belajar = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Belajar',1,0,'L');
		$pdf->Cell(57,7,$persentase_belajar.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_belajar,'R,B,T',1,'C');

		if ($persentase_karir <= 20) {
			$keterangan_karir = 'A';
		} elseif ($persentase_karir > 20 && $persentase_karir <= 40){
			$keterangan_karir = 'B';
		} elseif ($persentase_karir > 40 && $persentase_karir <= 60){
			$keterangan_karir = 'C';
		} elseif ($persentase_karir > 60 && $persentase_karir <= 80){
			$keterangan_karir = 'D';
		} elseif ($persentase_karir > 80 && $persentase_karir <= 100){
			$keterangan_karir = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(57,7,'Karir',1,0,'L');
		$pdf->Cell(57,7,$persentase_karir.'%','R,B,T',0,'C');
		$pdf->Cell(57,7,$keterangan_karir,'R,B,T',1,'C');

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan grafik dan tabel diatas dapat diketahui bahwa bidang '.$textMaxBidang.' merupakan permasalahan yang paling banyak dimiliki Peserta Didik. Sementara itu bidang '.$textMinBidang.' merupakan permasalahan yang sedikit dialami Peserta Didik.',1);

		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'B. Grafik dan Tabel DCM per Aspek',0,1,'L');

		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'1. Grafik DCM per Aspek',0,1,'L');

		$textColour = array( 0, 0, 0 );
		$rowLabels = array( "1-20", "21-40", "41-60", "61-80", "81-100", "101-120", "121-140", "141-160", "161-180", "181-200", "201-220");
		$chartXPos = 0;
		$chartYPos = 115;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array( 255, 100, 100 ),
			array( 100, 255, 100 ),
			array( 100, 100, 255 ),
			array( 255, 255, 100 ),
		);

		$data = array(
			array($persentase_masalah[0]['persentase']),
			array($persentase_masalah[1]['persentase']),
			array($persentase_masalah[2]['persentase']),
			array($persentase_masalah[3]['persentase']),
			array($persentase_masalah[4]['persentase']),
			array($persentase_masalah[5]['persentase']),
			array($persentase_masalah[6]['persentase']),
			array($persentase_masalah[7]['persentase']),
			array($persentase_masalah[8]['persentase']),
			array($persentase_masalah[9]['persentase']),
			array($persentase_masalah[10]['persentase']),
		);


		$pdf->SetFont( 'Arial', '', 12 );


// Compute the X scale
		$xScale = count($rowLabels) / ( $chartWidth - 40 );

// Compute the Y scale

		$maxTotal = 100;

		foreach ( $data as $dataRow ) {
			$totalSales = 0;
			foreach ( $dataRow as $dataCell ) $totalSales += $dataCell;
			$maxTotal = ( $totalSales > $maxTotal ) ? $totalSales : $maxTotal;
		}

		$yScale = $maxTotal / $chartHeight;

// Compute the bar width
		$barWidth = ( 1 / $xScale ) / 1.5;

// Add the axes:

		$pdf->SetFont( 'Arial', '', 9);

// X axis
		$pdf->Line( $chartXPos + 30, $chartYPos, $chartXPos + $chartWidth, $chartYPos );

		for ( $i=0; $i < count( $rowLabels ); $i++ ) {
			$pdf->SetXY( $chartXPos + 40 +  $i / $xScale, $chartYPos );
			$pdf->Cell( $barWidth, 10, $rowLabels[$i], 0, 0, 'C' );
		}

// Y axis
		$pdf->Line( $chartXPos + 30, $chartYPos, $chartXPos + 30, $chartYPos - $chartHeight - 8 );

		for ( $i=0; $i <= $maxTotal; $i ++ ) {
			if ($i % 10 == 0) {
				$pdf->SetXY( $chartXPos + 7, $chartYPos - 5 - $i / $yScale );
				$pdf->Cell( 20, 10, $i, 0, 1, 'R' );
				$pdf->Line( $chartXPos + 28, $chartYPos - $i / $yScale, $chartXPos + 30, $chartYPos - $i / $yScale );
			}
		}

// Create the bars
		$xPos = $chartXPos + 40;
		$bar = 0;

		foreach ( $data as $dataRow ) {

  // Total up the sales figures for this product
			$totalSales = 0;
			foreach ( $dataRow as $dataCell ) $totalSales += $dataCell;

  // Create the bar
			$colourIndex = $bar % count( $chartColours );
			$pdf->SetFillColor( $chartColours[$colourIndex][0], $chartColours[$colourIndex][1], $chartColours[$colourIndex][2] );
			$pdf->Rect( $xPos, $chartYPos - ( $totalSales / $yScale ), $barWidth, $totalSales / $yScale, 'DF' );
			$xPos += ( 1 / $xScale );
			$bar++;
		}

		$listAspek = $this->array_sort($persentase_masalah, 'persentase', SORT_DESC);

		$maxAspek = max(array_column($persentase_masalah, 'persentase'));
		$minAspek = min(array_column($persentase_masalah, 'persentase'));

		foreach ($listAspek as $keyAspek => $valueAspek) {
			if ($valueAspek['persentase'] == $minAspek) {
				$aspekMin[] = $valueAspek['aspek'];
			}
		}

		foreach ($listAspek as $keyAspek => $valueAspek) {
			if ($valueAspek['persentase'] == $maxAspek) {
				$aspekMax[] = $valueAspek['aspek'];
			}
		}

		if (count($aspekMin)>1) {
			$last_element_min = array_pop($aspekMin);
			array_push($aspekMin, 'dan '.$last_element_min);
		}
		$textMinAspek = implode(", ",$aspekMin);

		if (count($aspekMax)>1) {
			$last_element_max = array_pop($aspekMin);
			array_push($aspekMax, 'dan '.$last_element_max);
		}
		$textMaxAspek = implode(", ",$aspekMax);

		$pdf->SetFont('Arial','',12);
		$pdf->Ln(85);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 1',1,0,'L');
		$pdf->Cell(25,7,'1-20','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kesehatan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 2',1,0,'L');
		$pdf->Cell(25,7,'21-40','R,B,T',0,'L');
		$pdf->Cell(120,7,'Keadaan Kehidupan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 3',1,0,'L');
		$pdf->Cell(25,7,'41-60','R,B,T',0,'L');
		$pdf->Cell(120,7,'Rekreasi dan Hobi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 4',1,0,'L');
		$pdf->Cell(25,7,'61-80','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kehidupan Sosial - Keaktifan Berorganisasi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 5',1,0,'L');
		$pdf->Cell(25,7,'81-100','R,B,T',0,'L');
		$pdf->Cell(120,7,'Hubungan Pribadi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 6',1,0,'L');
		$pdf->Cell(25,7,'101-120','R,B,T',0,'L');
		$pdf->Cell(120,7,'Muda Mudi','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 7',1,0,'L');
		$pdf->Cell(25,7,'121-140','R,B,T',0,'L');
		$pdf->Cell(120,7,'Kehidupan Keluarga','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 8',1,0,'L');
		$pdf->Cell(25,7,'141-160','R,B,T',0,'L');
		$pdf->Cell(120,7,'Agama dan Moral','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 9',1,0,'L');
		$pdf->Cell(25,7,'161-180','R,B,T',0,'L');
		$pdf->Cell(120,7,'Penyesuaian Terhadap Sekolah','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 10',1,0,'L');
		$pdf->Cell(25,7,'181-200','R,B,T',0,'L');
		$pdf->Cell(120,7,'Masa Depan dan Cita-Cita Pendidikan/Jabatan','R,B,T',1,'L');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek 11',1,0,'L');
		$pdf->Cell(25,7,'201-220','R,B,T',0,'L');
		$pdf->Cell(120,7,'Penyesuaian Terhadap Kurikulum','R,B,T',1,'L');
		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan Grafik diatas dapat diketahui bahwa aspek '.$textMaxAspek.' merupakan permasalahan yang paling banyak dimiliki Peserta Didik. Sementara itu aspek '.$textMinAspek.' merupakan permasalahan yang sedikit dialami Peserta Didik.',1);

		$pdf->AddPage();

		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'2. Tabel Analisis DCM per Aspek',0,1,'L');
		$pdf->Ln(2);

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'Aspek',1,0,'C');
		$pdf->Cell(30,7,'No Item','R,B,T',0,'C');
		$pdf->Cell(40,7,'Jumlah Skor','R,B,T',0,'C');
		$pdf->Cell(35,7,'Persentase','R,B,T',0,'C');
		$pdf->Cell(40,7,'Keterangan','R,B,T',1,'C');

		if ($persentase_masalah[0]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[0]['persentase'] > 20 && $persentase_masalah[0]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[0]['persentase'] > 40 && $persentase_masalah[0]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[0]['persentase'] > 60 && $persentase_masalah[0]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[0]['persentase'] > 80 && $persentase_masalah[0]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'1',1,0,'C');
		$pdf->Cell(30,7,'1-20','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[0]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[0]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[1]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[1]['persentase'] > 20 && $persentase_masalah[1]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[1]['persentase'] > 40 && $persentase_masalah[1]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[1]['persentase'] > 60 && $persentase_masalah[1]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[1]['persentase'] > 80 && $persentase_masalah[1]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'2',1,0,'C');
		$pdf->Cell(30,7,'21-40','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[1]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[1]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[2]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[2]['persentase'] > 20 && $persentase_masalah[2]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[2]['persentase'] > 40 && $persentase_masalah[2]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[2]['persentase'] > 60 && $persentase_masalah[2]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[2]['persentase'] > 80 && $persentase_masalah[2]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'3',1,0,'C');
		$pdf->Cell(30,7,'41-60','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[2]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[2]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[3]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[3]['persentase'] > 20 && $persentase_masalah[3]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[3]['persentase'] > 40 && $persentase_masalah[3]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[3]['persentase'] > 60 && $persentase_masalah[3]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[3]['persentase'] > 80 && $persentase_masalah[3]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'4',1,0,'C');
		$pdf->Cell(30,7,'61-80','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[3]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[3]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[4]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[4]['persentase'] > 20 && $persentase_masalah[4]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[4]['persentase'] > 40 && $persentase_masalah[4]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[4]['persentase'] > 60 && $persentase_masalah[4]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[4]['persentase'] > 80 && $persentase_masalah[4]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'5',1,0,'C');
		$pdf->Cell(30,7,'81-100','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[4]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[4]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[5]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[5]['persentase'] > 20 && $persentase_masalah[5]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[5]['persentase'] > 40 && $persentase_masalah[5]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[5]['persentase'] > 60 && $persentase_masalah[5]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[5]['persentase'] > 80 && $persentase_masalah[5]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'6',1,0,'C');
		$pdf->Cell(30,7,'101-120','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[5]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[5]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[6]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[6]['persentase'] > 20 && $persentase_masalah[6]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[6]['persentase'] > 40 && $persentase_masalah[6]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[6]['persentase'] > 60 && $persentase_masalah[6]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[6]['persentase'] > 80 && $persentase_masalah[6]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'7',1,0,'C');
		$pdf->Cell(30,7,'121-140','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[6]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[6]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[7]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[7]['persentase'] > 20 && $persentase_masalah[7]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[7]['persentase'] > 40 && $persentase_masalah[7]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[7]['persentase'] > 60 && $persentase_masalah[7]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[7]['persentase'] > 80 && $persentase_masalah[7]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'8',1,0,'C');
		$pdf->Cell(30,7,'141-160','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[7]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[7]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[8]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[8]['persentase'] > 20 && $persentase_masalah[8]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[8]['persentase'] > 40 && $persentase_masalah[8]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[8]['persentase'] > 60 && $persentase_masalah[8]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[8]['persentase'] > 80 && $persentase_masalah[8]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'9',1,0,'C');
		$pdf->Cell(30,7,'161-180','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[8]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[8]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[9]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[9]['persentase'] > 20 && $persentase_masalah[9]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[9]['persentase'] > 40 && $persentase_masalah[9]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[9]['persentase'] > 60 && $persentase_masalah[9]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[9]['persentase'] > 80 && $persentase_masalah[9]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'10',1,0,'C');
		$pdf->Cell(30,7,'181-200','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[9]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[9]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		if ($persentase_masalah[10]['persentase'] <= 20) {
			$keterangan = 'A';
		} elseif ($persentase_masalah[10]['persentase'] > 20 && $persentase_masalah[10]['persentase'] <= 40){
			$keterangan = 'B';
		} elseif ($persentase_masalah[10]['persentase'] > 40 && $persentase_masalah[10]['persentase'] <= 60){
			$keterangan = 'C';
		} elseif ($persentase_masalah[10]['persentase'] > 60 && $persentase_masalah[10]['persentase'] <= 80){
			$keterangan = 'D';
		} elseif ($persentase_masalah[10]['persentase'] > 80 && $persentase_masalah[10]['persentase'] <= 100){
			$keterangan = 'E';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(25,7,'11',1,0,'C');
		$pdf->Cell(30,7,'201-220','R,B,T',0,'C');
		$pdf->Cell(40,7,$persentase_masalah[10]['jumlah_skor'],'R,B,T',0,'C');
		$pdf->Cell(35,7,$persentase_masalah[10]['persentase']."%",'R,B,T',0,'C');
		$pdf->Cell(40,7,$keterangan,'R,B,T',1,'C');

		$pdf->Ln(3);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'Keterangan :',0,1,'L');
		$pdf->Ln(2);

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'A',1,0,'C');
		$pdf->Cell(50,7,'0-20%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Baik Sekali','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'B',1,0,'C');
		$pdf->Cell(50,7,'21-40%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Baik','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'C',1,0,'C');
		$pdf->Cell(50,7,'41-60%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Cukup','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'D',1,0,'C');
		$pdf->Cell(50,7,'61-80%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Tidak Baik','R,B,T',1,'C');

		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(30,7,'E',1,0,'C');
		$pdf->Cell(50,7,'81-100%','R,B,T',0,'C');
		$pdf->Cell(90,7,'Tidak Sangat Baik','R,B,T',1,'C');

		$list = $this->array_sort($persentase_masalah, 'persentase', SORT_DESC);

		$rekapFixSort = [];
		foreach ($list as $key => $value) {
			$rekapFixSort[] = [
				"aspek" => $value['aspek'],
				"jumlah_skor"=> $value['jumlah_skor'],
				"persentase" => $value['persentase'],
			];
		}

		$max = max(array_column($persentase_masalah, 'persentase'));
		$min = min(array_column($persentase_masalah, 'persentase'));

		if ($max <= 20) {
			$keteranganMax = 'A';
		} elseif ($max > 20 && $max <= 40){
			$keteranganMax = 'B';
		} elseif ($max > 40 && $max <= 60){
			$keteranganMax = 'C';
		} elseif ($max > 60 && $max <= 80){
			$keteranganMax = 'D';
		} elseif ($max > 80 && $max <= 100){
			$keteranganMax = 'E';
		}

		if ($min <= 20) {
			$keteranganMin = 'A';
		} elseif ($min > 20 && $min <= 40){
			$keteranganMin = 'B';
		} elseif ($min > 40 && $min <= 60){
			$keteranganMin = 'C';
		} elseif ($min > 60 && $min <= 80){
			$keteranganMin = 'D';
		} elseif ($min > 80 && $min <= 100){
			$keteranganMin = 'E';
		}

		foreach ($list as $key => $value) {
			if ($value['persentase'] == $min) {
				$valueMin[] = $value['aspek'];
			}
		}

		foreach ($list as $key => $value) {
			if ($value['persentase'] == $max) {
				$valueMax[] = $value['aspek'];
			}
		}

		if (count($valueMin)>1) {
			$last_element_min = array_pop($valueMin);
			array_push($valueMin, 'dan '.$last_element_min);
		}
		$textMin = implode(", ",$valueMin);

		if (count($valueMax)>1) {
			$last_element_max = array_pop($valueMax);
			array_push($valueMax, 'dan '.$last_element_max);
		}
		$textMax = implode(", ",$valueMax);

		$pdf->Ln(5);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(170,7,'Analisis : Berdasarkan Tabel diatas dapat diketahui bahwa aspek '.$textMax.' merupakan masalah yang paling banyak dialami peserta didik dengan derajat masalah '.$keteranganMax.'. Sementara itu '.$textMin.' merupakan permasalah yang sedikit dialami peserta didik dengan derajat masalah '.$keteranganMin,1);

		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',12);

		$pdf->Cell(35,7,'C. Deskripsi Masalah Peserta Didik',0,1,'L');
		$pdf->SetFont('Arial','',12);

		if ($jawaban_deskriptif['225']==1) {
			$keterangan = 'Mendesak dan ingin saya selesaikan segera';
		} elseif ($jawaban_deskriptif['225']==2){
			$keterangan = 'Mendesak namun ingin saya selesaikan dengan perlahan';
		} elseif ($jawaban_deskriptif['225']==3){
			$keterangan = 'Tidak mendesak tapi ingin saya selesaikan segera';
		} elseif ($jawaban_deskriptif['225']==4){
			$keterangan = 'Tidak mendesak dan ingin saya selesaikan dengan perlahan';
		}

		$pdf->Cell(6,7,'',0,0,'L');
		if ($jawaban_deskriptif['224']=='Ya') {
			$pdf->MultiCell(165,7,$jawaban_deskriptif['222'].". Permasalahan ini ".strtolower($keterangan).'. Saya suka meluangkan waktu untuk membicarakan masalah saya dan saya biasanya membicarakan masalah saya dengan '.strtolower($jawaban_deskriptif['223']),0);
		} elseif ($jawaban_deskriptif['224']=='Tidak'){
			$pdf->MultiCell(165,7,$jawaban_deskriptif['222'].". Permasalahan ini ".strtolower($keterangan).'. Saya tidak suka meluangkan waktu untuk membicarakan masalah saya.',0);
		} 	

		$pdf->Ln();
		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(35,7,'D. Jawaban Peserta Didik',0,1,'L');
		$pdf->SetFont('Arial','',12);
		$pdf->Cell(5,7,'',0,0,'L');
		$pdf->Cell(35,7,'1. Item Masalah Peserta Didik',0,1,'L');

		
		$x = 'a';
		foreach ($get_aspek as $value_aspek) {
			$array_no_masalah = array();
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));

			foreach ($get_butir as $key => $value) {
				if (isset($jawaban[$value['id']])) {
					$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}
			}

			$pdf->Cell(10,7,'',0,0,'L');
			$pdf->Cell(50,7,$x++.'. '.$value_aspek['aspek'],0,1,'L');

			$pdf->Cell(15,7,'',0,0,'L');
			$pdf->MultiCell(161,7,'Pada bidang ini, subjek berada pada kategori rata-rata dengan persentase sebesar '.round((count(@$array_no_masalah[$value_aspek['kode_aspek']])/count($get_butir))*100,2).'%. Butir-butir yang subjek pilih dalam aspek '.$value_aspek['aspek'].', yaitu :',0);
			
			$i = 1;
			foreach ($get_butir as $key => $value) {
				if (isset($jawaban[$value['id']])) {
					$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];

					$pdf->Cell(15,7,'',0,0,'L');
					$pdf->MultiCell(161,7,$i++.'. '.ucfirst(strtolower($value['pernyataan'])),0);
				}
			}
			
		}		

		$pdf->Cell(176,6,'Jakarta, '.konversi_tanggal(date('Y-m-d')),0,0,'R');
		$pdf->Ln();
		$pdf->Cell(176,6,'Pengolah Data',0,0,'R');
		$pdf->Ln(20);
		$pdf->Cell(176,6,getField('user_konselor','nama_lengkap',array('id'=>$get_kelas[0]['konselor_id'])),0,0,'R');

		$pdf->SetTitle('LAPORAN DCM '.getField('kelas','kelas',array('id'=>$get_profil[0]['kelas'])).' - '.$get_profil[0]['nama_lengkap'].'.pdf');
		
		$pdf->Output('I','LAPORAN DCM '.getField('kelas','kelas',array('id'=>$get_profil[0]['kelas'])).' - '.$get_profil[0]['nama_lengkap'].'.pdf',FALSE);
	}

	protected function getHighestValueOfArray($data, $range)
	{
		// Sorting index array asc
		ksort($data);
		
		// Bentuk sebuah array yang membentuk grup data berdasarkan value
		// Yg akan menjadi index array nya adalah si value, dan index akan di kelompokan berdasarkan value nya
		foreach ($data as $k => $value) {
			$groups[$value][] = $k;
		}

		// Sorting DESC array yg barusan di buat
		krsort($groups);

		// Susun kembali array yg sebelumnya di kelompokan
		foreach ($groups as $value => $group) {
			foreach ($group as $key) {
				$sorted[$key] = $value;
			}
		}

		// Ambil sejumlah data sesuai dengan range yg di berikan
		$newData = array_slice($sorted, 0, $range, true);

		return $newData;
	}

}

/* End of file Aum.php */
/* Location: ./application/controllers/Aum.php */