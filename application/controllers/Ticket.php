<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	public function save()
	{
		$post = $this->input->post();
		$getProfil = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		if ($getProfil[0]['status'] == 'Guru BK') {
			$tipe = 1;
		} else {
			$tipe = 2;
		}
		$getKey = $this->Main_model->get_where('event_key', array('event_key' => $post['event_key'], 'status' => 'Inactive', 'tipe' => $tipe));
		if ($getKey) {
			$data['user_id'] = $this->session->userdata('id');
			$data['event_key'] = $getKey[0]['id'];
			$date = date("Y-m-d");
			$mod_date = strtotime($date . "+ " . $getKey[0]['masa_berlaku'] . " days");
			$data['tgl_kadaluarsa'] = date("Y-m-d", $mod_date);
			$data['date_created'] = date('Y-m-d H:i:s');

			if ($getKey[0]['key_type'] == 'single') {
				$event_key['status'] = 'Active';
			} else if ($getKey[0]['key_type'] == 'multi') {
				$event_key['status'] = 'Inactive';
			}

			$this->Main_model->insert_data('ticket', $data);
			$this->Main_model->update_data('event_key', $event_key, array('id' => $getKey[0]['id']));
			$this->session->set_flashdata('success', $post['event_key']);
			$this->session->set_flashdata('msg', 'event key');
			redirect('aum');
		} else {
			$this->session->set_flashdata('error', 'error');
			$this->session->set_flashdata('msg', 'Event key tidak tersedia. Silahkan coba lagi.');
			redirect('aum');
		}
	}

	public function save_dcm()
	{
		$post = $this->input->post();
		$getProfil = $this->Main_model->get_where('user_info', array('user_id' => $this->session->userdata('id')));
		$getKey = $this->Main_model->get_where('event_key', array('event_key' => $post['event_key'], 'status' => 'Inactive', 'tipe' => 3));
		if ($getKey) {
			$data['user_id'] = $this->session->userdata('id');
			$data['event_key'] = $getKey[0]['id'];
			$date = date("Y-m-d");
			$mod_date = strtotime($date . "+ " . $getKey[0]['masa_berlaku'] . " days");
			$data['tgl_kadaluarsa'] = date("Y-m-d", $mod_date);
			$data['date_created'] = date('Y-m-d H:i:s');

			if ($getKey[0]['key_type'] == 'single') {
				$event_key['status'] = 'Active';
			} else if ($getKey[0]['key_type'] == 'multi') {
				$event_key['status'] = 'Inactive';
			}

			$this->Main_model->insert_data('ticket', $data);
			$this->Main_model->update_data('event_key', $event_key, array('id' => $getKey[0]['id']));
			$this->session->set_flashdata('success', 'sukses');
			$this->session->set_flashdata('msg', 'event key');
			redirect('dcm');
		} else {
			$this->session->set_flashdata('error', 'error');
			$this->session->set_flashdata('msg', 'Event key tidak tersedia. Silahkan coba lagi.');
			redirect('dcm');
		}
	}
}

/* End of file Ticket.php */
/* Location: ./application/controllers/Ticket.php */
