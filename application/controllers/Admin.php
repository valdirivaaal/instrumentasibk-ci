<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');

		$get_user = $this->Main_model->get_where('user_info', [
			'user_id' => $this->session->userdata('id')
		]);

		if ($get_user[0]['level'] != 'admin') {
			$this->output->set_status_header(401);
			redirect(base_url() . 'dashboard');
		}
	}

	public function dashboard()
	{
		$data['get_user'] = $this->Main_model->get('user');
		$data['get_transaksi'] = $this->Main_model->get('admin_keuangan');
		$data['get_key_guru'] = $this->Main_model->get_where('event_key', array('status' => 'Inactive', 'tipe' => 1));
		$data['get_key_konselor'] = $this->Main_model->get_where('event_key', array('status' => 'Inactive', 'tipe' => 2));
		$data['get_bulan'] = array('01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember');
		$data['content'] = 'admin/dashboard';
		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function login()
	{
		$data['content'] = 'admin/login';
		$data['tipe_member'] = 'siswa';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function authentication()
	{
		$post = $this->input->post();
		$getUser = $this->Main_model->get_where('admin_user', array('username' => $post['username'], 'password' => $post['password']));
		if (@$getUser[0]['username'] && @$getUser[0]['password']) {
			$datauser = array(
				'id' => $getUser[0]['id'],
				'username'  => $getUser[0]['username'],
				'password' => $getUser[0]['password'],
				'role' => $getUser[0]['role'],
				'logged_in' => TRUE
			);

			$this->session->set_userdata($datauser);

			redirect('admin/index');
		} else {
			$this->session->set_flashdata('error', 'Maaf, username dan password anda tidak cocok');
			redirect('admin/login');
		}
	}

	public function transaksi($m = "")
	{
		$data['get_transaksi'] = $this->Main_model->get_where('admin_keuangan', array('month(tanggal_transaksi)' => $m));
		$data['m'] = $m;
		$data['content'] = 'admin/transaksi';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function transaksi_add($id = "")
	{
		$data['get_transaksi'] = $this->Main_model->get_where('admin_keuangan', array('id' => $id));
		$data['content'] = 'admin/transaksi_add';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function transaksi_save()
	{
		$post = $this->input->post();
		$post['tanggal_transaksi'] = date('Y-m-d', strtotime($post['tanggal_transaksi']));
		$getEventKey = $this->Main_model->get_where('event_key', array('event_key' => $post['event_key']));
		if ($getEventKey) {
			if ($post['id']) {
				$post['date_modified'] = date('Y-m-d H:i:s');
				$post['updated_by'] = $this->session->userdata('id');
				$this->Main_model->update_data('admin_keuangan', $post, array('id' => $post['id']));
			} else {
				$post['date_created'] = date('Y-m-d H:i:s');
				$post['created_by'] = $this->session->userdata('id');
				$this->Main_model->insert_data('admin_keuangan', $post);
			}
			$this->session->set_flashdata('success', 'transaksi');
			redirect('admin/transaksi');
		} else {
			if ($post['id']) {
				$this->session->set_flashdata('error', $post);
				redirect('admin/transaksi_add/' . $post['id']);
			} else {
				$this->session->set_flashdata('error', $post);
				redirect('admin/transaksi_add');
			}
		}
	}

	public function transaksi_hapus($id = "")
	{
		$this->Main_model->delete_data('admin_keuangan', array('id' => $id));
		redirect('admin/transaksi');
	}

	public function user()
	{
		$data['get_user'] = $this->Main_model->join('ticket', '*,ticket.date_created as date_created,user_info.status as status', array(array('table' => 'user_info', 'parameter' => 'ticket.user_id=user_info.user_id'), array('table' => 'event_key', 'parameter' => 'ticket.event_key=event_key.id')), array('tgl_kadaluarsa >=' => date('Y-m-d')));
		$data['content'] = 'admin/user';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function key_available()
	{
		$data['get_key_guru'] = $this->Main_model->get_where('event_key', array('status' => 'Inactive', 'tipe' => 1));
		$data['get_key_konselor'] = $this->Main_model->get_where('event_key', array('status' => 'Inactive', 'tipe' => 2));
		$data['get_key_dcm'] = $this->Main_model->get_where('event_key', array('status' => 'Inactive', 'tipe' => 3));
		$data['content'] = 'admin/key_available';
		$data['status'] = 'Tersedia';
		$data['state'] = 'available';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function key_used()
	{
		$data['get_key_guru'] = $this->Main_model->get_where('event_key', array('status' => 'Active', 'tipe' => 1));
		$data['get_key_konselor'] = $this->Main_model->get_where('event_key', array('status' => 'Active', 'tipe' => 2));
		$data['get_key_dcm'] = $this->Main_model->get_where('event_key', array('status' => 'Active', 'tipe' => 3));
		$data['content'] = 'admin/key_available';
		$data['status'] = 'Terpakai';
		$data['state'] = 'used';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function logo()
	{
		$data['get_logo'] = $this->Main_model->get('logo_daerah');
		$data['content'] = 'admin/logo';

		$this->load->view('admin/main', $data, false);
	}

	public function tambahLogo()
	{
		$data['content'] = 'admin/logo-tambah';

		$this->load->view('admin/main', $data, false);
	}

	public function logoAction()
	{
		$post = $this->input->post();

		if (!is_dir('uploads/logo/')) {
			mkdir('uploads/logo/', 0777, true);
		}

		$config['upload_path']          = 'uploads/logo/';
		$config['allowed_types']        = 'jpg|png';
		$config['encrypt_name']			= TRUE;
		$config['max_size']             = 2000;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if (!$this->upload->do_upload('uploaded_img')) {
			$this->session->set_flashdata('error', $this->upload->display_errors());
			redirect('admin/logo');
		} else {
			$fileData = $this->upload->data();
			$post['path'] = $fileData['file_name'];
			$this->Main_model->insert_data('logo_daerah', $post);
			$this->session->set_flashdata('success', $post['nama']);
			redirect('admin/logo');
		}
	}

	public function logoedit($id = "")
	{
		$data['get_data'] = $this->Main_model->get_where('logo_daerah', [
			'id' => $id
		]);

		if (empty($data['get_data'])) {
			$this->session->set_flashdata('error', ' ID Tidak Ditemukan');
			redirect('admin/logo');
		}

		$data['content'] = 'admin/logo-edit';

		$this->load->view('admin/main', $data, false);
	}

	public function logoEditAction($id = "")
	{
		$post = $this->input->post();
		var_dump($post);
		$config['upload_path']          = 'uploads/logo/';
		$config['allowed_types']        = 'jpg|png';
		$config['encrypt_name']			= TRUE;
		$config['max_size']             = 2000;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if (!empty($_FILES['uploaded_image'])) {
			if (!$this->upload->do_upload('uploaded_img')) {
				$this->session->set_flashdata('error', $this->upload->display_errors());
				redirect('admin/logo');
			} else {
				$fileData = $this->upload->data();
				$post['path'] = $fileData['file_name'];
			}
		}
		$this->Main_model->update_data('logo_daerah', $post, [
			'id' => $id
		]);
		$this->session->set_flashdata('success', $post['nama']);
		redirect('admin/logo');
	}

	public function logohapus($id = "")
	{
		$this->Main_model->delete_data('logo_daerah', array('id' => $id));
		redirect('admin/logo');
	}

	public function keyEdit($eventkey = "")
	{
		$data['get_data'] = $this->db->query("SELECT *,event_key.event_key as key_code,event_key.status as key_status FROM `event_key` LEFT JOIN ticket ON event_key.id = ticket.event_key LEFT JOIN user_info ON ticket.user_id = user_info.user_id WHERE event_key.event_key = '$eventkey'")->result_array();

		if (empty($data['get_data'])) {
			$this->session->set_flashdata('error', ' ID Tidak Ditemukan');
			redirect($_SERVER['HTTP_REFERER']);
		}

		$data['content'] = 'admin/key-edit';

		$this->load->view('admin/main', $data, false);
	}

	public function keyProses()
	{
		$post = $this->input->post();
		$this->Main_model->update_data('event_key', $post, [
			'event_key' => $post['event_key']
		]);
		redirect(base_url() . 'admin/key_available');
	}
}

/* End of file Admin.php */
/* Location: ./application/controllers/Admin.php */
