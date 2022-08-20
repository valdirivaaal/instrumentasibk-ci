<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sosiometri extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');

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

		$data = [
			'get_kelas' => $kelas,
			'get_konselor' => $konselor,
			'get_profil' => $profil,
			'codeSettled' => $codeSettled,
			'content' => 'layouts/sosiometri/index.php'
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
}
