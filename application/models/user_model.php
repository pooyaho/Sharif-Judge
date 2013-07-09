<?php
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

	public function have_user($username){
		$query = $this->db->get_where('users',array('username'=>$username));
		return ($query->num_rows()>=1);
	}

	public function have_email($email){
		$query = $this->db->get_where('users',array('email'=>$email));
		return ($query->num_rows()>=1);
	}

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

	public function selected_assignment($username){
		return $this->db->select('selected_assignment')->get_where('users',array('username'=>$username))->row()->selected_assignment;
	}

	public function select_assignment($username, $assignment_id){
		$this->db->where('username',$username)->update('users',array('selected_assignment'=>$assignment_id));
	}

	public function get_user($username){
		return $this->db->select('display_name, email')->get_where('users',array('username'=>$username))->row();
	}

	public function update_profile(){
		$this->load->helper('password_hash');
		$t_hasher = new PasswordHash(8, FALSE);
		$user=array(
			'display_name' => $this->input->post('display_name'),
			'email' => $this->input->post('email'),
			'password' => $t_hasher->HashPassword($this->input->post('password'))
		);
		$this->db->where('username',$this->session->userdata('username'))->update('users',$user);
	}

	public function send_passchange_mail($email){
		$this->load->library('email');

		$this->email->from('info@mjnaderi.ir', 'Mohammad Javad');
		$this->email->to('mjnaderi@gmail.com');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		$this->email->send();
	}
}