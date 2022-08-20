<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Siswa extends CI_Controller {
	
	public function __construct()
	{
		parent::__construct();
		date_default_timezone_set("Asia/Jakarta");

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

		if (!$siswa) {
			redirect('kelas');
		}
			
		$param = [
			'data' => $siswa,
			'content' => "layouts/siswa/siswa.edit.php"
		];

		$this->load->view('main.php', $param, FALSE);
	}

	public function editSave()
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
		$siswa = $this->Main_model->get_where('kelas_siswa', ['id' => $id]);

		$response = [
			"success" => false,
			"msg" => "Delete student data failure!"
		];

		if ($siswa) {
			$isDeleted = $this->Main_model->delete_data('kelas_siswa', ['id' => $id]);

			$response = [
				"success" => true,
				"data" => $siswa[0]
			];
		}

		// echo json_encode($response);
		return $this->output
            ->set_content_type('application/json')
            ->set_status_header(200)
            ->set_output(json_encode(array(
                    'success' => true,
					'data' => $siswa[0]
            )));
	}

	public function siswa_biodata($id)
	{
		$kelas = $this->Main_model->get_where('kelas', ['user_id' => $id]);

		$data = [
			'kelas' => $kelas,
			'content' => 'layouts/siswa/siswa.biodata.php',
			'tipe_member' => 'siswa'
		];
		
		$this->load->view('main.php', $data, false);
	}
	
	public function getNis()
	{
		$param = $this->input->post();

		if (!$param['id']) {
			$result = [
				"success" => false,
				"message" => "Kelas ID not found."
			];
		}

		$nis = $this->Main_model->get_where('kelas_siswa', ['id_kelas' => $param['id']]);

		if ( (!$nis) || (count($nis) < 1) ) {
			$result = [
				'success' => false,
				'message' => "Siswa data not found."
			];
		} else {
			$result = [
				'success' => true,
				'nis' => $nis
			];
		}

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode($result));
	}

	public function getSiswa()
	{
		$param = $this->input->post();

		if (!$param['nis']) {
			$result = [
				'success' => false,
				'message' => 'NIS not found.'
			];
		}

		$siswa = $this->Main_model->get_where('kelas_siswa', ['nis' => $param['nis']]);

		if (!$siswa) {
			$result = [
				'success' => false,
				'message' => 'Siswa data not found.'
			];
		} else {
			$result = [
				'success' => true,
				'siswa' => $siswa[0]
			];
		}

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode($result));
	}

	public function saveBiodata()
	{
		$request = $this->input->post();

		if (!$request['id']) {
			$this->session->set_flashdata('error', 'siswa');
			return false;
		}

		if ($request['id'] == '') {
			$this->session->set_flashdata('error', 'siswa');
			return false;
		}

		// Set input value
		$request['tgl_lahir'] = str_replace("/", "-", $request['tgl_lahir']);
		$request['tgl_lahir'] = date('Y-m-d', strtotime($request['tgl_lahir']));
		$request['updated_at'] = date('Y-m-d H:i:s');

		// Get siswa details
		$siswa = $this->Main_model->get_where('kelas_siswa', ['id' => $request['id']]);

		$this->Main_model->update_data('kelas_siswa', $request, ['id' => $request['id']]);
		$this->session->set_flashdata('success','siswa');
		// redirect('kelas/detail/' . $siswa[0]['id_kelas']);
		redirect($_SERVER['HTTP_REFERER']);

	}
}
