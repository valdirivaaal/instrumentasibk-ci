<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	public function edit($id)
	{
		$siswa = [];

		if (!empty($id)) {
			$siswa = $this->Main_model->get_where('kelas_siswa', ['id' => $id]);
		}
			
		$param = [
			'data' => $siswa,
			'content' => "layouts/siswa/siswa.edit.php"
		];

		$this->load->view('main.php', $param, FALSE);
	}

	public function saveEdit()
	{
		$req = $this->input->post();

		// Set input value
		$req['tgl_lahir'] = str_replace("/", "-", $req['tgl_lahir']);
		$req['tgl_lahir'] = date('Y-m-d', strtotime($req['tgl_lahir']));
		$req['updated_at'] = date('Y-m-d H:i:s');
		
		// Get siswa details
		$siswa = $this->Main_model->get_where('kelas_siswa', ['id' => $req['id']]);

		$this->Main_model->update_data('kelas_siswa', $req, ['id' => $req['id']]);
		$this->session->set_flashdata('success','siswa');
		redirect('kelas/detail/' . $siswa[0]['id_kelas']);
	}

	public function delete($id)
	{

	}
}
