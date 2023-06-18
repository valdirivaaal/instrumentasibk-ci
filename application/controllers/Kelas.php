<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kelas extends CI_Controller
{

	private $filename = 'import_data';

	public function __construct()
	{
		parent::__construct();

		require_once APPPATH . 'third_party/PHPExcel-1.8/Classes/PHPExcel.php';

		$this->load->model('Main_model');
		$this->load->model('SiswaModel');

		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	public function index()
	{
		$data['get_kelas'] = $this->Main_model->join(
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

		$data['get_konselor'] = $this->Main_model->get_where(
			'user_konselor',
			array(
				'user_id' => $this->session->userdata('id')
			),
			'nama_lengkap',
			'asc'
		);

		$data['get_profil'] = $this->Main_model->get_where(
			'user_info',
			array(
				'user_id' => $this->session->userdata('id')
			)
		);

		$data['content'] = 'kelas.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function tambah($id = "")
	{
		$data['get_kelas'] = $this->Main_model->get_where('kelas', array('id' => $id));
		$data['get_konselor'] = $this->Main_model->get_where('user_konselor', array('user_id' => $this->session->userdata('id')), 'nama_lengkap', 'asc');
		$data['content'] = 'kelas_edit.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function save()
	{
		$post = $this->input->post();
		if ($post['id']) {
			$post['date_modified'] = date('Y-m-d H:i:s');
			$this->Main_model->update_data('kelas', $post, array('id' => $post['id']));
		} else {
			$post['user_id'] = $this->session->userdata('id');
			$post['date_created'] = date('Y-m-d H:i:s');
			$this->Main_model->insert_data('kelas', $post);
		}


		$this->session->set_flashdata('success', 'kelas');
		redirect('kelas');
	}

	public function sunting($id = "")
	{
		$data['get_kelas'] = $this->Main_model->get_where('kelas', array('id' => $id));
		$data['get_konselor'] = $this->Main_model->get_where('user_konselor', array('user_id' => $this->session->userdata('id')), 'nama_lengkap', 'asc');
		$data['content'] = 'kelas_edit.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function hapus($id = "")
	{
		$this->Main_model->delete_data('kelas', array('id' => $id));
		redirect('kelas');
	}

	public function detail($id)
	{
		// Data kelas
		$kelas = $this->Main_model->get_where('kelas', ['id' => $id]);

		// Data siswa
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

		$result = [
			'siswa' => $siswa,
			'kelas' => $kelas[0]
		];

		$data = [
			'data' => $result,
			'content' => 'layouts/kelas/detail.php',
			'id_kelas' => $id
		];

		$this->load->view('main.php', $data, false);
	}

	public function siswa_upload($id)
	{
		$data = [];

		if (isset($_POST['preview'])) {
			$upload = $this->SiswaModel->upload_file($this->filename);

			if ($upload['result'] == 'success') {
				$excel = new PHPExcel_Reader_Excel2007();
				$loadExcel = $excel->load('excel/' . $this->filename . '.xlsx');
				$sheet = $loadExcel->getActiveSheet()->toArray(null, true, true, true);

				$data['sheet'] = $sheet;
			} else {
				$data['upload_error'] = $upload['error'];
			}
		}

		$data['content'] = 'layouts/siswa/siswa.upload.php';
		$data['id_kelas'] = $id;

		$this->load->view('main.php', $data, false);
	}

	public function siswa_import($id)
	{
		$excel = new PHPExcel_Reader_Excel2007();
		$loadExcel = $excel->load('excel/' . $this->filename . '.xlsx');
		$sheet = $loadExcel->getActiveSheet()->toArray(null, true, true, true);

		$siswa = [];
		// printA($sheet);
		$numrow = 1;
		foreach ($sheet as $row) {
			if ($numrow > 1) {
				array_push($siswa, [
					'id_kelas' => $id,
					'nis' => $row['A'],
					'nama' => $row['B'],
					'jk' => $row['C'],
					'alamat' => $row['D']
				]);
			}

			$numrow++;
		}
		// printA($siswa);

		$this->SiswaModel->insert_multiple($siswa);

		$this->session->set_flashdata('success', 'siswa');

		redirect('/kelas/detail/' . $id);
	}
}

/* End of file Kelas.php */
/* Location: ./application/controllers/Kelas.php */
