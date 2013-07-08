<?php
/**
 * Sharif Judge online judge
 * @file scoreboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Scoreboard extends CI_Controller{

	public function index(){
		$this->load->helper('url');
		$username = $this->session->userdata('username');
		$data = array(
			'username'=>$username,
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