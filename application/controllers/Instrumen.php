<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Instrumen extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
	}

	public function aum($kode_singkat = "")
	{
		$data['get_kode'] = $this->Main_model->get_where('user_instrumen', array('kode_singkat' => $kode_singkat));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('id' => $data['get_kode'][0]['instrumen_id']));
		$get_profil = $this->Main_model->get_where('user_info', array('user_id' => $data['get_kode'][0]['user_id']));
		if ($get_profil[0]['status'] == 'Guru BK') {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		} else {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'jenjang' => $get_instrumen[0]['jenjang'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		}
		$data['content'] = 'aum_view.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}

	public function ptsdl($kode_singkat = "")
	{
		$data['get_kode'] = $this->Main_model->get_where('user_instrumen', array('kode_singkat' => $kode_singkat));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('id' => $data['get_kode'][0]['instrumen_id']));
		$get_profil = $this->Main_model->get_where('user_info', array('user_id' => $data['get_kode'][0]['user_id']));
		if ($get_profil[0]['status'] == 'Guru BK') {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		} else {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'jenjang' => $get_instrumen[0]['jenjang'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		}
		$data['content'] = 'ptsdl_view.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}

	public function auap($kode_singkat = "")
	{
		$data['get_kode'] = $this->Main_model->get_where('user_instrumen', array('kode_singkat' => $kode_singkat));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('id' => $data['get_kode'][0]['instrumen_id']));
		$get_profil = $this->Main_model->get_where('user_info', array('user_id' => $data['get_kode'][0]['user_id']));
		if ($get_profil[0]['status'] == 'Guru BK') {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		} else {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'jenjang' => $get_instrumen[0]['jenjang'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		}
		$data['get_aspek'] = $this->Main_model->get_where('instrumen_aspek', array('instrumen_id' => $data['get_kode'][0]['instrumen_id']));
		$data['content'] = 'auap_view.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}

	public function dcm($kode_singkat = "")
	{
		$data['get_kode'] = $this->Main_model->get_where('user_instrumen', array('kode_singkat' => $kode_singkat));
		$get_instrumen = $this->Main_model->get_where('instrumen', array('id' => $data['get_kode'][0]['instrumen_id']));
		$get_profil = $this->Main_model->get_where('user_info', array('user_id' => $data['get_kode'][0]['user_id']));
		if ($get_profil[0]['status'] == 'Guru BK') {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		} else {
			$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $data['get_kode'][0]['user_id'], 'jenjang' => $get_instrumen[0]['jenjang'], 'tahun_ajaran' => $this->Main_model->getTahunAjaran()));
		}
		$data['content'] = 'dcm_view.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}

	public function aum_advanced()
	{
		$get = $this->input->get();
		$get_kode = $this->Main_model->get_where('user_instrumen', array('id' => $get['instrumen_id']));
		$data['get_instrumen'] = $this->Main_model->join('instrumen_pernyataan', '*,instrumen_pernyataan.id as id', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_id' => $get_kode[0]['instrumen_id']));
		$data['get_instrumen'] = array_chunk($data['get_instrumen'], 20);
		$data['jumlah_tab'] = count($data['get_instrumen']) - 1;
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'aum_view_advanced.php';
		$this->load->view('main', $data, FALSE);
	}

	public function ptsdl_advanced()
	{
		$get = $this->input->get();
		$get_kode = $this->Main_model->get_where('user_instrumen', array('id' => $get['instrumen_id']));
		$data['get_instrumen'] = $this->Main_model->join('instrumen_pernyataan', '*,instrumen_pernyataan.id as id', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_id' => $get_kode[0]['instrumen_id']));
		$data['get_instrumen'] = array_chunk($data['get_instrumen'], 20);
		$data['jumlah_tab'] = count($data['get_instrumen']) - 1;
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'ptsdl_view_advanced.php';
		$this->load->view('main', $data, FALSE);
	}

	public function auap_advanced()
	{
		$get = $this->input->get();
		$get_pernyataan = array();
		foreach ($get['bidang_peminatan'] as $key => $value) {
			$get_pernyataan[] = $this->Main_model->join('instrumen_pernyataan', '*,instrumen_pernyataan.id as id', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_pernyataan.aspek_id' => $value));
		}

		$pernyataan = array();
		foreach ($get_pernyataan as $keyp => $valuep) {
			$pernyataan = array_merge($pernyataan, $valuep);
		}

		$data['get_instrumen'] = array_chunk($pernyataan, 20);
		$data['jumlah_tab'] = count($data['get_instrumen']) - 1;
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'auap_view_advanced.php';
		$this->load->view('main', $data, FALSE);
	}

	public function dcm_advanced()
	{
		$get = $this->input->get();
		$get_kode = $this->Main_model->get_where('user_instrumen', array('id' => $get['instrumen_id']));
		$data['get_instrumen'] = $this->Main_model->join('instrumen_pernyataan', '*,instrumen_pernyataan.id as id', array(array('table' => 'instrumen_aspek', 'parameter' => 'instrumen_pernyataan.aspek_id=instrumen_aspek.id')), array('instrumen_id' => $get_kode[0]['instrumen_id']));
		$data['get_instrumen'] = array_chunk($data['get_instrumen'], 20);
		$data['jumlah_tab'] = count($data['get_instrumen']) - 1;
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'dcm_view_advanced.php';
		$this->load->view('main', $data, FALSE);
	}

	public function aum_advanced_berat()
	{
		$get = $this->input->get();
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'aum_view_advanced_berat.php';
		$this->load->view('main', $data, FALSE);
	}

	public function aum_advanced_detail()
	{
		$get = $this->input->get();
		$data['jawaban'] = array_chunk($get['jawaban'], 20, true);
		$data['jumlah_tab'] = count($data['jawaban']) - 1;
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'aum_view_advanced_detail.php';
		$this->load->view('main', $data, FALSE);
	}

	public function dcm_advanced_detail()
	{
		$get = $this->input->get();
		$data['jawaban'] = array_chunk($get['jawaban'], 20, true);
		$data['jumlah_tab'] = count($data['jawaban']) - 1;
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'dcm_view_advanced_detail.php';
		$this->load->view('main', $data, FALSE);
	}

	public function ptsdl_save()
	{
		$get = $this->input->get();
		$get['jawaban'] = serialize($get['jawaban']);
		$get['tanggal_lahir'] = date('Y-m-d', strtotime($get['tanggal_lahir']));
		$get['date_created'] = date('Y-m-d H:i:s');
		$this->Main_model->insert_data('instrumen_jawaban', $get);
		$this->session->set_flashdata('success', 'Data Alat Ungkap Masalah Kegiatan Belajar telah berhasil disimpan.');
		redirect('instrumen/ptsdl_success');
	}

	public function dcm_advanced_save()
	{
		$get = $this->input->get();
		$get['jawaban_deskriptif'] = serialize($get['jawaban_deskriptif']);
		$get['tanggal_lahir'] = date('Y-m-d', strtotime($get['tanggal_lahir']));
		$get['date_created'] = date('Y-m-d H:i:s');
		$this->Main_model->insert_data('instrumen_jawaban', $get);
		$this->session->set_flashdata('success', 'Data Alat Ungkap Masalah telah berhasil disimpan.');
		redirect('instrumen/aum_success');
	}

	public function aum_advanced_save()
	{
		$get = $this->input->get();
		$get['jawaban_berat'] = serialize($get['jawaban_berat']);
		$get['jawaban_deskriptif'] = serialize($get['jawaban_deskriptif']);
		$get['tanggal_lahir'] = date('Y-m-d', strtotime($get['tanggal_lahir']));
		$get['date_created'] = date('Y-m-d H:i:s');
		$this->Main_model->insert_data('instrumen_jawaban', $get);
		$this->session->set_flashdata('success', 'Data Alat Ungkap Masalah telah berhasil disimpan.');
		redirect('instrumen/aum_success');
	}

	public function auap_save()
	{
		$get = $this->input->get();
		$get['jawaban'] = serialize($get['jawaban']);
		$get['tanggal_lahir'] = date('Y-m-d', strtotime($get['tanggal_lahir']));
		$get['date_created'] = date('Y-m-d H:i:s');
		$this->Main_model->insert_data('instrumen_jawaban', $get);
		$this->session->set_flashdata('success', 'Data Alat Ungkap Arah Peminatan telah berhasil disimpan.');
		redirect('instrumen/ptsdl_success');
	}

	public function dcm_success()
	{
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'success.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function aum_success()
	{
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'success.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function ptsdl_success()
	{
		$data['tipe_member'] = 'siswa';
		$data['content'] = 'success.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function aum_ptsdl()
	{
		$data['content'] = 'aum_ptsdl.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function aum_ptsdl_tambah()
	{
		$data['content'] = 'aum_ptsdl_tambah.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function aum_ptsdl_save()
	{
		$this->session->set_flashdata('success', 'AUM PTSDL');
		redirect('instrumen/aum_ptsdl');
	}

	public function aum_ptsdl_view()
	{
		$data['content'] = 'aum_ptsdl_view.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}

	public function aum_ptsdl_view_advanced()
	{
		$data['content'] = 'aum_ptsdl_view_advanced.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}
}

/* End of file Instrumen.php */
/* Location: ./application/controllers/Instrumen.php */
