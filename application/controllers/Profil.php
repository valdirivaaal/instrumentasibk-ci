<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profil extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	public function test_photo()
	{
		$data = $_POST['image'];

		list($type, $data) = explode(';', $data);
		list(, $data)      = explode(',', $data);

		$data = base64_decode($data);
		$imageName = time() . '.png';
		file_put_contents('upload/' . $imageName, $data);

		echo 'done';
	}

	public function index()
	{
		$data['content'] = 'profil.php';
		$data['get_profil'] = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$this->load->view('main.php', $data, FALSE);
	}

	public function edit()
	{
		$data['content'] = 'profil_edit.php';
		$data['get_profil'] = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$this->load->view('main.php', $data, FALSE);
	}

	public function save()
	{
		$post = $this->input->post();
		$get_profil = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$post['tanggal_lahir'] = str_replace("/", "-", $post['tanggal_lahir']);
		$post['tanggal_lahir'] = date('Y-m-d', strtotime($post['tanggal_lahir']));
		$post['date_modified'] = date('Y-m-d H:i:s');

		if (!is_dir('uploads/foto_profil/' . $this->session->userdata('id'))) {
			mkdir('uploads/foto_profil/' . $this->session->userdata('id'), 0777, true);
		}

		$config['upload_path']          = 'uploads/foto_profil/' . $this->session->userdata('id');
		$config['allowed_types']        = 'gif|jpg|png';
		$config['encrypt_name']			= TRUE;
		$config['width']				= 100;
		$config['height']				= 200;

		$this->load->library('upload', $config);

		$this->upload->initialize($config);

		if (!empty($_FILES['foto'])) {
			if (!$this->upload->do_upload('foto') && isset($get_profil[0]['foto'])) {
				$error = [
					'error' => $this->upload->display_errors(),
					'success' => false
				];
			} else {
				$data = array('upload_data' => $this->upload->data());

				// $this->load->view('upload_success', $data);
				$success = [
					'success' => true,
					'msg' => $data
				];

				$foto_profil = $data;
			}

			$post['foto'] = @$foto_profil['upload_data']['file_name'];
		}


		$this->Main_model->update_data('user_info', $post, array('user_id' => $this->session->userdata('id')));
		$this->session->set_flashdata('success', 'profil');
		redirect('profil');
	}
  
	public function upload()
	{
		if (!is_dir('uploads/foto_profil/' . $this->session->userdata('id'))) {
			mkdir('uploads/foto_profil/' . $this->session->userdata('id'), 0777, true);
		}

		$config['upload_path']          = 'uploads/foto_profil/' . $this->session->userdata('id');
		$config['allowed_types']        = 'gif|jpg|png';
		$config['encrypt_name']			= TRUE;
		$config['max_size']             = 2000;

		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		if (!$this->upload->do_upload('uploaded_img')) {
			$this->session->set_flashdata('error', $this->upload->display_errors());
			redirect('profil');
		} else {
			$fileData = $this->upload->data();
			$post['foto'] = $fileData['file_name'];
			$this->Main_model->update_data('user_info', $post, array('user_id' => $this->session->userdata('id')));
			$this->session->set_flashdata('success', 'foto profil');
			redirect('profil');
		}
	}
  
	public function hapus_foto($id = "")
	{
		$get_profil = $this->Main_model->get_where('user_info', array('user_id' => $id));
		$post['foto'] = '';
		unlink(FCPATH . '/uploads/foto_profil/' . $id . '/' . $get_profil[0]['foto']);
		$this->Main_model->update_data('user_info', $post, array('user_id' => $this->session->userdata('id')));
		$this->session->set_flashdata('success', 'Foto berhasil dihapus.');
		redirect('profil/edit');
	}

	public function hapus_logokanan($id = "")
	{
		$get_profil = $this->Main_model->get_where('user_surat', array('user_id' => $id));
		$post['logo_kanan'] = '';
		unlink(FCPATH . '/uploads/logo/' . $id . '/' . $get_profil[0]['logo_kanan']);
		$this->Main_model->update_data('user_surat', $post, array('user_id' => $this->session->userdata('id')));
		$this->session->set_flashdata('success', 'Logo kanan berhasil dihapus.');
		redirect('profil/kop_surat');
	}

	public function hapus_logokiri($id = "")
	{
		$get_profil = $this->Main_model->get_where('user_surat', array('user_id' => $id));
		$post['logo_kiri'] = '';
		unlink(FCPATH . '/uploads/logo/' . $id . '/' . $get_profil[0]['logo_kiri']);
		$this->Main_model->update_data('user_surat', $post, array('user_id' => $this->session->userdata('id')));
		$this->session->set_flashdata('success', 'Logo kiri berhasil dihapus.');
		redirect('profil/kop_surat');
	}

	public function kop_surat()
	{
		$data['content'] = 'kop_surat.php';
		$data['get_kopsurat'] = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));
		$data['get_logo'] = $this->Main_model->get('logo_daerah');

		$this->load->view('main.php', $data, FALSE);
	}

	public function save_kop_surat()
	{
		$post = $this->input->post();
		$post['user_id'] = $this->session->userdata('id');
		$getKopSurat = $this->Main_model->get_where('user_surat', array('user_id' => $this->session->userdata('id')));

		if (!is_dir('uploads/logo/' . $this->session->userdata('id'))) {
			mkdir('uploads/logo/' . $this->session->userdata('id'), 0777, true);
		}

		$config['upload_path']          = 'uploads/logo/' . $this->session->userdata('id');
		$config['allowed_types']        = 'gif|jpg|png';
		$config['encrypt_name']			= TRUE;
		$config['width']				= 100;
		$config['height']				= 200;

		$this->load->library('upload', $config);

		$this->upload->initialize($config);

		if (!$this->upload->do_upload('logo_kiri')) {
			$error = array('error' => $this->upload->display_errors());
		} else {
			$fileData = $this->upload->data();
			$post['logo_kiri'] = $fileData['file_name'];
		}

		if (!$this->upload->do_upload('logo_kanan')) {
			$error = array('error' => $this->upload->display_errors());
		} else {
			$fileData = $this->upload->data();
			$post['logo_kanan'] = $fileData['file_name'];
		}

		if ($getKopSurat) {
			$post['date_modified'] = date('Y-m-d H:i:s');
			$this->Main_model->update_data('user_surat', $post, array('user_id' => $this->session->userdata('id')));
		} else {
			$post['date_created'] = date('Y-m-d H:i:s');
			$this->Main_model->insert_data('user_surat', $post);
		}
		$this->session->set_flashdata('success', 'kop surat');
		redirect('profil');
	}
}

/* End of file Profil.php */
/* Location: ./application/controllers/Profil.php */
