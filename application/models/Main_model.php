<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Main_model extends CI_Model
{

	var $table = "";

	public function get($table = "default", $column = "", $params = "", $sort = "", $order = "", $limit = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->select($column);
		$this->db->order_by($sort, $order);
		$this->db->limit($limit);
		if ($params) {
			$this->db->where($params);
		}
		if ($column) {
			$this->db->distinct();
		}
		$query = $this->db->get($table);
		return $query->result_array();
	}

	public function get_where($table = "default", $params = "", $sort = "", $order = "", $limit = "", $group_by = "", $select = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->order_by($sort, $order);
		$this->db->limit($limit);
		$this->db->group_by($group_by);
		$this->db->where($params);
		if ($select) {
			$this->db->select($select);
			// $this->db->distinct();
		}
		$query = $this->db->get($table);
		return $query->result_array();
	}

	public function get_where_in($table = "default", $column = "", $params = "", $where = "", $sort = "", $order = "", $limit = "", $group_by = "", $select = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->order_by($sort, $order);
		$this->db->limit($limit);
		$this->db->group_by($group_by);
		if ($where) {
			$this->db->where($where);
		}
		$this->db->where_in($column, $params);
		if ($select) {
			$this->db->select($select);
			// $this->db->distinct();
		}
		$query = $this->db->get($table);
		return $query->result_array();
	}

	public function get_where_not($table = "default", $params = "", $field = "", $sort = "", $order = "", $limit = "", $group_by = "", $select = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->order_by($sort, $order);
		$this->db->limit($limit);
		$this->db->group_by($group_by);
		$this->db->where_not_in($field, $params);
		if ($select) {
			$this->db->select($select);
			// $this->db->distinct();
		}
		$query = $this->db->get($table);
		return $query->result_array();
	}


	public function join($table = "default", $column = "", $table2 = "", $params = "", $sort = "", $order = "", $limit = "", $group_by = "", $offset = "", $like = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->select($column);
		$this->db->from($table);
		foreach ($table2 as $row) {
			$this->db->join($row['table'], $row['parameter'], 'left');
		}

		if (!empty($params)) {
			$this->db->where($params);
		}
		$this->db->distinct();
		$this->db->group_by($group_by);
		$this->db->order_by($sort, $order);
		$this->db->limit($limit, $offset);

		if ($like) {
			foreach ($like as $resultLike) {
				$this->db->like($resultLike['column'], $resultLike['keyword'], $resultLike['method']);
			}
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function innerJoin($table = "default", $column = "", $table2 = "", $params = "", $sort = "", $order = "", $limit = "", $group_by = "", $offset = "", $like = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->select($column);
		$this->db->from($table);
		foreach ($table2 as $row) {
			$this->db->join($row['table'], $row['parameter'], 'inner');
		}

		if (!empty($params)) {
			$this->db->where($params);
		}
		$this->db->distinct();
		$this->db->order_by($sort, $order);
		$this->db->limit($limit, $offset);
		$this->db->group_by($group_by);

		if ($like) {
			foreach ($like as $resultLike) {
				$this->db->like($resultLike['column'], $resultLike['keyword'], $resultLike['method']);
			}
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function innerJoin2($table = "default", $column = "", $table2 = "", $params = "", $whereNotIn = [], $sort = "", $order = "", $limit = "", $group_by = "", $offset = "", $like = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->select($column);
		$this->db->from($table);
		foreach ($table2 as $row) {
			$this->db->join($row['table'], $row['parameter'], 'inner');
		}

		if (!empty($params)) {
			$this->db->where($params);
		}

		if ($whereNotIn) {
			$this->db->where_not_in($whereNotIn['field'], $whereNotIn['rows']);
		}

		$this->db->distinct();
		$this->db->order_by($sort, $order);
		$this->db->limit($limit, $offset);
		$this->db->group_by($group_by);

		if ($like) {
			foreach ($like as $resultLike) {
				$this->db->like($resultLike['column'], $resultLike['keyword'], $resultLike['method']);
			}
		}
		$query = $this->db->get();
		return $query->result_array();
	}

	public function insert_data($table = "default", $data = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->trans_start();
		$this->db->insert($table, $data);
		$insert_id = $this->db->insert_id();
		$this->db->trans_complete();
		return  $insert_id;
	}

	public function insert_data_batch($table = 'default', $data = '')
	{
		$table = $table == 'default' ? $this->table : $table;
		$this->db->trans_start();
		$this->db->insert_batch($table, $data);
		return $this->db->trans_complete();
	}

	public function update_data($table = "default", $data = "", $params = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->update($table, $data, $params);
	}

	public function delete_data($table = "default", $params = "")
	{
		$table = $table == "default" ? $this->table : $table;
		$this->db->delete($table, $params);
	}

	function getOrder($table = "", $field = "", $params = "")
	{

		empty($params) ? $where = "" : $where = $this->db->where($params);

		$table = $table == "default" ? $this->table : $table;
		$this->db->select("MAX($field) as kd_max");
		$where;
		$query = $this->db->get($table);

		$kd = "";
		if ($query->num_rows() > 0) {
			foreach ($query->result() as $k) {
				$tmp = ((int)$k->kd_max) + 1;
				$kd = sprintf($tmp);
			}
		} else {
			$kd = "1";
		}
		return $kd;
	}

	public function getTahunAjaran()
	{
		$yearNow = date('Y');
		$yearBefore = $yearNow - 1;
		$yearAfter = $yearNow + 1;
		$data = $this->get_where('tahun_ajaran', ['id' => 1]);

		if (date('m') >= 7) {
			$tahun_ajaran = $yearNow . '/' . $yearAfter;
		} else {
			$tahun_ajaran = $yearBefore . '/' . $yearNow;
		}

		if ($data[0]['status'] == 'Aktif') {
			return $data[0]['tahun_ajaran'];
		} else {
			return $tahun_ajaran;
		}
	}
}

/* End of file Pasien.php */
/* Location: ./application/models/Pasien.php */
