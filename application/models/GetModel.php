<?php
defined('BASEPATH') or exit('No direct script access allowed');

class SiswaModel extends CI_Model
{
	public function getLastTicket($user_id = '')
	{
		return $this->db->query("SELECT * FROM ticket LEFT JOIN event_key ON event_key.id = ticket.event_key JOIN user_info ON user_info.user_id = ticket.user_id WHERE ticket.user_id = '$user_id' ORDER BY ticket.id DESC LIMIT 1")->result();
	}

	public function getUserInfo($user_id = '')
	{
		return $this->db->get_where('user_info', [
			'user_id' => $user_id
		])->result();
	}
}
