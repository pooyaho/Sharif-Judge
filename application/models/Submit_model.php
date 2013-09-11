<?php
/**
 * Sharif Judge online judge
 * @file Submit_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Submit_model extends CI_Model {

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns table row for a specific submission
	 */
	public function get_submission($username, $assignment, $problem, $submit_id){
		$query = $this->db->get_where('all_submissions',
			array(
				'username'=>$username,
				'assignment'=>$assignment,
				'problem'=>$problem,
				'submit_id'=>$submit_id
			)
		);
		if($query->num_rows()!=1)
			return FALSE;
		return $query->row_array();
	}


	// ------------------------------------------------------------------------


	public function get_final_submissions($assignment_id, $user_level, $username, $page_number = FALSE){
		$arr['assignment']=$assignment_id;
		if ($user_level==0)// students can only get final submissions of themselves
			$arr['username']=$username;
		if ($page_number===FALSE)
			return $this->db->order_by('username asc, problem asc')->get_where('final_submissions',$arr)->result_array();
		else{
			$per_page = $this->settings_model->get_setting('results_per_page');
			return $this->db->order_by('username asc, problem asc')->limit($per_page,($page_number-1)*$per_page)->get_where('final_submissions',$arr)->result_array();
		}

	}


	// ------------------------------------------------------------------------


	public function get_all_submissions($assignment_id, $user_level, $username, $page_number = FALSE){
		$arr['assignment']=$assignment_id;
		if ($user_level==0)
			$arr['username']=$username;
		if ($page_number===FALSE)
			return $this->db->order_by('submit_id','desc')->get_where('all_submissions',$arr)->result_array();
		else {
			$per_page = $this->settings_model->get_setting('results_per_page');
			return $this->db->order_by('submit_id','desc')->limit($per_page,($page_number-1)*$per_page)->get_where('all_submissions',$arr)->result_array();
		}
	}


	// ------------------------------------------------------------------------


	public function count_final_submissions($assignment_id, $user_level, $username){
		$arr['assignment']=$assignment_id;
		if ($user_level==0)
			$arr['username']=$username;
		return $this->db->where($arr)->count_all_results('final_submissions');
	}


	// ------------------------------------------------------------------------


	public function count_all_submissions($assignment_id, $user_level, $username){
		$arr['assignment']=$assignment_id;
		if ($user_level==0)
			$arr['username']=$username;
		return $this->db->where($arr)->count_all_results('all_submissions');
	}


	// ------------------------------------------------------------------------


	public function set_final_submission($username, $assignment, $problem, $submit_id){
		$query = $this->db->get_where('final_submissions',
			array('username'=>$username,
				'assignment'=>$assignment,
				'problem'=>$problem
			));
		if ($query->num_rows()==0)
			return FALSE;
		$submission = $this->db->get_where('all_submissions',
			array('username'=>$username,
				'assignment'=>$assignment,
				'problem'=>$problem,
				'submit_id'=>$submit_id
			));
		if ($submission->num_rows()==0)
			return FALSE;
		$submission = $submission->row_array();
		unset($submission['submit_number']);
		$this->db->where(
			array('username'=>$username,
			'assignment'=>$assignment,
			'problem'=>$problem
		))->update('final_submissions',$submission);
		return TRUE;
	}


	// ------------------------------------------------------------------------


	/**
	 * add the result of an "upload only" submit to the database
	 */
	public function add_upload_only($submit_info){
		$now = shj_now();

		$submit_query = $this->db->get_where('final_submissions',
			array(
				'username'=>$submit_info['username'],
				'assignment'=>$submit_info['assignment'],
				'problem'=>$submit_info['problem']
			)
		);

		$submit_info['time'] = date('Y-m-d H:i:s', $now);
		$submit_info['status'] = 'Uploaded';
		$submit_info['pre_score'] = 0;

		if ($submit_query->num_rows() == 0){
			$submit_info['submit_count'] = 1;
			$this->db->insert('final_submissions', $submit_info);
		}
		else {
			$submit_info['submit_count'] = $submit_query->row()->submit_count + 1;
			$this->db->where(
				array(
					'username' => $submit_info['username'],
					'assignment' => $submit_info['assignment'],
					'problem' => $submit_info['problem']
				)
			)->update('final_submissions',$submit_info);
		}

		$submit_info['submit_number'] = $submit_info['submit_count'];
		unset ($submit_info['submit_count']);
		$this->db->insert('all_submissions', $submit_info);

	}


}