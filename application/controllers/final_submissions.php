<?php
/**
 * Sharif Judge online judge
 * @file final_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Final_submissions extends CI_Controller{

	public function index(){
		$this->load->helper('url');
		$username = $this->session->userdata('username');
		$data = array(
			'username'=>$username,
			'title'=>'Final Submissions',
			'style'=>'main.css'
		);
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		else{ // if has logged in
			$this->load->view('templates/header',$data);
			$this->load->view('pages/final_submissions',$data);
			$this->load->view('templates/footer');
		}
	}
}