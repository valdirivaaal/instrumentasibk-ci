<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SosiometriSiswa extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
	}

	public function sosiometriSiswaPage($code)
	{
		// Get list kelas
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

		// Get config
		// $config = $this->Main_model->get_where('sosiometri', ['url' => $code]);
		$config = $this->Main_model->join(
			'sosiometri',
			'*, sosiometri_pertanyaan.pertanyaan as pertanyaan',
			[
				[
					'table' => 'sosiometri_pertanyaan',
					'parameter' => 'sosiometri_pertanyaan.id = sosiometri.id_pertanyaan'
				]
			],
			[
				'sosiometri.url' => $code
			],
			'sosiometri.id',
			'asc'
		);

		$data = [
			'kelas' => $kelas,
			'config' => $config ? $config[0] : [],
			'content' => 'layouts/sosiometri/sosiometri.siswa.php',
			'tipe_member' => 'siswa'
		];

		$this->load->view('main.php', $data, false);
	}

	public function getSiswa($id)
	{
		$siswa = $this->Main_model->innerJoin(
			"kelas_siswa",
			"kelas.kelas as nama_kelas, kelas_siswa.*",
			[
				[
					"table" => "kelas",
					"parameter" => "kelas_siswa.id_kelas = kelas.id"
				]
			],
			[
				"kelas_siswa.id_kelas" => $id
			]
		);

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode(array(
				'success' => true,
				'data' => $siswa
			)));
	}

	public function getSiswaNotIn()
	{
		$req = $this->input->post();

		$siswa = $this->Main_model->innerJoin2(
			"kelas_siswa",
			"kelas.kelas as nama_kelas, kelas_siswa.*",
			[
				[
					"table" => "kelas",
					"parameter" => "kelas_siswa.id_kelas = kelas.id"
				]
			],
			[
				"kelas_siswa.id_kelas" => $req['id_kelas']
			],
			[
				'field' => 'kelas_siswa.id',
				'rows' => $req['id_siswa']
			]
		);

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode(array(
				'success' => true,
				'data' => $siswa
			)));
	}

	public function angketSiswa()
	{
		$req = $this->input->post();

		printA($req);
	}
}
