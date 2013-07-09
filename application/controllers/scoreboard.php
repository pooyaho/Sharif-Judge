<?php
/**
 * Sharif Judge online judge
 * @file scoreboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Scoreboard extends CI_Controller{
	var $username;
	var $assignment;
	public function __construct(){
		parent::__construct();
		$this->username = $this->session->userdata('username');
	}

	public function index(){
		$this->load->helper('url');
		$data = array(
			'username'=>$this->username,
			'title'=>'Scoreboard',
			'style'=>'main.css'
		);
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		else{ // if has logged in
			$this->load->view('templates/header',$data);
			$this->load->view('pages/scoreboard',$data);
			$this->load->view('templates/footer');
		}
	}
}