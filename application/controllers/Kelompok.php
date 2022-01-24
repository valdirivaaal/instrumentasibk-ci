<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kelompok extends CI_Controller {

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
		$data['get_kelompok'] = $this->Main_model->get_where('kelompok',array('user_id'=>$this->session->userdata('id')));
		$data['content'] = 'kelompok.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function tambah($id="")
	{
		$data['get_kelompok'] = $this->Main_model->get_where('kelompok',array('id'=>$id));
		$data['get_kelas'] = $this->Main_model->get_where('kelas',array('user_id'=>$this->session->userdata('id')));
		$data['content'] = 'kelompok_edit.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function save()
	{
		$post = $this->input->post();
		if ($post['id']) {
			$data['nama_kelompok'] = $post['nama_kelompok'];
			$data['user_id'] = $this->session->userdata('id');
			$data['kelas'] = implode(",", $post['kelas']);
			$data['date_modified'] = date('Y-m-d H:i:s');

			$this->Main_model->update_data('kelompok',$data,array('id'=>$post['id']));
		} else {
			$data['nama_kelompok'] = $post['nama_kelompok'];
			$data['user_id'] = $this->session->userdata('id');
			$data['kelas'] = implode(",", $post['kelas']);
			$data['date_created'] = date('Y-m-d H:i:s');

			$this->Main_model->insert_data('kelompok',$data);
		}

		$this->session->set_flashdata('success','kelompok');
		redirect('kelompok');
	}

	public function hapus($id=""){
		$this->Main_model->delete_data('kelompok',array('id'=>$id));
		redirect('kelas');
	}

}

/* End of file Kelompok.php */
/* Location: ./application/controllers/Kelompok.php */