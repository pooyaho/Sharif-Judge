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


	// ------------------------------------------------------------------------


	/**
	 * Returns TRUE if one submission with $username, $assignment and $problem is already in queue (for preventing multiple submission)
	 */
	public function in_queue ($username, $assignment, $problem){
		$query = $this->db->get_where('queue',array('username'=>$username,'assignment'=>$assignment,'problem'=>$problem));
		if ($query->num_rows()>0)
			return TRUE;
		return FALSE;
	}


	// ------------------------------------------------------------------------


	public function add_to_queue($submit_info){
		$now = shj_now();

		$submit_query = $this->db->get_where('final_submissions',array(
			'username'=>$submit_info['username'],
			'assignment'=>$submit_info['assignment'],
			'problem'=>$submit_info['problem']
		));

		$submit_info['time']=date('Y-m-d H:i:s',$now);
		$submit_info['status']='PENDING';
		$submit_info['pre_score']=0;

		if ($submit_query->num_rows()==0)
			$submit_info['submit_number'] = 1;
		else
			$submit_info['submit_number'] = $submit_query->row()->submit_count + 1;

		$this->db->insert('all_submissions',$submit_info);

		$this->db->insert('queue',array(
			'submit_id'=>$submit_info['submit_id'],
			'username'=>$submit_info['username'],
			'assignment'=>$submit_info['assignment'],
			'problem'=>$submit_info['problem']
		));
	}


}