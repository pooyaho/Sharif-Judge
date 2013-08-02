<?php
/**
 * Sharif Judge online judge
 * @file users.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Users extends CI_Controller {
	var $username;
	var $assignment;
	var $user_level;
	public function __construct(){
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		if ( $this->user_level <= 2)
			show_error("You have not enough permission to access this page.");
	}

	public function index(){

		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Users',
			'style'=>'main.css',
			'users'=>$this->user_model->get_all_users()
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/users',$data);
		$this->load->view('templates/footer');
	}

	public function add(){
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Add User',
			'style'=>'main.css',
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/add_user',$data);
		$this->load->view('templates/footer');
	}
}