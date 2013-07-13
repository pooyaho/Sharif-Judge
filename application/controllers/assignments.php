<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file assignments.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Assignments extends CI_Controller{
	var $username;
	var $assignment;
	public function __construct(){
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->form_validation->set_rules('assignment_select','Assignment',"integer|greater_than[0]");
	}

	public function index(){
		$data = array(
			'username'=>$this->username,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'title'=>'Assignments',
			'style'=>'main.css'
		);

		if($this->form_validation->run()){
			$this->assignment = $this->assignment_model->assignment_info( $this->input->post('assignment_select') );
			$this->user_model->select_assignment($this->username, $this->assignment['id']);
		}
		$data['assignment'] = $this->assignment;

		$this->load->view('templates/header',$data);
		$this->load->view('pages/assignments',$data);
		$this->load->view('templates/footer');
	}

	public function select(){ /* used by ajax request (for select assignment from top bar) */
		if($this->form_validation->run()){
			$this->user_model->select_assignment($this->username, $this->input->post('assignment_select'));
			//$selected = $this->assignment_model->assignment_info( $this->input->post('assignment_select') );
			//echo $selected['name'];
			echo "shj_success";
		}
		else
			echo 'shj_failed';
	}
}