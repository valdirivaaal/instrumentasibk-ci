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

	public function detail($idKelas)
	{
		// Get sosiometri respon by class
		$sosiometriResponse = $this->Main_model->join(
			'sosiometri_respon',
			'*',
			[
				[
					'table' => 'kelas_siswa',
					'parameter' => 'kelas_siswa.id=sosiometri_respon.id_siswa'
				]
			],
			[
				'kelas_siswa.id_kelas' => $idKelas
			],
			'kelas_siswa.nis',
			'asc'
		);

		// Class info
		$kelas = $this->Main_model->get_where('kelas', ['id' => $idKelas]);

		// Gender count
		$genderCount = $this->getGenderCount($this->getStudentByClass($idKelas));

		// printA($sosiometriResponse);
		// printA($kelas);
		// printA($this->unserializeDecision($sosiometriResponse));

		$data = [
			'data' => [
				'tabulasi' => [
					'data' => $this->getStudentByClassWithResponse($idKelas),
					'dataTotal' => count($this->getStudentByClassWithResponse($idKelas)),
					'studentTotal' => count($this->getStudentByClass($idKelas)),
					'girls' => $genderCount['girls'],
					'boys' => $genderCount['boys'],
				],
				'details' => $this->unserializeDecision($sosiometriResponse),
				'responded' => $sosiometriResponse ? count($sosiometriResponse) : 0,
				'kelas_total' => $kelas ? $kelas[0]['jumlah_siswa'] : 0,
				'kelas_detail' => $kelas ? $kelas[0] : [],
			],
			'content' => 'layouts/sosiometri/sosiometri.detail.php'
		];

		$this->load->view('main.php', $data, false);
	}

	public function getSociogramData($idKelas)
	{
		if (!$idKelas) {
			$return = json_encode([
				'success' => false,
				'message' => 'ID Kelas not found'
			]);
		}

		$data = $this->getStudentByClassWithResponse($idKelas);

		if (!$data) {
			$return = json_encode([
				'success' => false,
				'message' => 'Data not found'
			]);
		}

		foreach ($data as $index => $row) {
			if ($row['pilihan']) {
				if ($row['pilihan_negatif']) {
					$connections = $row['pilihan'];
					array_push($connections, $row['pilihan_negatif']);

					$row['connections'] = $connections;
				} else {
					$row['connections'] = $row['pilihan'];
				}
			} else {
				$row['connections'] = [];
			}

			$data[$index] = $row;
		}

		$return = json_encode([
			'success' => true,
			'data' => $data
		]);

		return $this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output($return);
	}

	public function calculateBobotPenilaian($data)
	{
		if (!$data) {
			return [];
		}

		foreach ($data as $i => $row) {
			$bobotPemilih = 0;
			if ($row['pilihan'] && $row['bobot_penilaian']) {
				foreach ($row['pilihan'] as $index => $id) {
					$bobotPemilih += $row['bobot_penilaian'][$index];
				}
			}

			$row['score_pemilih'] = $bobotPemilih;
			$row['score_penolak'] = $row['pilihan_negatif'] ? 1 : 0;

			$data[$i] = $row;
		}

		return $data;
	}

	public function getStudentByClassWithResponse($idKelas)
	{
		if (!$idKelas) {
			return [];
		}

		$data = $this->Main_model->join(
			'kelas_siswa',
			'kelas_siswa.*, 
			sosiometri_respon.pilihan as pilihan, 
			sosiometri_respon.pilihan_negatif as pilihan_negatif,
			sosiometri.bobot_penilaian as bobot_penilaian',
			[
				[
					'table' => 'sosiometri_respon',
					'parameter' => 'sosiometri_respon.id_siswa=kelas_siswa.id'
				],
				[
					'table' => 'sosiometri',
					'parameter' => 'sosiometri.id=sosiometri_respon.id_sosiometri'
				]
			],
			[
				'kelas_siswa.id_kelas' => $idKelas
			],
			'kelas_siswa.nis',
			'asc'
		);

		if (!$data) {
			return [];
		}

		// $data = $this->unserializeDecision($data);
		foreach ($data as $index => $row) {
			$data[$index]['pilihan'] = unserialize($row['pilihan']);
			$data[$index]['bobot_penilaian'] = unserialize($row['bobot_penilaian']);
		}

		$data = $this->calculateBobotPenilaian($data);

		// printA($data);

		return $data ? $data : [];
	}

	public function getStudentByClass($idKelas)
	{
		if (!$idKelas) {
			return [];
		}

		$data = $this->Main_model->get_where('kelas_siswa', ['id_kelas' => $idKelas]);

		return $data ? $data : [];
	}

	public function getGenderCount($data)
	{
		if (!$data) {
			return [
				'girls' => 0,
				'boys' => 0
			];
		}

		$girls = $boys = [];
		foreach ($data as $row) {
			if ($row['jk'] == 'P') {
				$girls[] = $row;
			} else {
				$boys[] = $row;
			}
		}

		return [
			'girls' => $girls ? count($girls) : 0,
			'boys' => $boys ? count($boys) : 0,
		];
	}

	public function unserializeDecision($data)
	{
		if (!$data) {
			return [];
		}

		foreach ($data as &$row) {
			$tempDecision = [];

			$decisions = unserialize($row['pilihan']);

			if ($decisions) {
				foreach ($decisions as $decision) {
					$tempDecision[] = $this->getSiswaDetail($decision);
				}
			}

			$row['pilihan'] = $tempDecision;
		}

		return $data;
	}

	public function getSiswaDetail($idSiswa)
	{
		if (!$idSiswa) {
			return [];
		}

		$data = $this->Main_model->get_where('kelas_siswa', ['id' => $idSiswa]);

		return $data ? $data[0] : [];
	}
}
