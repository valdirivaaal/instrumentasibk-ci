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

		// Get config
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

		// Get list kelas
		$kelas = $this->Main_model->join(
			'kelas',
			'*,kelas.id as id',
			[
				[
					'table' => 'user_konselor',
					'parameter' => 'user_konselor.id=kelas.konselor_id'
				]
			],
			[
				'kelas.user_id' => $config[0]['user_id']
			],
			'kelas',
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

		$isResponded = $this->Main_model->get_where(
			'sosiometri_respon',
			[
				'id_sosiometri' => $req['id_sosiometri'],
				'id_siswa' => $req['id_siswa']
			]
		);

		if (!$isResponded) {
			// Restructuring data
			$data = [
				'id_sosiometri' => $req['id_sosiometri'],
				'id_siswa' => $req['id_siswa'],
				'pilihan' => serialize($req['pilihan']),
				'pilihan_negatif' => $req['pilihan_negatif']
			];

			// Insert new record
			$this->Main_model->insert_data('sosiometri_respon', $data);
			$this->session->set_flashdata('success', 'angket');
		} else {
			// Restructuring data
			$data = [
				'id_sosiometri' => $req['id_sosiometri'],
				'id_siswa' => $req['id_siswa'],
				'pilihan' => serialize($req['pilihan']),
				'pilihan_negatif' => $req['pilihan_negatif']
			];

			// Update existing record
			$this->Main_model->update_data('sosiometri_respon', $req, ['id' => $isResponded['id']]);
			$this->session->set_flashdata('success', 'angket');
		}
	}
}
