<?php
defined('BASEPATH') or exit('No direct script access allowed');

class PesertaDidik extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
	}

	public function index()
	{
		$get_kelas = $this->Main_model->get_where('kelas', [
			'user_id' => $this->session->userdata('id')
		]);
		$get_tahun_ajaran = $this->db->query("SELECT DISTINCT tahun_ajaran FROM kelas WHERE `user_id` = " . $this->session->userdata('id'))->result_array();

		$data = [
			'get_kelas' => $get_kelas,
			'get_tahun_ajaran' => $get_tahun_ajaran,
			'content' => 'pesertadidik/index'
		];

		$this->load->view('main', $data, False);
	}

	public function view()
	{
		$post = $this->input->get();
		if (empty($post['tahun_ajaran']) || empty($post['kelas'])) {
			return redirect('pesertadidik');
		}
		$kelas = $post['kelas'];

		$data_temp = [];

		$temp_siswa = $this->db->query("SELECT id,nis,nama_lengkap as nama,jenis_kelamin as jk, tanggal_lahir as tgl_lahir, whatsapp as no_telepon,email,kelas as id_kelas FROM `instrumen_jawaban` WHERE kelas = $kelas GROUP BY nama_lengkap")->result_array();
		$get_siswa = $this->Main_model->get_where('kelas_siswa', ['id_kelas' => $kelas]);

		$get_tahun_ajaran = $this->db->query("SELECT DISTINCT tahun_ajaran FROM kelas WHERE `user_id` = " . $this->session->userdata('id'))->result_array();

		$get_kelas = $this->Main_model->get_where('kelas', array('id' => $kelas));

		$getInstrumen = $this->Main_model->join('user_instrumen', '*', [[
			'table' => 'instrumen',
			'parameter' => 'user_instrumen.instrumen_id = instrumen.id'
		]], ['user_instrumen.user_id' => $this->session->userdata('id')]);

		$get_profil = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));

		// if (empty($get_siswa)) {

		// $get_siswa = $this->db->query("SELECT id,nama_lengkap as nama,jenis_kelamin as jk, tanggal_lahir as tgl_lahir, whatsapp as no_telepon,email,kelas as id_kelas FROM `instrumen_jawaban` WHERE kelas = $kelas GROUP BY nama_lengkap")->result_array();
		// }

		if (empty($get_siswa)) {
			$data_temp = $temp_siswa;
		} else if (empty($temp_siswa)) {
			$data_temp = $get_siswa;
		} else if (!empty($get_siswa) && !empty($temp_siswa)) {
			// mengcompare dan manyatukan 2 data agar mendapat data yang unique dari Kelas_siswa and instrumen_jawaban By Name
			$i = 0;
			$data_temp = $get_siswa;
			foreach ($temp_siswa as $key => $val) {
				foreach ($data_temp as $key1 => $val1) {
					$same = false;
					if ($val['nama'] == $val1['nama']) {
						$same = true;
						break;
					}
				}
				if ($same == false) {
					$data_temp[] = $temp_siswa[$i];
				}
				$i++;
			}
		}

		$data = [
			'content' => 'pesertadidik/view',
			'siswa' => $data_temp,
			'tahun_ajaran' => $get_tahun_ajaran,
			'instrumen' => $getInstrumen,
			'get_kelas' => $get_kelas,
			'get_profil' => $get_profil
		];

		$this->load->view('main', $data, FALSE);
	}

	public function getkelas()
	{
		$post = $this->input->post();
		$get_kelas = $this->Main_model->get_where('kelas', [
			'user_id' => $this->session->userdata('id'),
			'tahun_ajaran' => $post['tahunajaran']
		]);

		echo "<option value=''>Pilih Kelas yang tersedia</option>";
		foreach ($get_kelas as $val) {
			if ($post['kelas'] == $val['id']) {
				$selected = 'selected';
			} else {
				$selected = '';
			}
			echo "<option value='" . $val['id'] . "' " . $selected . ">" . $val['kelas'] . "</option>";
		}
	}
}
