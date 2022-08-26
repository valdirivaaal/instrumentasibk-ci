<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Hapus extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('Main_model');
		if (!$this->session->userdata('logged_in')) {
			redirect('auth/login');
		}
	}

	function _remap($parameter)
	{

		$this->index($parameter);
	}

	public function index()
	{
		$tipe =  $this->uri->segment(2);
		$kelompok =  $this->uri->segment(3);
		$individu =  $this->uri->segment(4);

		if ($tipe == 'user_admin') {
			$this->HapusUser($kelompok);
		} else if ($tipe == 'narasumber') {
			$this->HapusJawabanAdmin($kelompok);
		} else if ($tipe == 'eventkey') {
			$this->HapusEventkey($kelompok);
		} else if ($tipe == 'aum') {
			$this->HapusAum($kelompok, $individu);
		} else if ($tipe == 'ptsdl') {
			$this->HapusPtsdl($kelompok, $individu);
		} else if ($tipe == 'auap') {
			$this->HapusAuap($kelompok, $individu);
		} else if ($tipe == 'dcm') {
			$this->HapusDcm($kelompok, $individu);
		} else {
			return redirect('dashboard');
		}
	}

	protected function HapusUser($id = '')
	{
		$get_user = $this->Main_model->get_where('user_info', [
			'user_id' => $this->session->userdata('id')
		]);

		if ($get_user[0]['level'] != 'admin') {
			$this->output->set_status_header(401);
			redirect(base_url() . 'dashboard');
		}

		if (!$id) {
			return redirect('dashboard');
		} else {
			$this->Main_model->delete_data('user_info', array('user_id' => $id));
			$this->Main_model->delete_data('user', array('id' => $id));
			redirect('admin/user');
		}
	}

	protected function HapusJawabanAdmin($id)
	{
		$get_user = $this->Main_model->get_where('user_info', [
			'user_id' => $this->session->userdata('id')
		]);

		if ($get_user[0]['level'] != 'admin') {
			$this->output->set_status_header(401);
			redirect(base_url() . 'dashboard');
		}

		if (!$id) {
			return redirect('dashboard');
		} else {
			$this->Main_model->delete_data('instrumen_jawaban', array('id' => $id));
			redirect('admin/user');
		}
	}

	protected function HapusJawaban($id)
	{
		// $get_user = $this->Main_model->get_where('user_info', [
		// 	'user_id' => $this->session->userdata('id')
		// ]);

		// if ($get_user[0]['level'] != 'admin') {
		// 	$this->output->set_status_header(401);
		// 	redirect(base_url() . 'dashboard');
		// }

		if (!$id) {
			return redirect('dashboard');
		} else {
			$this->Main_model->delete_data('instrumen_jawaban', array('id' => $id));
			redirect('admin/user');
		}
	}

	protected function HapusEventkey($id)
	{
		$get_user = $this->Main_model->get_where('user_info', [
			'user_id' => $this->session->userdata('id')
		]);

		if ($get_user[0]['level'] != 'admin') {
			$this->output->set_status_header(401);
			redirect(base_url() . 'dashboard');
		}

		if (!$id) {
			return redirect('dashboard');
		} else {
			$this->Main_model->delete_data('event_key', array('id' => $id));
			$this->session->set_flashdata('success', 'hapus');
			redirect('admin/key_available');
		}
	}

	protected function HapusAum($kelompok, $individu)
	{

		if (!$individu) {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'AUM Umum'
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Unauthorized', 403, 'You are not eligible to acces this url');
				} else {
					foreach ($data as $val) {
						$this->Main_model->delete_data('instrumen_jawaban', array('id' => $val['jawaban_id']));
					}
					redirect('aum');
				}
			}
		} else {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'AUM Umum',
				'instrumen_jawaban.id' => $individu
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Not Accesible', 403, 'You are not eligible to acces this url');
				} else {
					$this->Main_model->delete_data('instrumen_jawaban', array('id' => $data[0]['jawaban_id']));
					redirect('aum/view/' . $kelompok);
				}
			}
		}
	}

	protected function HapusPtsdl($kelompok, $individu)
	{

		if (!$individu) {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'AUM PTSDL'
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Unauthorized', 403, 'You are not eligible to acces this url');
				} else {
					foreach ($data as $val) {
						$this->Main_model->delete_data('instrumen_jawaban', array('id' => $val['jawaban_id']));
					}
					redirect('aum');
				}
			}
		} else {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'AUM PTSDL',
				'instrumen_jawaban.id' => $individu
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Not Accesible', 403, 'You are not eligible to acces this url');
				} else {
					$this->Main_model->delete_data('instrumen_jawaban', array('id' => $data[0]['jawaban_id']));
					redirect('aum/view/' . $kelompok);
				}
			}
		}
	}

	protected function HapusAuap($kelompok, $individu)
	{

		if (!$individu) {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'AUAP'
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Unauthorized', 403, 'You are not eligible to acces this url');
				} else {
					foreach ($data as $val) {
						$this->Main_model->delete_data('instrumen_jawaban', array('id' => $val['jawaban_id']));
					}
					redirect('aum');
				}
			}
		} else {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'AUAP',
				'instrumen_jawaban.id' => $individu
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Not Accesible', 403, 'You are not eligible to acces this url');
				} else {
					$this->Main_model->delete_data('instrumen_jawaban', array('id' => $data[0]['jawaban_id']));
					redirect('aum/view/' . $kelompok);
				}
			}
		}
	}
	protected function HapusDcm($kelompok, $individu)
	{

		if (!$individu) {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'DCM'
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Unauthorized', 403, 'You are not eligible to acces this url');
				} else {
					foreach ($data as $val) {
						$this->Main_model->delete_data('instrumen_jawaban', array('id' => $val['jawaban_id']));
					}
					redirect('aum');
				}
			}
		} else {
			$data = $this->Main_model->join('instrumen_jawaban', '*,instrumen_jawaban.id as jawaban_id', [
				[
					'table' => 'user_instrumen',
					'parameter' => 'user_instrumen.id = instrumen_jawaban.instrumen_id'
				], [
					'table' => 'instrumen',
					'parameter' => 'instrumen.id = user_instrumen.instrumen_id'
				]
			], [
				'instrumen_jawaban.kelas' => $kelompok,
				'instrumen.nickname' => 'DCM',
				'instrumen_jawaban.id' => $individu
			]);
			if (empty($data)) {
				show_404();
			} else {
				if ($data[0]['user_id'] != $this->session->userdata('id')) {
					show_error('Not Accesible', 403, 'You are not eligible to acces this url');
				} else {
					$this->Main_model->delete_data('instrumen_jawaban', array('id' => $data[0]['jawaban_id']));
					redirect('aum/view/' . $kelompok);
				}
			}
		}
	}
}
