<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file user.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class User_model extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}



	/*
	 * Returns TRUE if there is a user with username $username in database
	 */
	public function have_user($username){
		$query = $this->db->get_where('users',array('username'=>$username));
		return ($query->num_rows()>=1);
	}



	/*
	 * Returns TRUE if there is a user (except $username) with email $email in database
	 */
	public function have_email($email,$username=FALSE){
		$query = $this->db->get_where('users',array('email'=>$email));
		if ($query->num_rows()>=1){
			if($username !== FALSE && $query->row()->username==$username)
				return FALSE;
			else
				return TRUE;
		}
		return FALSE;
	}



	/*
	 * Add new user to database
	 */
	public function add_user(){
		$this->load->helper('password_hash');
		$t_hasher = new PasswordHash(8, FALSE);
		$user=array(
			'username' => $this->input->post('username'),
			'email' => $this->input->post('email'),
			'password' => $t_hasher->HashPassword($this->input->post('password'))
		);
		$this->db->insert('users',$user);
	}



	/*
	 * Function used for validating user login
	 */
	public function validate_user($username, $password){
		$this->load->helper('password_hash');
		$t_hasher = new PasswordHash(8, FALSE);
		$query = $this->db->get_where('users',array('username'=>$username));
		if ($query->num_rows() != 1)
			return FALSE;
		if ($t_hasher->CheckPassword($password,$query->row()->password))
			return TRUE;
		return FALSE;
	}


	/*
	 * Returns selected assignment by user $username
	 */
	public function selected_assignment($username){
		return $this->db->select('selected_assignment')->get_where('users',array('username'=>$username))->row()->selected_assignment;
	}


	/*
	 * Sets selected assignment for $username
	 */
	public function select_assignment($username, $assignment_id){
		$this->db->where('username',$username)->update('users',array('selected_assignment'=>$assignment_id));
	}


	/*
	 * Gets information about user $username
	 */
	public function get_user($username){
		return $this->db->select('display_name, email')->get_where('users',array('username'=>$username))->row();
	}


	/*
	 * Gets permission level of given user
	 * admin            -> 3
	 * head_instructor  -> 2
	 * instructor       -> 1
	 * student          -> 0
	 */
	public function get_user_level($username){
		$role = $this->db->select('role')->get_where('users',array('username'=>$username))->row()->role;
		switch ($role){
			case 'admin': return 3;
			case 'head_instructor': return 2;
			case 'instructor': return 1;
			case 'student': return 0;
		}
	}


	/*
	 * Update user profile
	 */
	public function update_profile(){
		$user=array(
			'display_name' => $this->input->post('display_name'),
			'email' => $this->input->post('email')
		);
		if ($this->input->post('password')!=""){
			$this->load->helper('password_hash');
			$t_hasher = new PasswordHash(8, FALSE);
			$user['password'] = $t_hasher->HashPassword($this->input->post('password'));
		}
		$this->db->where('username',$this->session->userdata('username'))->update('users',$user);
	}



	/*
	 * Generate a password reset key and
	 * send an email containing the link for resetting password (in case of password lost)
	 */
	public function send_passchange_mail($email){
		if ( !$this->have_email($email) )
			return;

		$passchange_key = random_string('alnum',50);

		$now = shj_now();
		$this->db->where('email',$email)->update('users',array('passchange_key'=>$passchange_key,'passchange_time'=>date('Y-m-d H:i:s',$now)));

		$this->load->library('email');
		$config['mailtype']='html';
		$this->email->initialize($config);
		$this->email->from('info@mjnaderi.ir', 'Sharif Judge');
		$this->email->to($email);
		$this->email->subject('Password Reset');
		$this->email->message('<p>Someone requested to reset the password for account with this email address at '.site_url().'.</p>
		<p>To change your password, visit this link:</p>
		<p><a href="'.site_url('login/reset/'.$passchange_key).'">Reset Password</a></p>
		<p>If you don\'t want to change your password, just ignore this email.</p>');

		$this->email->send();
	}

	/*
	 * Returns TRUE if the given passchange key is valid
	 */
	public function have_passchange($passchange_key){
		$query = $this->db->select('passchange_time')->get_where('users',array('passchange_key'=>$passchange_key));
		if ($query->num_rows() != 1)
			return 'Invalid password reset link.';
		$time = strtotime($query->row()->passchange_time);
		$now = shj_now();
		if ($now-$time>3600 OR $now-$time<0) // reset link is valid for 1 hour
			return 'This password reset link is expired.';
		return TRUE;
	}

	/*
	 * Resets password for given passchange key (in case of lost password)
	 */
	public function reset_password($passchange_key, $newpassword){
		$query = $this->db->get_where('users',array('passchange_key'=>$passchange_key));
		if ($query->num_rows() != 1)
			return FALSE;
		$this->load->helper('password_hash');
		$t_hasher = new PasswordHash(8, FALSE);
		$this->db->where('username',$query->row()->username)->update('users',array('passchange_key'=>'','password' => $t_hasher->HashPassword($newpassword)));
		return TRUE;
	}
}