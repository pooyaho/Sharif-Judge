<?php
/**
 * Sharif Judge online judge
 * @file submit_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Submit_model extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	function addUploadOnlyResultToDB($item){
	//function addUploadOnlyResultToDB($submit_id, $username, $assignment_id, $problem_id, $status, $pre_score, $file_name, $file_type){

	}

	function get_final_submissions($assignment_id, $user_level, $username){
		$arr['assignment']=$assignment_id;
		if ($user_level==0)
			$arr['username']=$username;
		return $this->db->get_where('final_submissions',$arr)->result_array();
	}
}