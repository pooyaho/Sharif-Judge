<?php
/**
 * Sharif Judge online judge
 * @file profile.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Profile extends CI_Controller{

	public function index(){
		$this->load->helper('url');
		$this->load->helper('form');
		$username = $this->session->userdata('username');
		$data = array(
			'username'=>$username,
			'title'=>'Profile',
			'style'=>'main.css'
		);
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		else{ // if has logged in
			$this->load->view('templates/header',$data);
			$this->load->view('pages/profile',$data);
			$this->load->view('templates/footer');
		}
	}
}