<?php
/**
 * Sharif Judge online judge
 * @file all_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class All_submissions extends CI_Controller{
	var $username;
	var $assignment;
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->username = $this->session->userdata('username');
	}
	public function index(){
		$data = array(
			'username'=>$this->username,
			'title'=>'All Submissions',
			'style'=>'main.css'
		);
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		else{ // if has logged in
			$this->load->view('templates/header',$data);
			$this->load->view('pages/all_submissions',$data);
			$this->load->view('templates/footer');
		}
	}
}