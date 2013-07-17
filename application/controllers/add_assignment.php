<?php
/**
 * Sharif Judge online judge
 * @file add_assignment.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Add_assignment extends CI_Controller{
	var $username;
	var $assignment;
	public function __construct(){
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
	}

	public function index(){

		$this->load->model('user_model');
		$user=$this->user_model->get_user($this->username);
		$data = array(
			'username'=>$this->username,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Add Assignment',
			'style'=>'main.css',
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/add_assignment',$data);
		$this->load->view('templates/footer');
	}

	public function add(){
		/* TODO set form validation rules*/
		if ($this->form_validation->run()){

		}
		$this->index();
	}
}