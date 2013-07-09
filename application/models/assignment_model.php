<?php
/**
 * Sharif Judge online judge
 * @file assignment_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Assignment_model extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function all_assignments(){
		return $this->db->get('assignments')->result_array();
	}

	public function assignment_info($assignment_id){
		return $this->db->get_where('assignments',array('id'=>$assignment_id))->row();
	}
}