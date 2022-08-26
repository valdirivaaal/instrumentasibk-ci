<?php
defined('BASEPATH') or exit('No direct script access allowed');

class GetModel extends CI_Model
{
	public function getLastTicket($user_id = '')
	{
		return $this->db->query("SELECT * FROM user_info LEFT JOIN ticket ON ticket.user_id = user_info.user_id JOIN event_key ON event_key.id = ticket.event_key WHERE ticket.user_id = '$user_id' ORDER BY ticket.id DESC LIMIT 1")->result_array();
	}

	public function getUserInfo($user_id = '')
	{
		return $this->db->get_where('user_info', [
			'user_id' => $user_id
		])->result();
	}
}
