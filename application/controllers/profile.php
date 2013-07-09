<?php
/**
 * Sharif Judge online judge
 * @file profile.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Profile extends CI_Controller{
	var $username;
	var $assignment;
	public function __construct(){
		parent::__construct();
		$this->username = $this->session->userdata('username');
	}

	public function index(){
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->model('user_model');
		$user=$this->user_model->get_user($this->username);
		$data = array(
			'username'=>$this->username,
			'title'=>'Profile',
			'style'=>'main.css',
			'email' => $user->email,
			'display_name' => $user->display_name
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

	public function check_password($str){
		if (strlen($str)==0 OR (strlen($str)>=6 && strlen($str)<=30))
			return TRUE;
		return FALSE;
	}

	public function update(){
		$this->load->library('form_validation');
		$this->load->model('user_model');
		$this->form_validation->set_rules('display_name','Display Name','max_length[40]|xss_clean|strip_tags');
		$this->form_validation->set_rules('email','Email Address','required|max_length[40]|valid_email');
		$this->form_validation->set_message('check_password','Password must be between 6 and 30 characters in length.');
		$this->form_validation->set_rules('password','Password','callback_check_password|alpha_numeric');
		$this->form_validation->set_rules('password_again','Password Confirmation','matches[password]');
		if ($this->form_validation->run()){
			$this->user_model->update_profile();
		}
		$this->index();
	}
}