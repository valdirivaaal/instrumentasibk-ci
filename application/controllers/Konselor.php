<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Konselor extends CI_Controller {

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
		$data['get_konselor'] = $this->Main_model->get_where('user_konselor',array('user_id'=>$this->session->userdata('id')),'nama_lengkap','asc');
		$data['content'] = 'konselor.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function tambah($id="")
	{
		$data['get_konselor'] = $this->Main_model->get_where('user_konselor',array('id'=>$id));
		$data['content'] = 'konselor_tambah.php';

		$this->load->view('main.php', $data, FALSE);
	}

	public function save(){
		$post = $this->input->post();
		$post['user_id'] = $this->session->userdata('id');
		$post['date_created'] = date('Y-m-d H:i:s');
		if ($post['id']) {
			$this->Main_model->update_data('user_konselor',$post,array('id'=>$post['id']));
		} else {
			$this->Main_model->insert_data('user_konselor',$post);
		}
		$this->session->set_flashdata('success','guru');
		redirect('konselor');
	}

	public function hapus($id=""){
		$this->Main_model->delete_data('user_konselor',array('id'=>$id));
		redirect('konselor');
	}

}

/* End of file Konselor.php */
/* Location: ./application/controllers/Konselor.php */