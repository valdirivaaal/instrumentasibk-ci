<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SiswaModel extends CI_Model
{
	public function view()
	{
		return $this->db->get('kelas_siswa')->result();
	}

	public function upload_file($filename)
	{
		$this->load->library('upload');

		$config = [
			'upload_path' => './excel',
			'allowed_types' => 'xlsx',
			'max_size' => '2048',
			'overwrite' => true,
			'file_name' => $filename
		];

		$this->upload->initialize($config);

		if ($this->upload->do_upload('file')) {
			$return = [
				'result' => 'success',
				'file' => $this->upload->data(),
				'error' => ''
			];

			return $return;
		} else {
			$return = [
				'result' => 'failed',
				'file' => '',
				'error' => $this->upload->display_errors()
			];

			return $return;
		}
	}

	public function insert_multiple($data)
	{
		$this->db->insert_batch('kelas_siswa', $data);
	}
}
