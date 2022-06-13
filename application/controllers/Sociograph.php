<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sociograph extends CI_Controller {

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
		$data['content'] = 'sociograph.php';
		
		$this->load->view('main.php', $data, false);
	}
}
