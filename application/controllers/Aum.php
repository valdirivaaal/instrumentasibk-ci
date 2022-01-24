<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aum extends CI_Controller {

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

	public function index($jenjang="")
	{
		$this->load->library('encrypt');
		$get_ticket = $this->Main_model->get_where('ticket',array('user_id'=>$this->session->userdata('id')));
		$data['get_profil'] = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));

		if ($jenjang) {
			$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$jenjang));
		} else {
			$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$data['get_profil'][0]['jenjang']));
		}


		$data['get_kelompok'] = $this->Main_model->get_where('kelompok',array('user_id'=>$this->session->userdata('id')));
		
		$data['get_aum'] = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>$get_instrumen[0]['id']));

		$data['get_kode'] = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>@$data['get_aum'][0]['instrumen_id']));
		
		$data['jenjang'] =  $jenjang;

		if ($get_ticket) {
			if ($jenjang) {
				$data['kelas'] = $this->Main_model->get_where('kelas',array('user_id'=>$this->session->userdata('id'),'jenjang'=>$jenjang),'kelas','asc');
			} else {
				$data['kelas'] = $this->Main_model->get_where('kelas',array('user_id'=>$this->session->userdata('id')),'kelas','asc');
			}
			$data['content'] = 'aum.php';
		} else {
			$data['content'] = 'key';
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
		} elseif ($data['get_profil'][0]['jenjang']=='Konselor'){
			$get_kelas = $this->Main_model->get_where('kelas',array('id'=>$id));
			$data['get_profil'][0]['jenjang'] = $get_kelas[0]['jenjang'];
		}

		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$data['get_profil'][0]['jenjang']));

		$get_aum = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>$get_instrumen[0]['id']));
		
		$data['get_jawaban'] = $this->Main_model->get_where('instrumen_jawaban',array('instrumen_id'=>$get_aum[0]['id'],'kelas'=>$id));
		$data['content'] = 'aum_detail.php';

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
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$jenjang));
		} else {
			$data['get_instrumen'] = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$getProfil[0]['jenjang']));
		}

		
		$data['get_aum'] = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>@$data['get_instrumen'][0]['id']));

		$data['content'] = 'aum_kode.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function kode_save(){
		$post = $this->input->post();
		$getProfil = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$get_aum = $this->Main_model->get_where('user_instrumen',array('instrumen_id'=>$post['instrumen_id'],'user_id'=>$this->session->userdata('id')));
		$checkKode = $this->Main_model->get_where('user_instrumen',array('kode_singkat'=>$post['kode_singkat'],'instrumen_id !='=>$post['instrumen_id']));
		$jenjang = $post['jenjang'];
		unset($post['jenjang']);
		if ($checkKode) {
			$this->session->set_flashdata('error','Kode telah digunakan. Silahkan coba kode lain.');
			redirect('aum/kode');
		} else {
			if ($get_aum) {
				$this->Main_model->update_data('user_instrumen',$post,array('id'=>$get_aum[0]['id']));
			} else {
				$this->Main_model->insert_data('user_instrumen',$post);
			}
		}
		
		if ($getProfil[0]['jenjang']=='Konselor') {
			redirect('aum/index/'.$jenjang);
		} else{
			redirect('aum');
		}
	}

	public function laporan_kelompok($id=""){
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$get_user[0]['jenjang']));
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
			$jawaban_berat = unserialize($value['jawaban_berat']);
			$jawaban_deskriptif = unserialize($value['jawaban_deskriptif']);

			$search = ['Ya'];
			$replace = [1];
			$result[] = str_replace($search, $replace, $jawaban);
			$result_berat[] = str_replace($search, $replace, $jawaban_berat);
			$result_deskriptif[] = $jawaban_deskriptif[223];
		}

		$sumArray = array();
		foreach ($result as $k=>$subArray) {
			foreach ($subArray as $id=>$value) {
				@$sumArray[$id]+=$value;
			}
		}

		$sumArrayBerat = array();
		foreach (array_filter($result_berat) as $k=>$subArray) {
			foreach ($subArray as $id=>$value) {
				@$sumArrayBerat[$id]+=$value;
			}
		}

		$skor_masalah = array();
		$skor_masalah_berat = array();
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (@$sumArray[$value['id']]) {
					$skor_masalah[$value_aspek['kode_aspek']]['butir'][] = $sumArray[$value['id']];
				}

				if (@$sumArrayBerat[$value['id']]) {
					$skor_masalah_berat[$value_aspek['kode_aspek']]['butir'][] = $sumArrayBerat[$value['id']];
				}
			}

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
		$pdf->Cell(185,6,'INSTRUMEN ALAT UNGKAP MASALAH - UMUM '.$get_user[0]['jenjang'],0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,strtoupper(getField('user_info','instansi',array('id'=>$this->session->userdata('id')))),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'TAHUN AJARAN 2019/2020',0,0,'C');

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
		$pdf->Cell(50,6,'A. DATA DASAR PERMASALAHAN YANG DIALAMI KELOMPOK',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50,6,'1. Grafik Permasalahan Tiap Bidang',0,0,'L');

		$textColour = array( 0, 0, 0 );
		$rowLabels = array( "JDK", "DPI", "HSO", "KDP", "EDK", "PDP", "ANM", "HMM", "KHK", "WSG");
		$chartXPos = -5;
		$chartYPos = 200;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array( 255, 100, 100 ),
			array( 100, 255, 100 ),
			array( 100, 100, 255 ),
			array( 255, 255, 100 ),
		);

		$pdf->SetFont('Arial', '', 12 );

		$data = array(
			array((@array_sum($skor_masalah['JDK']['butir'])/$skor_masalah['JDK']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['DPI']['butir'])/$skor_masalah['DPI']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['HSO']['butir'])/$skor_masalah['HSO']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['KDP']['butir'])/$skor_masalah['KDP']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['EDK']['butir'])/$skor_masalah['EDK']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['PDP']['butir'])/$skor_masalah['PDP']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['ANM']['butir'])/$skor_masalah['ANM']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['HMM']['butir'])/$skor_masalah['HMM']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['KHK']['butir'])/$skor_masalah['KHK']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['WSG']['butir'])/$skor_masalah['WSG']['total_butir'][0])*100),
		);


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

		$pdf->SetFont('Arial','B',12);
		$pdf->Ln(85);
		$pdf->Cell(10);
		$pdf->Cell(50,6,'Penafsiran :',0,1,'L');
		$pdf->Cell(185,6,'Tabel 1. Persentase Kondisi Permasalahan',0,1,'C');
		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'Persentase Jumlah Masalah',1,0,'L');
		$pdf->Cell(85,7,'Kondisi Permasalahan','R,B,T',1,'L');

		$pdf->SetFont('Arial','',12);
		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'0-20%',1,0,'L');
		$pdf->Cell(85,7,'Perlu Perhatian','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'21-40%',1,0,'L');
		$pdf->Cell(85,7,'Sangat Memerlukan Perhatian','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'41-60%',1,0,'L');
		$pdf->Cell(85,7,'Cukup Tinggi','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'61-80%',1,0,'L');
		$pdf->Cell(85,7,'Tinggi','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'81-100%',1,0,'L');
		$pdf->Cell(85,7,'Sangat Tinggi','R,B,T',1,'L');

		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(6);
		$pdf->Cell(50,6,'2. Tabel Masalah Tiap Bidang',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(185,6,'Tabel 2. Masalah Tiap Bidang',0,0,'C');
		$pdf->Ln();

		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(12,7,'',0,0,'L');
		$pdf->Cell(40,14,'Bidang Masalah',1,0,'L');
		$pdf->Cell(90,7,'Jumlah Masalah yang Dialami','R,B,T',0,'C');
		$pdf->Cell(36,7,'Masalah Berat','R,B,T',1,'L');

		$pdf->Cell(52,7,'',0,0,'L');
		$pdf->Cell(18,7,'R','R,B,T',0,'C');
		$pdf->Cell(18,7,'T','R,B,T',0,'C');
		$pdf->Cell(18,7,'J','R,B,T',0,'C');
		$pdf->Cell(18,7,'P (%)','R,B,T',0,'C');
		$pdf->Cell(18,7,'M','R,B,T',0,'C');

		$pdf->Cell(18,7,'J','R,B,T',0,'C');
		$pdf->Cell(18,7,'M','R,B,T',0,'C');
		
		$pdf->Ln();
		$pdf->Cell(12,7,'',0,0,'L');
		$pdf->Cell(40,7,'1','L,R,B',0,'C');
		$pdf->Cell(18,7,'2','R,B',0,'C');
		$pdf->Cell(18,7,'3','R,B',0,'C');
		$pdf->Cell(18,7,'4','R,B',0,'C');
		$pdf->Cell(18,7,'5','R,B',0,'C');
		$pdf->Cell(18,7,'6','R,B',0,'C');
		$pdf->Cell(18,7,'7','R,B',0,'C');
		$pdf->Cell(18,7,'8','R,B',0,'C');

		$pdf->SetFont('Arial','',12);
		$pdf->Ln();
		$pdf->SetWidths(array(40,18,18,18,18,18,18,18));
		$pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
		srand(microtime()*1000000);

		foreach ($get_aspek as $key => $value) {
			$pdf->Cell(12,7,'',0,0,'L');
			$pdf->Row(array($value['aspek']." (".$value['kode_aspek'].")",'20',$skor_masalah[$value['kode_aspek']]['total_butir'][0],@array_sum($skor_masalah[$value['kode_aspek']]['butir']),round((@array_sum($skor_masalah[$value['kode_aspek']]['butir'])/$skor_masalah[$value['kode_aspek']]['total_butir'][0])*100,2),round(@array_sum($skor_masalah[$value['kode_aspek']]['butir'])/count($get_data),2),@array_sum($skor_masalah_berat[$value['kode_aspek']]['butir']),round(@array_sum($skor_masalah_berat[$value['kode_aspek']]['butir'])/count($get_data),2)));
		}

		$pdf->SetFont('Arial','I',8);
		$pdf->Cell(12,5,'',0,0,'L');
		$pdf->Cell(180,5,'*R = Rendah, T = Tertinggi, J = Jumlah, P(%) = Persentase, M = Mean (Rata-rata)',0,1,'L');

		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(6);
		$pdf->Cell(50,6,'3. Peserta didik ingin mengkomunikasikan masalah kepada',0,0,'L');
		$pdf->SetFont('Arial','',12);

		$count_bk = 0;
		$count_teman = 0;
		$count_gurulain = 0;
		$count_orangtua = 0;
		$count_ahlilain = 0;
		$count_lainlain = 0;
		$count_tidakingin = 0;

		foreach ($result_deskriptif as $key => $value) {
			if ($value=='Guru Pembimbing') {
				$count_bk++;
			}

			if ($value=='Teman') {
				$count_teman++;
			}

			if ($value=='Guru Lain') {
				$count_gurulain++;
			}

			if ($value=='Orangtua') {
				$count_orangtua++;
			}

			if ($value=='Ahli Lain') {
				$count_ahlilain++;
			}

			if ($value=='Lain-lain') {
				$count_lainlain++;
			}

			if ($value=='Tidak Ingin') {
				$count_tidakingin++;
			}
		}

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Guru Pembimbing',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_bk.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Teman',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_teman.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Guru Lain',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_gurulain.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Orangtua',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_orangtua.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Ahli Lain',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_ahlilain.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Lain-Lain',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_lainlain.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Tidak Ingin',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_tidakingin.' Orang',0,0,'L');


		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'B. 10 ITEM MASALAH YANG DOMINAN DALAM KELOMPOK',0,0,'L');
		$pdf->Ln(8);
		$pdf->Cell(185,6,'Tabel 3. 10 Item Masalah yang Dominan dalam Kelompok',0,0,'C');

		$pdf->Ln(8);
		$pdf->SetWidths(array(20,90,30,30));
		srand(microtime()*1000000);

		arsort($sumArray);
		arsort($sumArrayBerat);
		$pdf->Cell(8,7,'',0,0,'L');
		$pdf->SetAligns(array('C','C','C','C'));
		$pdf->Row(array('No Item','Pernyataan','Bidang','Persentase'));

		$pdf->SetFont('Arial','',12);

		$counter_masalah = 1;

		$pdf->SetLeftMargin(18);
		foreach ($sumArray as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan','*',array(array('table'=>'instrumen_aspek','parameter'=>'instrumen_pernyataan.aspek_id=instrumen_aspek.id')),array('instrumen_pernyataan.id'=>$key));
			if ($counter_masalah<11) {
				$pdf->SetAligns(array('C','L','C','C'));
				$pdf->Row(array($counter_masalah,$get_instrumen[0]['pernyataan'],$get_instrumen[0]['kode_aspek'],$sumArray[$key]));
			}

			$counter_masalah++;
		}

		$pdf->SetLeftMargin(10);

		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'C. 10 MASALAH BERAT YANG DOMINAN DALAM KELOMPOK',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(185,6,'Tabel 4. 10 Item Masalah Berat yang Dominan dalam Kelompok',0,0,'C');

		$pdf->Ln(8);
		$pdf->SetWidths(array(20,90,30,30));
		srand(microtime()*1000000);

		$pdf->Cell(8,7,'',0,0,'L');
		$pdf->SetAligns(array('C','C','C','C'));
		$pdf->Row(array('No Item','Pernyataan','Bidang','Persentase'));

		$pdf->SetFont('Arial','',12);

		$counter_masalah_berat = 1;
		foreach ($sumArrayBerat as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan','*',array(array('table'=>'instrumen_aspek','parameter'=>'instrumen_pernyataan.aspek_id=instrumen_aspek.id')),array('instrumen_pernyataan.id'=>$key));
			if ($counter_masalah_berat<11) {
				$pdf->Cell(8,7,'',0,0,'L');
				$pdf->SetAligns(array('C','L','C','C'));
				$pdf->Row(array($counter_masalah_berat,$get_instrumen[0]['pernyataan'],$get_instrumen[0]['kode_aspek'],$sumArrayBerat[$key]));
			}

			$counter_masalah_berat++;
		}

		$pdf->Ln(8);
		$pdf->Cell(178,6,'Jakarta, '.konversi_tanggal(date('Y-m-d')),0,0,'R');
		$pdf->Ln();
		$pdf->Cell(178,6,'Pengolah Data',0,0,'R');
		$pdf->Ln(20);
		$pdf->Cell(178,6,getField('user_konselor','nama_lengkap',array('id'=>$get_kelas[0]['konselor_id'])),0,0,'R');

		$pdf->SetTitle('LAPORAN AUM '.$get_kelompok[0]['nama_kelompok'].'.pdf');

		$pdf->Output('I','LAPORAN AUM '.$get_kelompok[0]['nama_kelompok'].'.pdf',FALSE);
	}



	public function laporan_kelas($id=""){
		$this->load->library('fpdf_diag');
		$get_user = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		if ($get_user[0]['jenjang']=='Konselor'){
			$get_kelas = $this->Main_model->get_where('kelas',array('id'=>$id));
			$get_user[0]['jenjang'] = $get_kelas[0]['jenjang'];
		}
		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$get_user[0]['jenjang']));
		$get_kode = $this->Main_model->get_where('user_instrumen',array('user_id'=>$this->session->userdata('id'),'instrumen_id'=>$get_instrumen[0]['id']));
		$get_data = $this->Main_model->get_where('instrumen_jawaban',array('kelas'=>$id,'instrumen_id'=>$get_kode[0]['id']));
		$get_surat = $this->Main_model->get_where('user_surat',array('user_id'=>$this->session->userdata('id')));
		$get_kelas = $this->Main_model->get_where('kelas',array('id'=>$id));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek',array('instrumen_id'=>$get_instrumen[0]['id']));

		foreach ($get_data as $key => $value) {
			$jawaban = unserialize($value['jawaban']);
			$jawaban_berat = unserialize($value['jawaban_berat']);
			$jawaban_deskriptif = unserialize($value['jawaban_deskriptif']);

			$search = ['Ya'];
			$replace = [1];
			$result[] = str_replace($search, $replace, $jawaban);
			$result_berat[] = str_replace($search, $replace, $jawaban_berat);
			$result_deskriptif[] = $jawaban_deskriptif[223];
		}

		$sumArray = array();
		foreach ($result as $k=>$subArray) {
			foreach ($subArray as $id=>$value) {
				@$sumArray[$id]+=$value;
			}
		}

		$sumArrayBerat = array();
		foreach ($result_berat as $k=>$subArray) {
			if ($subArray) {
				foreach ($subArray as $id=>$value) {
					@$sumArrayBerat[$id]+=$value;
				}
			}
		}

		$skor_masalah = array();
		$skor_masalah_berat = array();
		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (@$sumArray[$value['id']]) {
					$skor_masalah[$value_aspek['kode_aspek']]['butir'][] = $sumArray[$value['id']];
				}

				if (@$sumArrayBerat[$value['id']]) {
					$skor_masalah_berat[$value_aspek['kode_aspek']]['butir'][] = $sumArrayBerat[$value['id']];
				}
			}

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
		$pdf->Cell(185,6,'INSTRUMEN ALAT UNGKAP MASALAH - UMUM '.$get_user[0]['jenjang'],0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,strtoupper(getField('user_info','instansi',array('id'=>$this->session->userdata('id')))),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'TAHUN AJARAN 2019/2020',0,0,'C');

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
		$pdf->Cell(50,6,'A. DATA DASAR PERMASALAHAN YANG DIALAMI KELOMPOK',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(6);
		$pdf->Cell(50,6,'1. Grafik Permasalahan Tiap Bidang',0,0,'L');

		$textColour = array( 0, 0, 0 );
		$rowLabels = array( "JDK", "DPI", "HSO", "KDP", "EDK", "PDP", "ANM", "HMM", "KHK", "WSG");
		$chartXPos = -5;
		$chartYPos = 200;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array( 255, 100, 100 ),
			array( 100, 255, 100 ),
			array( 100, 100, 255 ),
			array( 255, 255, 100 ),
		);

		$pdf->SetFont('Arial', '', 12 );

		$data = array(
			array((@array_sum($skor_masalah['JDK']['butir'])/$skor_masalah['JDK']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['DPI']['butir'])/$skor_masalah['DPI']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['HSO']['butir'])/$skor_masalah['HSO']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['KDP']['butir'])/$skor_masalah['KDP']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['EDK']['butir'])/$skor_masalah['EDK']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['PDP']['butir'])/$skor_masalah['PDP']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['ANM']['butir'])/$skor_masalah['ANM']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['HMM']['butir'])/$skor_masalah['HMM']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['KHK']['butir'])/$skor_masalah['KHK']['total_butir'][0])*100),
			array((@array_sum($skor_masalah['WSG']['butir'])/$skor_masalah['WSG']['total_butir'][0])*100),
		);


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

		$pdf->SetFont('Arial','B',12);
		$pdf->Ln(85);
		$pdf->Cell(10);
		$pdf->Cell(50,6,'Penafsiran :',0,1,'L');
		$pdf->Cell(185,6,'Tabel 1. Persentase Kondisi Permasalahan',0,1,'C');
		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'Persentase Jumlah Masalah',1,0,'L');
		$pdf->Cell(85,7,'Kondisi Permasalahan','R,B,T',1,'L');

		$pdf->SetFont('Arial','',12);
		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'0-20%',1,0,'L');
		$pdf->Cell(85,7,'Perlu Perhatian','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'21-40%',1,0,'L');
		$pdf->Cell(85,7,'Sangat Memerlukan Perhatian','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'41-60%',1,0,'L');
		$pdf->Cell(85,7,'Cukup Tinggi','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'61-80%',1,0,'L');
		$pdf->Cell(85,7,'Tinggi','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'81-100%',1,0,'L');
		$pdf->Cell(85,7,'Sangat Tinggi','R,B,T',1,'L');

		$pdf->AddPage();
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(6);
		$pdf->Cell(50,6,'2. Tabel Masalah Tiap Bidang',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(185,6,'Tabel 2. Masalah Tiap Bidang',0,0,'C');
		$pdf->Ln();

		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(12,7,'',0,0,'L');
		$pdf->Cell(40,14,'Bidang Masalah',1,0,'L');
		$pdf->Cell(90,7,'Jumlah Masalah yang Dialami','R,B,T',0,'C');
		$pdf->Cell(36,7,'Masalah Berat','R,B,T',1,'L');

		$pdf->Cell(52,7,'',0,0,'L');
		$pdf->Cell(18,7,'R','R,B,T',0,'C');
		$pdf->Cell(18,7,'T','R,B,T',0,'C');
		$pdf->Cell(18,7,'J','R,B,T',0,'C');
		$pdf->Cell(18,7,'P (%)','R,B,T',0,'C');
		$pdf->Cell(18,7,'M','R,B,T',0,'C');

		$pdf->Cell(18,7,'J','R,B,T',0,'C');
		$pdf->Cell(18,7,'M','R,B,T',0,'C');
		
		$pdf->Ln();
		$pdf->Cell(12,7,'',0,0,'L');
		$pdf->Cell(40,7,'1','L,R,B',0,'C');
		$pdf->Cell(18,7,'2','R,B',0,'C');
		$pdf->Cell(18,7,'3','R,B',0,'C');
		$pdf->Cell(18,7,'4','R,B',0,'C');
		$pdf->Cell(18,7,'5','R,B',0,'C');
		$pdf->Cell(18,7,'6','R,B',0,'C');
		$pdf->Cell(18,7,'7','R,B',0,'C');
		$pdf->Cell(18,7,'8','R,B',0,'C');

		$pdf->SetFont('Arial','',12);
		$pdf->Ln();
		$pdf->SetWidths(array(40,18,18,18,18,18,18,18));
		$pdf->SetAligns(array('L','C','C','C','C','C','C','C'));
		srand(microtime()*1000000);


		foreach ($get_aspek as $key => $value) {
			$pdf->Cell(12,7,'',0,0,'L');
			$pdf->Row(array($value['aspek']." (".$value['kode_aspek'].")",'20',$skor_masalah[$value['kode_aspek']]['total_butir'][0],@array_sum($skor_masalah[$value['kode_aspek']]['butir']),round((@array_sum($skor_masalah[$value['kode_aspek']]['butir'])/$skor_masalah[$value['kode_aspek']]['total_butir'][0])*100,2),round(@array_sum($skor_masalah[$value['kode_aspek']]['butir'])/count($get_data),2),@array_sum($skor_masalah_berat[$value['kode_aspek']]['butir']),round(@array_sum($skor_masalah_berat[$value['kode_aspek']]['butir'])/count($get_data),2)));
		}

		$pdf->SetFont('Arial','I',8);
		$pdf->Cell(12,5,'',0,0,'L');
		$pdf->Cell(180,5,'*R = Rendah, T = Tertinggi, J = Jumlah, P(%) = Persentase, M = Mean (Rata-rata)',0,1,'L');

		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(6);
		$pdf->Cell(50,6,'3. Peserta didik ingin mengkomunikasikan masalah kepada',0,0,'L');
		$pdf->SetFont('Arial','',12);

		$count_bk = 0;
		$count_teman = 0;
		$count_gurulain = 0;
		$count_orangtua = 0;
		$count_ahlilain = 0;
		$count_lainlain = 0;
		$count_tidakingin = 0;

		foreach ($result_deskriptif as $key => $value) {
			if ($value=='Guru Pembimbing') {
				$count_bk++;
			}

			if ($value=='Teman') {
				$count_teman++;
			}

			if ($value=='Guru Lain') {
				$count_gurulain++;
			}

			if ($value=='Orangtua') {
				$count_orangtua++;
			}

			if ($value=='Ahli Lain') {
				$count_ahlilain++;
			}

			if ($value=='Lain-lain') {
				$count_lainlain++;
			}

			if ($value=='Tidak Ingin') {
				$count_tidakingin++;
			}
		}

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Guru Pembimbing',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_bk.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Teman',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_teman.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Guru Lain',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_gurulain.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Orangtua',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_orangtua.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Ahli Lain',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_ahlilain.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Lain-Lain',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_lainlain.' Orang',0,0,'L');

		$pdf->Ln();
		$pdf->Cell(11,7,'',0,0,'L');
		$pdf->Cell(35,7,'Tidak Ingin',0,0,'L');
		$pdf->Cell(5,7,':',0,0,'L');
		$pdf->Cell(40,7,$count_tidakingin.' Orang',0,0,'L');

		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'B. 10 ITEM MASALAH YANG DOMINAN DALAM KELOMPOK',0,0,'L');
		$pdf->Ln(8);
		$pdf->Cell(185,6,'Tabel 3. 10 Item Masalah yang Dominan dalam Kelompok',0,0,'C');

		$pdf->Ln(8);
		$pdf->SetWidths(array(20,90,30,30));
		srand(microtime()*1000000);

		arsort($sumArray);
		arsort($sumArrayBerat);
		$pdf->Cell(8,7,'',0,0,'L');
		$pdf->SetAligns(array('C','C','C','C'));
		$pdf->Row(array('No Item','Pernyataan','Bidang','Persentase'));

		$pdf->SetFont('Arial','',12);

		$counter_masalah = 1;
		$pdf->SetLeftMargin(18);
		foreach ($sumArray as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan','*',array(array('table'=>'instrumen_aspek','parameter'=>'instrumen_pernyataan.aspek_id=instrumen_aspek.id')),array('instrumen_pernyataan.id'=>$key));
			if ($counter_masalah<11) {
				$pdf->SetAligns(array('C','L','C','C'));
				$pdf->Row(array($counter_masalah,$get_instrumen[0]['pernyataan'],$get_instrumen[0]['kode_aspek'],$sumArray[$key]));
			}

			$counter_masalah++;
		}

		$pdf->SetLeftMargin(10);

		$pdf->Ln(5);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'C. 10 MASALAH BERAT YANG DOMINAN DALAM KELOMPOK',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(185,6,'Tabel 4. 10 Item Masalah Berat yang Dominan dalam Kelompok',0,0,'C');

		$pdf->Ln(8);
		$pdf->SetWidths(array(20,90,30,30));
		srand(microtime()*1000000);

		$pdf->Cell(8,7,'',0,0,'L');
		$pdf->SetAligns(array('C','C','C','C'));
		$pdf->Row(array('No Item','Pernyataan','Bidang','Persentase'));

		$pdf->SetFont('Arial','',12);

		$counter_masalah_berat = 1;
		$pdf->SetLeftMargin(18);
		foreach ($sumArrayBerat as $key => $value) {
			$get_instrumen = $this->Main_model->join('instrumen_pernyataan','*',array(array('table'=>'instrumen_aspek','parameter'=>'instrumen_pernyataan.aspek_id=instrumen_aspek.id')),array('instrumen_pernyataan.id'=>$key));
			if ($counter_masalah_berat<11) {
				$pdf->SetAligns(array('C','L','C','C'));
				$pdf->Row(array($counter_masalah_berat,$get_instrumen[0]['pernyataan'],$get_instrumen[0]['kode_aspek'],$sumArrayBerat[$key]));
			}

			$counter_masalah_berat++;
		}

		$pdf->SetLeftMargin(10);
		$pdf->Ln(8);
		$pdf->Cell(178,6,'Jakarta, '.konversi_tanggal(date('Y-m-d')),0,0,'R');
		$pdf->Ln();
		$pdf->Cell(178,6,'Pengolah Data',0,0,'R');
		$pdf->Ln(20);
		$pdf->Cell(178,6,getField('user_konselor','nama_lengkap',array('id'=>$get_kelas[0]['konselor_id'])),0,0,'R');

		
		$pdf->SetTitle('LAPORAN AUM '.$get_kelas[0]['kelas'].'.pdf');

		$pdf->Output('I','LAPORAN AUM '.$get_kelas[0]['kelas'].'.pdf',FALSE);
	}

	public function laporan_individu($id=""){
		$this->load->library('fpdf_diag');
		$get_profil = $this->Main_model->get_where('instrumen_jawaban',array('id'=>$id));
		$get_kelas = $this->Main_model->get_where('kelas',array('id'=>$get_profil[0]['kelas']));
		$get_user = $this->Main_model->get_where('user_info',array('user_id'=>$this->session->userdata('id')));
		$get_surat = $this->Main_model->get_where('user_surat',array('user_id'=>$this->session->userdata('id')));
		if ($get_user[0]['jenjang']=='Konselor'){
			$get_kelas = $this->Main_model->get_where('kelas',array('id'=>$get_profil[0]['kelas']));
			$get_user[0]['jenjang'] = $get_kelas[0]['jenjang'];
		}
		$get_instrumen = $this->Main_model->get_where('instrumen',array('nickname'=>'AUM Umum','jenjang'=>$get_user[0]['jenjang']));
		$get_aspek = $this->Main_model->get_where('instrumen_aspek',array('instrumen_id'=>$get_instrumen[0]['id']));
		$jawaban = unserialize($get_profil[0]['jawaban']);
		$jawaban_berat = unserialize($get_profil[0]['jawaban_berat']);
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
		$pdf->Ln(10);
		$pdf->Cell(185,6,'LAPORAN INDIVIDU',0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'INSTRUMEN ALAT UNGKAP MASALAH - UMUM '.$get_user[0]['jenjang'],0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,strtoupper(getField('user_info','instansi',array('id'=>$this->session->userdata('id')))),0,0,'C');
		$pdf->Ln();
		$pdf->Cell(185,6,'TAHUN AJARAN 2019/2020',0,0,'C');

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
		$pdf->Cell(50,6,'A. DATA DASAR PERMASALAHAN YANG DIALAMI INDIVIDU',0,0,'L');
		$pdf->Ln();
		$pdf->Cell(50,6,'1. Grafik Permasalahan Tiap Bidang',0,0,'L');

		$textColour = array( 0, 0, 0 );
		$rowLabels = array( "JDK", "DPI", "HSO", "KDP", "EDK", "PDP", "ANM", "HMM", "KHK", "WSG");
		$chartXPos = -5;
		$chartYPos = 200;
		$chartWidth = 190;
		$chartHeight = 80;
		$chartYStep = 100;

		$chartColours = array(
			array( 255, 100, 100 ),
			array( 100, 255, 100 ),
			array( 100, 100, 255 ),
			array( 255, 255, 100 ),
		);

		$pdf->SetFont('Arial', '', 12 );

		foreach ($get_aspek as $value_aspek) {
			$array_no_masalah = array();
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));
			foreach ($get_butir as $key => $value) {
				if (@$jawaban[$value['id']]) {
					$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}
			}

			$skor_masalah[] = round((count(@$array_no_masalah[$value_aspek['kode_aspek']])/count($get_butir))*100,2);
		}

		$data = array(
			array(@$skor_masalah[0]),
			array(@$skor_masalah[1]),
			array(@$skor_masalah[2]),			
			array(@$skor_masalah[3]),
			array(@$skor_masalah[4]),
			array(@$skor_masalah[5]),
			array(@$skor_masalah[6]),
			array(@$skor_masalah[7]),
			array(@$skor_masalah[8]),
			array(@$skor_masalah[9]),
		);


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

		$pdf->SetFont('Arial','B',12);
		$pdf->Ln(85);
		$pdf->Cell(10);
		$pdf->Cell(50,6,'Penafsiran :',0,1,'L');
		$pdf->Cell(185,6,'Tabel 1. Persentase Kondisi Permasalahan',0,1,'C');
		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'Persentase Jumlah Masalah',1,0,'L');
		$pdf->Cell(85,7,'Kondisi Permasalahan','R,B,T',1,'L');

		$pdf->SetFont('Arial','',12);
		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'0-20%',1,0,'L');
		$pdf->Cell(85,7,'Perlu Perhatian','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'21-40%',1,0,'L');
		$pdf->Cell(85,7,'Sangat Memerlukan Perhatian','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'41-60%',1,0,'L');
		$pdf->Cell(85,7,'Cukup Tinggi','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'61-80%',1,0,'L');
		$pdf->Cell(85,7,'Tinggi','R,B,T',1,'L');

		$pdf->Cell(10,7,'',0,0,'L');
		$pdf->Cell(85,7,'81-100%',1,0,'L');
		$pdf->Cell(85,7,'Sangat Tinggi','R,B,T',1,'L');

		$pdf->AddPage();
		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);

		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'2. Tabel Masalah Tiap Bidang',0,0,'L');
		$pdf->Ln(5);
		$pdf->Cell(185,6,'Tabel 2. Masalah Tiap Bidang',0,0,'C');
		$pdf->Ln();

		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(10,14,'No',1,0,'C');
		$pdf->Cell(45,14,'Bidang Masalah','R,B,T',0,'C');
		$pdf->Cell(80,7,'Jenis Masalah','R,B,T',0,'C');
		$pdf->Cell(37,7,'No Masalah','L,R,T',1,'C');

		$pdf->Cell(61,7,'',0,0,'L');
		$pdf->Cell(50,7,'No Masalah','R,B,T',0,'C');
		$pdf->Cell(15,7,'Jml','R,B,T',0,'C');
		$pdf->Cell(15,7,'%','R,B,T',0,'C');
		$pdf->Cell(37,7,'Berat','R,B',1,'C');		

		$pdf->SetFont('Arial','',12);
		$pdf->SetWidths(array(10,45,50,15,15,37));
		$pdf->SetAligns(array('C','L','L','C','C','C'));
		srand(microtime()*1000000);
		$i = 1;

		foreach ($get_aspek as $value_aspek) {
			$get_butir = $this->Main_model->get_where('instrumen_pernyataan',array('aspek_id'=>$value_aspek['id']));
			$array_no_masalah = array();
			$array_masalah_berat = array();
			foreach ($get_butir as $key => $value) {
				if (@$jawaban[$value['id']]) {
					$array_no_masalah[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}

				if (@$jawaban_berat[$value['id']]) {
					$array_masalah_berat[$value_aspek['kode_aspek']][] = $value['kode_pernyataan'];
				}
			}

			$pdf->Cell(6,7,'',0,0,'L');
			$pdf->Row(array($i++,$value_aspek['aspek']." (".$value_aspek['kode_aspek'].")",($array_no_masalah) ? implode(",",$array_no_masalah[$value_aspek['kode_aspek']]) : '-',($array_no_masalah) ? count($array_no_masalah[$value_aspek['kode_aspek']]) : '0',($array_no_masalah) ? round((count($array_no_masalah[$value_aspek['kode_aspek']])/count($get_butir))*100,2) : '0',($array_masalah_berat) ? implode(",",$array_masalah_berat[$value_aspek['kode_aspek']]) : '-'));
		}

		$pdf->Ln(5);
		$pdf->SetFont('Arial','I',12);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(50,6,'3. Peserta didik ingin mengkomunikasikan masalah kepada '.$jawaban_deskriptif['223'],0,0,'L');

		$pdf->Ln(10);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'B. Item masalah yang paling berat atau mengganggu bagi individu',0,0,'L');
		$pdf->Ln(8);
		

		if ($jawaban_berat) {
			$pdf->Cell(185,6,'Tabel 3. 10 Item Masalah yang Dominan dalam Kelompok',0,0,'C');

			$pdf->Ln(8);
			$pdf->Cell(6,7,'',0,0,'L');
			$pdf->SetWidths(array(20,121,30));
			srand(microtime()*1000000);
			$pdf->SetAligns(array('C','C','C','C'));
			$pdf->Row(array('No Item','Pernyataan','Bidang'));
			$pdf->SetFont('Arial','',12);
			$i = 1;
			foreach ($jawaban_berat as $key_berat => $value_berat) {
				$get_instrumen_berat = $this->Main_model->join('instrumen_pernyataan','*',array(array('table'=>'instrumen_aspek','parameter'=>'instrumen_pernyataan.aspek_id=instrumen_aspek.id')),array('instrumen_pernyataan.id'=>$key_berat));
				$pdf->Cell(6,7,'',0,0,'L');
				$pdf->SetAligns(array('C','L','C'));
				$pdf->Row(array($i++,$get_instrumen_berat[0]['pernyataan'],$get_instrumen_berat[0]['kode_aspek']));
			}
		}

		$pdf->SetFont('Arial','',12);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->Cell(150,7,'Tidak ada masalah berat yang dipilih oleh peserta didik.',0,0,'L');
		

		$pdf->Ln(8);
		$pdf->SetLeftMargin(10);
		$pdf->SetFont('Arial','B',12);
		$pdf->Cell(50,6,'C. Masalah individu yang belum tercantum dalam AUM-U',0,0,'L');
		$pdf->SetFont('Arial','',12);
		$pdf->Ln(8);
		$pdf->Cell(6,7,'',0,0,'L');
		$pdf->MultiCell(160,6,$jawaban_deskriptif['222'],0,'J');

		
		$pdf->Ln(10);
		$pdf->Cell(176,6,'Jakarta, '.konversi_tanggal(date('Y-m-d')),0,0,'R');
		$pdf->Ln();
		$pdf->Cell(176,6,'Pengolah Data',0,0,'R');
		$pdf->Ln(25);
		$pdf->Cell(176,6,getField('user_konselor','nama_lengkap',array('id'=>$get_kelas[0]['konselor_id'])),0,0,'R');

		$pdf->SetTitle('LAPORAN AUM '.getField('kelas','kelas',array('id'=>$get_profil[0]['kelas'])).' - '.$get_profil[0]['nama_lengkap'].'.pdf');

		$pdf->Output('I','LAPORAN AUM '.getField('kelas','kelas',array('id'=>$get_profil[0]['kelas'])).' - '.$get_profil[0]['nama_lengkap'].'.pdf',FALSE);
	}
}

/* End of file Aum.php */
/* Location: ./application/controllers/Aum.php */