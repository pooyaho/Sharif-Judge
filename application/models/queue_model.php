<?php
/**
 * Sharif Judge online judge
 * @file queue_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Queue_model extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}


	/*
	 * Returns TRUE if one submission with $username, $assignment and $problem is already in queue (for preventing multiple submission)
	 */
	public function in_queue ($username, $assignment, $problem){
		$query = $this->db->get_where('queue',array('username'=>$username,'assignment'=>$assignment,'problem'=>$problem));
		if ($query->num_rows()>0)
			return TRUE;
		return FALSE;
	}
}