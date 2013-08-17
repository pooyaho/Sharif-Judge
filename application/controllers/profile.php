<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file profile.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Profile extends CI_Controller{
	var $username;
	var $assignment;
	var $user_level;
	var $form_status;
	var $edit_username;
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		$this->form_status = "";
	}

	public function index($user_id = FALSE){
		if ($user_id===FALSE){
			$user_id = $this->db->get_where('users',array('username'=>$this->username))->row()->id;
		}
		if (!is_numeric($user_id)){
			show_error("Incorrect user id");
		}
		$query = $this->db->get_where('users',array('id'=>$user_id));
		if ($query->num_rows()!=1)
			show_error("Permission Denied");
		$user = $query->row();
		$this->edit_username = $user->username;
		if ($this->user_level<=2)
			if ($this->username != $this->edit_username)
				show_error("Permission Denied");
		$this->form_validation->set_message('_email_check','User with same %s exists.');
		$this->form_validation->set_message('_password_check','Password must be between 6 and 30 characters in length.');
		$this->form_validation->set_message('_password_again_check','The Password Confirmation field does not match the Password field.');
		$this->form_validation->set_rules('display_name','Display Name','max_length[40]|xss_clean|strip_tags');
		$this->form_validation->set_rules('email','Email Address','required|max_length[40]|valid_email|callback__email_check');
		$this->form_validation->set_rules('password','Password','callback__password_check|alpha_numeric');
		$this->form_validation->set_rules('password_again','Password Confirmation','callback__password_again_check');
		$this->form_validation->set_rules('role','Role','callback__role_check');
		if ($this->form_validation->run()){
			$this->user_model->update_profile($user_id);
			$user = $this->db->get_where('users',array('id'=>$user_id))->row();
			$this->form_status = "ok";
		}
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Profile',
			'style'=>'main.css',
			'id'=>$user_id,
			'edit_username'=>$this->edit_username,
			'email' => $user->email,
			'display_name' => $user->display_name,
			'role' => $user->role,
			'form_status' => $this->form_status,
		);
		$this->load->view('templates/header',$data);
		$this->load->view('pages/profile',$data);
		$this->load->view('templates/footer');
	}

	public function _password_check($str){
		if (strlen($str)==0 OR (strlen($str)>=6 && strlen($str)<=30))
			return TRUE;
		return FALSE;
	}

	public function _password_again_check($str){
		if ($this->input->post('password')!==$this->input->post('password_again'))
			return FALSE;
		return TRUE;
	}

	public function _email_check($email){ // checks whether a user with this email exists (used for validating registration)
		if ($this->user_model->have_email($email,$this->edit_username))
			return FALSE;
		return TRUE;
	}

	public function _role_check($role){ // checks whether a user with this email exists (used for validating registration)
		if ($this->user_level<=2)
			if($role=="")
				return TRUE;
			else
				return FALSE;
		$roles = array('admin','head_instructor','instructor','student');
		if (in_array($role,$roles))
			return TRUE;
		return FALSE;
	}
}