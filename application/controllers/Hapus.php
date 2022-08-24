<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hapus extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	function _remap($parameter)
	{

		$this->index($parameter);
	}

	public function index()
	{
		$tipe =  $this->uri->segment(2);
		$kelompok =  $this->uri->segment(3);
		$individu =  $this->uri->segment(4);

		if ($tipe == 'user_admin') {
			$this->HapusUser($kelompok);
		} else {
			return redirect('dashboard');
		}
	}

	protected function HapusUser($id = '')
	{
		$get_user = $this->Main_model->get_where('user_info', [
			'user_id' => $this->session->userdata('id')
		]);

		if ($get_user[0]['level'] != 'admin') {
			$this->output->set_status_header(401);
			redirect(base_url() . 'dashboard');
		}

		if (!$id) {
			return redirect('dashboard');
		} else {
			$this->Main_model->delete_data('user_info', array('user_id' => $id));
			$this->Main_model->delete_data('user', array('id' => $id));
			redirect('admin/user');
		}
	}
}
