<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
	}

	public function login()
	{
		$data['content'] = 'login.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}

	public function authentication(){
		$post = $this->input->post();
		$getUser = $this->Main_model->get_where('user',array('email'=>$post['e-mail'],'password'=>$post['password']));
		$getUserInfo = $this->Main_model->get_where('user_info',array('user_id'=>@$getUser[0]['id']));
		if (@$getUser[0]['email'] && @$getUser[0]['password'])
		{
			$datauser = array(
				'id' => $getUser[0]['id'],
				'email'  => $getUser[0]['email'],
				'password' => $getUser[0]['password'],
				'status'=>$getUserInfo[0]['status'],
				'logged_in' => TRUE
			);
			
			$this->session->set_userdata($datauser);
			
			redirect('dashboard');
		} else {
			$this->session->set_flashdata('error','Maaf, email dan password anda tidak cocok');
			redirect('auth/login');
		}
	}

	public function register()
	{
		$data['content'] = 'register.php';
		$data['tipe_member'] = 'siswa';

		$this->load->view('main.php', $data, FALSE);
	}

	public function register_save(){
		$post = $this->input->post();
		$get_profil = $this->Main_model->get_where('user',array('email'=>$post['email']));
		if($get_profil) {
			$this->session->set_flashdata('error', 'error');
			$this->session->set_flashdata('msg', 'Email telah terdaftar. Silahkan gunakan email lain.');
			$this->session->set_flashdata('type', 'email');
			$this->session->set_flashdata('value', $post);
			redirect('auth/register');
		} elseif($post['password']!=$post['password_conf']){
			$this->session->set_flashdata('error', 'error');
			$this->session->set_flashdata('msg', 'Kata sandi dan konfirmasi kata sandi harus sama.');
			$this->session->set_flashdata('type', 'password');
			$this->session->set_flashdata('value', $post);
			redirect('auth/register');
		} else{
			$user['email'] = $post['email'];
			$user['password'] = $post['password'];
			$user['date_created'] = date('Y-m-d H:i:s');
			$user['date_modified'] = date('Y-m-d H:i:s');

			$user_id = $this->Main_model->insert_data('user',$user);

			$user_info['user_id'] = $user_id;
			$user_info['nama_lengkap'] = $post['nama_lengkap'];
			$user_info['jenis_kelamin'] = $post['jenis_kelamin'];
			$user_info['no_whatsapp'] = $post['no_whatsapp'];
			$user_info['instansi'] = $post['instansi'];
			$user_info['jenjang'] = $post['jenjang'];
			$user_info['alamat_instansi'] = $post['alamat_instansi'];
			$user_info['date_created'] = date('Y-m-d H:i:s');
			$user_info['date_modified'] = date('Y-m-d H:i:s');

			if ($post['jenjang']=='1' || $post['jenjang']=='2' || $post['jenjang']=='3') {
				$user_info['status'] = 'Guru BK';
			} else {
				$user_info['status'] = 'Konselor';
			}

			$user_id = $this->Main_model->insert_data('user_info',$user_info);

			$this->session->set_flashdata('success', 'success');
			redirect('auth/login');
		}
		
	}

	public function logout(){
		$this->session->sess_destroy();
		redirect('auth/login');
	}

}

/* End of file Member.php */
/* Location: ./application/controllers/Member.php */	