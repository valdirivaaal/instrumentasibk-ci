<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelas extends CI_Controller {

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
		$data['get_kelas'] = $this->Main_model->join(
			'kelas',
			'*,kelas.id as id',
			array(
				array(
					'table'=>'user_konselor',
					'parameter'=>'user_konselor.id=kelas.konselor_id')
				),
				array('kelas.user_id'=>$this->session->userdata('id')
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

	public function tambah($id="")
	{
		$data['get_kelas'] = $this->Main_model->get_where('kelas',array('id'=>$id));
		$data['get_konselor'] = $this->Main_model->get_where('user_konselor',array('user_id'=>$this->session->userdata('id')),'nama_lengkap','asc');
		$data['content'] = 'kelas_edit.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function save(){
		$post = $this->input->post();
		if ($post['id']) {
			$post['date_modified'] = date('Y-m-d H:i:s');
			$this->Main_model->update_data('kelas',$post,array('id'=>$post['id']));
		} else {
			$post['user_id'] = $this->session->userdata('id');
			$post['date_created'] = date('Y-m-d H:i:s');
			$this->Main_model->insert_data('kelas',$post);
		}


		$this->session->set_flashdata('success','kelas');
		redirect('kelas');
	}

	public function sunting($id=""){
		$data['get_kelas'] = $this->Main_model->get_where('kelas',array('id'=>$id));
		$data['get_konselor'] = $this->Main_model->get_where('user_konselor',array('user_id'=>$this->session->userdata('id')),'nama_lengkap','asc');
		$data['content'] = 'kelas_edit.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function hapus($id=""){
		$this->Main_model->delete_data('kelas',array('id'=>$id));
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
}

/* End of file Kelas.php */
/* Location: ./application/controllers/Kelas.php */
