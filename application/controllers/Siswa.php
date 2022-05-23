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
}
