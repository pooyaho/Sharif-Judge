<?php
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
		$this->load->helper('url');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
	}

	public function index(){
		$data = array(
			'username'=>$this->username,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'title'=>'Assignments',
			'style'=>'main.css'
		);
		$count_assignments = count($data['all_assignments'])+1;
		$this->form_validation->set_rules('assignment_select','Assignment',"integer|greater_than[0]|less_than[{$count_assignments}]");
		if($this->form_validation->run()){
			$this->assignment = $this->assignment_model->assignment_info( $this->input->post('assignment_select') );
			$this->user_model->select_assignment($this->username, $this->assignment->id);
		}
		$data['assignment'] = $this->assignment;

		$this->load->view('templates/header',$data);
		$this->load->view('pages/assignments',$data);
		$this->load->view('templates/footer');
	}
}