<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file scoreboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Scoreboard extends CI_Controller{
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
	}

	public function index(){
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Scoreboard',
			'style'=>'main.css'
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/scoreboard',$data);
		$this->load->view('templates/footer');
	}
}