<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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


	/*
	 * Returns a list of all assignments and their information
	 */
	public function all_assignments(){
		return $this->db->get('assignments')->result_array();
	}


	public function all_problems($assignment_id){
		return $this->db->get_where('problems',array('assignment'=>$assignment_id))->result_array();
	}

	public function problem_info($assignment_id, $problem_id){
		return $this->db->get_where('problems',array('assignment'=>$assignment_id,'id'=>$problem_id))->row_array();
	}


	/*
	 * Returns info about given assignment
	 */
	public function assignment_info($assignment_id){
		$query = $this->db->get_where('assignments',array('id'=>$assignment_id));
		if ($query->num_rows()!=1)
			return array(
				'id'=>0,
				'name'=>'Not Selected'
			);
		return $query->row_array();
	}


	/*
	 * Returns TRUE if $username if one of the $participants
	 * Examples for participants: "ALL" or "user1, user2,user3"
	 */
	public function is_participant($participants, $username){
		$participants = explode(",",$participants);
		foreach ($participants as &$participant){
			$participant = trim($participant);
		}
		if(in_array("ALL",$participants))
			return TRUE;
		if(in_array($username,$participants))
			return TRUE;
		return FALSE;
	}

	public function add_total_submits($assignment_id){
		$total = $this->db->select('total_submits')->get_where('assignments',array('id'=>$assignment_id))->row()->total_submits;
		$this->db->where('id',$assignment_id)->update('assignments',array('total_submits'=>($total+1)));
		return ($total+1);
	}

}