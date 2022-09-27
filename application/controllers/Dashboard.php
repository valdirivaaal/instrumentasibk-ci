<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
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
		$data['content'] = 'dashboard.php';
		$data['get_profil'] = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$data['get_kopsurat'] = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$data['get_konselor'] = $this->Main_model->get_where('user_konselor', array('user_id' => $this->session->userdata('id')), 'nama_lengkap', 'asc');
		$data['get_kelas'] = $this->Main_model->get_where('kelas', array('user_id' => $this->session->userdata('id')));
		$data['get_kelompok'] = $this->Main_model->get_where('kelompok', array('user_id' => $this->session->userdata('id')));
		$data['get_ticket'] = $this->Main_model->get_where('ticket', array('user_id' => $this->session->userdata('id')));

		$skor = 0;
		if (@emptyElementExists($data['get_profil'][0]) == FALSE) {
			$skor += 1;
		}

		unset($data['get_kopsurat'][0]['baris_kelima']);
		if (@emptyElementExists($data['get_kopsurat'][0]) == FALSE) {
			$skor += 1;
		}

		if (@emptyElementExists($data['get_konselor'][0]) == FALSE) {
			$skor += 1;
		}

		if (@emptyElementExists($data['get_kelas'][0]) == FALSE) {
			$skor += 1;
		}

		unset($data['get_kelompok'][0]['siswa']);
		if (@emptyElementExists($data['get_kelompok'][0]) == FALSE) {
			$skor += 1;
		}

		if (@emptyElementExists($data['get_ticket'][0]) == FALSE) {
			$skor += 1;
		}

		$data['persentase'] = ($skor / 6) * 100;
		$data['skor'] = $skor;

		$this->load->view('main.php', $data, FALSE);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/Dashboard.php */
