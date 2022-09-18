<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
	}

	public function dashboard()
	{
		$data['get_user'] = $this->Main_model->get('user');
		$data['get_transaksi'] = $this->Main_model->get('admin_keuangan');
		$data['get_key_guru'] = $this->Main_model->get_where('event_key',array('status'=>'Inactive','tipe'=>1));
		$data['get_key_konselor'] = $this->Main_model->get_where('event_key',array('status'=>'Inactive','tipe'=>2));
		$data['get_bulan'] = array('01'=>'Januari','02'=>'Februari','03'=>'Maret','04'=>'April','05'=>'Mei','06'=>'Juni','07'=>'Juli','08'=>'Agustus','09'=>'September','10'=>'Oktober','11'=>'November','12'=>'Desember');
		$data['content'] = 'admin/dashboard';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function login(){
		$data['content'] = 'admin/login';
		$data['tipe_member'] = 'siswa';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function authentication(){
		$post = $this->input->post();
		$getUser = $this->Main_model->get_where('admin_user',array('username'=>$post['username'],'password'=>$post['password']));
		if (@$getUser[0]['username'] && @$getUser[0]['password'])
		{
			$datauser = array(
				'id' => $getUser[0]['id'],
				'username'  => $getUser[0]['username'],
				'password' => $getUser[0]['password'],
				'role'=>$getUser[0]['role'],
				'logged_in' => TRUE
			);
			
			$this->session->set_userdata($datauser);
			
			redirect('admin/index');
		} else {
			$this->session->set_flashdata('error','Maaf, username dan password anda tidak cocok');
			redirect('admin/login');
		}
	}

	public function transaksi($m="")
	{
		$data['get_transaksi'] = $this->Main_model->get_where('admin_keuangan',array('month(tanggal_transaksi)'=>$m));
		$data['m'] = $m;
		$data['content'] = 'admin/transaksi';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function transaksi_add($id="")
	{
		$data['get_transaksi'] = $this->Main_model->get_where('admin_keuangan',array('id'=>$id));
		$data['content'] = 'admin/transaksi_add';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function transaksi_save(){
		$post = $this->input->post();
		$post['tanggal_transaksi'] = date('Y-m-d', strtotime($post['tanggal_transaksi']));
		$getEventKey = $this->Main_model->get_where('event_key',array('event_key'=>$post['event_key']));
		if ($getEventKey) {
			if ($post['id']) {
				$post['date_modified'] = date('Y-m-d H:i:s');
				$post['updated_by'] = $this->session->userdata('id');
				$this->Main_model->update_data('admin_keuangan',$post,array('id'=>$post['id']));
			} else {
				$post['date_created'] = date('Y-m-d H:i:s');
				$post['created_by'] = $this->session->userdata('id');
				$this->Main_model->insert_data('admin_keuangan',$post);
			}
			$this->session->set_flashdata('success','transaksi');
			redirect('admin/transaksi');
		} else {
			if ($post['id']) {
				$this->session->set_flashdata('error',$post);
				redirect('admin/transaksi_add/'.$post['id']);
			} else {
				$this->session->set_flashdata('error',$post);
				redirect('admin/transaksi_add');
			}
		} 
	}

	public function transaksi_hapus($id=""){
		$this->Main_model->delete_data('admin_keuangan',array('id'=>$id));
		redirect('admin/transaksi');
	}

	public function user()
	{
		$data['get_user'] = $this->Main_model->join('ticket','*,ticket.date_created as date_created,user_info.status as status',array(array('table'=>'user_info','parameter'=>'ticket.user_id=user_info.user_id'),array('table'=>'event_key','parameter'=>'ticket.event_key=event_key.id')),array('tgl_kadaluarsa >='=> date('Y-m-d')));
		$data['content'] = 'admin/user';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function key_available(){
		$data['get_key_guru'] = $this->Main_model->get_where('event_key',array('status'=>'Inactive','tipe'=>1));
		$data['get_key_konselor'] = $this->Main_model->get_where('event_key',array('status'=>'Inactive','tipe'=>2));
		$data['content'] = 'admin/key_available';
		$data['status'] = 'Tersedia';

		$this->load->view('admin/main.php', $data, FALSE);
	}

	public function key_used(){
		$data['get_key_guru'] = $this->Main_model->get_where('event_key',array('status'=>'Active','tipe'=>1));
		$data['get_key_konselor'] = $this->Main_model->get_where('event_key',array('status'=>'Active','tipe'=>2));
		$data['content'] = 'admin/key_available';
		$data['status'] = 'Terpakai';

		$this->load->view('admin/main.php', $data, FALSE);
	}
}

/* End of file Admin.php */
/* Location: ./application/controllers/Admin.php */