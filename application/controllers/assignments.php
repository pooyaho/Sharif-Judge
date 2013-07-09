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
		$this->load->helper('form');
		$this->load->helper('url');
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->load->model('assignment_model');
		$this->username = $this->session->userdata('username');
	}

	public function index(){
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$data = array(
			'username'=>$this->username,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'title'=>'Assignments',
			'style'=>'main.css'
		);
		$count_assignments = count($data['all_assignments'])+1;
		$this->form_validation->set_rules('assignment_select','Assignment',"integer|greater_than[0]|less_than[{$count_assignments}]");
		if($this->form_validation->run()){
			$data['selected_assignment'] = $this->assignment_model->assignment_info( $this->input->post('assignment_select') );
			$this->user_model->select_assignment($this->username, $data['selected_assignment']->id);
		}
		else{
			$data['selected_assignment'] = $this->assignment_model->assignment_info( $this->user_model->selected_assignment($this->username) );
		}
		$this->load->view('templates/header',$data);
		$this->load->view('pages/assignments',$data);
		$this->load->view('templates/footer');
	}
}