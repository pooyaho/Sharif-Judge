<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file final_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Submissions extends CI_Controller{
	var $username;
	var $assignment;
	var $user_level;
	public function __construct(){
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->load->model('submit_model');
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
	}




	public function the_final(){

		$data = array(
			'view'=>'final',
			'username'=>$this->username,
			'user_level'=>$this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Final Submissions',
			'style'=>'main.css',
			'items'=>$this->submit_model->get_final_submissions($this->assignment['id'],$this->user_level,$this->username)
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/submissions',$data);
		$this->load->view('templates/footer');
	}



	public function all(){
		$final = $this->submit_model->get_final_submissions($this->assignment['id'],$this->user_level,$this->username);
		$final_items=array();
		foreach ($final as $item){
			$final_items[$item['username']][$item['problem']]=$item;
		}
		$data = array(
			'view'=>'all',
			'username'=>$this->username,
			'user_level'=>$this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'All Submissions',
			'style'=>'main.css',
			'items'=>$this->submit_model->get_all_submissions($this->assignment['id'],$this->user_level,$this->username),
			'final_items' => $final_items
		);
		$this->load->view('templates/header',$data);
		$this->load->view('pages/submissions',$data);
		$this->load->view('templates/footer');
	}

	public function select(){ /* used by ajax request (for selecting final submission) */
		$this->form_validation->set_rules('submit_id','Submit ID',"integer|greater_than[0]");
		$this->form_validation->set_rules('problem','problem',"integer|greater_than[0]");
		//echo $this->input->post('problem'); echo '<br>'; echo $this->input->post('submit_id');
		if($this->form_validation->run() && $this->submit_model->set_final_submission($this->username, $this->assignment['id'], $this->input->post('problem'), $this->input->post('submit_id'))){
			echo "shj_success";
		}
		else
			echo 'shj_failed';
	}

}