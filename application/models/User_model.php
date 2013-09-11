<?php
/**
 * Sharif Judge online judge
 * @file User_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model{

	public function __construct(){
		parent::__construct();
		$this->load->database();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns TRUE if there is a user with username $username in database
	 */
	public function have_user($username){
		$query = $this->db->get_where('users', array('username'=>$username));
		if ($query->num_rows() == 0)
			return FALSE;
		if ($username === $query->row()->username) // needed (because of utf8_general_ci [ci=case insensitive])
			return TRUE;
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Converts user id to username (returns FALSE if user does not exist)
	 */
	public function user_id_to_username($user_id){
		if( ! is_numeric($user_id))
			return FALSE;
		$query = $this->db->select('username')->get_where('users', array('id'=>$user_id));
		if ($query->num_rows() == 0)
			return FALSE;
		return $query->row()->username;
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns TRUE if there is a user (except $username) with email $email in database
	 */
	public function have_email($email, $username = FALSE){
		$query = $this->db->get_where('users', array('email'=>$email));
		if ($query->num_rows() >= 1){
			if($username !== FALSE && $query->row()->username == $username)
				return FALSE;
			else
				return TRUE;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Add a new user to database
	 */
	public function add_user($username, $email, $password, $role){
		if (strlen($username) < 3 OR strlen($username) > 20 OR strlen($password) < 6 OR strlen($password) > 30)
			return 'Username or password length error.';
		if ($this->have_user($username))
			return 'User with this username exists.';
		if ($this->have_email($email))
			return 'User with this email exists.';
		if (strtolower($username) !== $username)
			return 'Username must be lowercase.';
		$roles = array('admin', 'head_instructor', 'instructor', 'student');
		if ( ! in_array($role, $roles))
			return 'Users role is not valid.';
		$this->load->library('password_hash', array(8, FALSE));
		$user=array(
			'username' => $username,
			'email' => $email,
			'password' => $this->password_hash->HashPassword($password),
			'role' => $role
		);
		$this->db->insert('users', $user);
		return TRUE; //success
	}


	// ------------------------------------------------------------------------


	/**
	 * Add multiple users
	 */
	public function add_users($text, $send_mail, $delay){

		$lines = preg_split('/((\r?\n)|(\n?\r))/', $text);
		$users_ok = array();
		$users_error = array();

		// loop over lines of $text :
		foreach ($lines as $line){

			$line = trim($line);

			if (strlen($line) == 0 OR $line[0] == '#')
				continue; //ignore comments and empty lines

			$parts = preg_split('/\s+/', $line);
			if (count($parts) != 4)
				continue; //ignore lines that not contain 4 parts

			if (strtolower(substr($parts[2], 0, 6)) == 'random'){ // generate random password
				$n = trim(substr($parts[2], 6), '[]');
				if (is_numeric($n)){
					$this->load->helper('string');
					$parts[2] = random_string('alnum', $n);
				}
			}

			$result = $this->add_user($parts[0], $parts[1], $parts[2], $parts[3]);

			if ($result === TRUE)
				array_push($users_ok, array($parts[0], $parts[1], $parts[2], $parts[3]));
			else
				array_push($users_error, array($parts[0], $parts[1], $parts[2], $parts[3], $result));

		} // end of loop

		if ($send_mail){ // sending usernames and passwords by email
			$this->load->library('email');
			$count_users = count($users_ok);
			$counter = 0;
			foreach ($users_ok as $user){
				$counter++;
				$config['mailtype'] = 'html';
				$this->email->initialize($config);
				$this->email->from($this->settings_model->get_setting('mail_from'), $this->settings_model->get_setting('mail_from_name'));
				$this->email->to($user[1]);
				$this->email->subject('Sharif Judge Username and Password');
				$text = $this->settings_model->get_setting('add_user_mail');
				$text = str_replace('{SITE_URL}', base_url(), $text);
				$text = str_replace('{ROLE}', $user[3], $text);
				$text = str_replace('{USERNAME}', $user[0], $text);
				$text = str_replace('{PASSWORD}', $user[2], $text);
				$text = str_replace('{LOGIN_URL}', base_url(), $text);
				$this->email->message($text);
				$this->email->send();
				if ($counter < $count_users)
					sleep($delay);
			}
		}

		return array($users_ok, $users_error);

	}


	// ------------------------------------------------------------------------


	/**
	 * Delete a user from database
	 */
	public function delete_user($username, $delete_submissions){
		$this->db->delete('users', array('username'=>$username));
		if ($delete_submissions){// also delete all submissions and submitted codes
			$this->db->delete('final_submissions', array('username' => $username));
			$this->db->delete('all_submissions', array('username' => $username));
			shell_exec("cd {$this->settings_model->get_setting('assignments_root')}; rm -r */*/{$username};");
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Function used for validating user login
	 */
	public function validate_user($username, $password){
		$this->load->library('password_hash', array(8, FALSE));
		$query = $this->db->get_where('users', array('username' => $username));
		if ($query->num_rows() != 1)
			return FALSE;
		if ($query->row()->username !== $username) // needed (because of utf8_general_ci [ci=case insensitive])
			return FALSE;
		if ($this->password_hash->CheckPassword($password, $query->row()->password))
			return TRUE;
		return FALSE;
	}


	// ------------------------------------------------------------------------


	public function update_login_time($username) {
		$now = date('Y-m-d H:i:s', shj_now());

		$first_login = $this->db->select('first_login_time')->get_where('users', array('username'=>$username))->row()->first_login_time;
		if ($first_login === '0000-00-00 00:00:00')
			$this->db->where('username', $username)->update('users', array('first_login_time'=>$now));

		$this->db->where('username', $username)->update('users', array('last_login_time'=>$now));
	}

	// ------------------------------------------------------------------------


	/**
	 * Returns selected assignment by user $username
	 */
	public function selected_assignment($username){
		$query = $this->db->select('selected_assignment')->get_where('users', array('username'=>$username));
		if ($query->num_rows() != 1){//logout
			$this->session->sess_destroy();
			redirect('login');
		}
		return $query->row()->selected_assignment;
	}


	// ------------------------------------------------------------------------


	/**
	 * Sets selected assignment for $username
	 */
	public function select_assignment($username, $assignment_id){
		$this->db->where('username', $username)->update('users', array('selected_assignment'=>$assignment_id));
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns database row of user $username
	 */
	public function get_user($username){
		$query = $this->db->select('display_name, email')->get_where('users', array('username'=>$username));
		if ($query->num_rows() != 1)
			return FALSE;
		return $query->row();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns permission level of given user
	 * admin            -> 3
	 * head_instructor  -> 2
	 * instructor       -> 1
	 * student          -> 0
	 */
	public function get_user_level($username){
		$role = $this->db->select('role')->get_where('users', array('username'=>$username))->row()->role;
		switch ($role){
			case 'admin': return 3;
			case 'head_instructor': return 2;
			case 'instructor': return 1;
			case 'student': return 0;
		}
	}


	// ------------------------------------------------------------------------


	/**
	 * Update user profile
	 */
	public function update_profile($user_id){
		$query = $this->db->get_where('users', array('id'=>$user_id));
		if ($query->num_rows() != 1)
			return FALSE;
		$the_user = $query->row();
		$username = $the_user->username;

		$user=array(
			'display_name' => $this->input->post('display_name'),
			'email' => $this->input->post('email')
		);

		// if a role is provided, change the role
		// (only admins are able to provide a role)
		if ($this->input->post('role') !== NULL)
			$user['role'] = $this->input->post('role');

		// if a password is provided, change the password:
		if ($this->input->post('password') != ''){
			$this->load->library('password_hash', array(8, FALSE));
			$user['password'] = $this->password_hash->HashPassword($this->input->post('password'));
		}

		$this->db->where('username', $username)->update('users', $user);
	}


	// ------------------------------------------------------------------------


	/**
	 * Generate a password reset key and
	 * send an email containing the link for resetting password (in case of password lost)
	 */
	public function send_passchange_mail($email){
		// exit if $email is invalid:
		if ( ! $this->have_email($email) )
			return;

		// generate a random password reset key:
		$this->load->helper('string');
		$passchange_key = random_string('alnum', 50);

		// save the key in users table:
		$now = shj_now();
		$this->db->where('email', $email)->update('users', array('passchange_key'=>$passchange_key, 'passchange_time'=>date('Y-m-d H:i:s', $now)));

		// send the email:
		$this->load->library('email');
		$config['mailtype'] = 'html';
		$this->email->initialize($config);
		$this->email->from($this->settings_model->get_setting('mail_from'), $this->settings_model->get_setting('mail_from_name'));
		$this->email->to($email);
		$this->email->subject('Password Reset');
		$text = $this->settings_model->get_setting('reset_password_mail');
		$text = str_replace('{SITE_URL}', base_url(), $text);
		$text = str_replace('{RESET_LINK}', site_url('login/reset/'.$passchange_key), $text);
		$text = str_replace('{VALID_TIME}', '1 hour', $text); // links are valid for 1 hour
		$this->email->message($text);
		$this->email->send();
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns TRUE if the given password reset key is valid
	 */
	public function have_passchange($passchange_key){
		$query = $this->db->select('passchange_time')->get_where('users', array('passchange_key'=>$passchange_key));
		if ($query->num_rows() != 1)
			return 'Invalid password reset link.';
		$time = strtotime($query->row()->passchange_time);
		$now = shj_now();
		if ($now-$time > 3600 OR $now-$time < 0) // reset link is valid for 1 hour
			return 'The link is expired.';
		return TRUE;
	}


	// ------------------------------------------------------------------------


	/**
	 * Resets password for given password reset key (in case of lost password)
	 */
	public function reset_password($passchange_key, $newpassword){
		$query = $this->db->get_where('users', array('passchange_key'=>$passchange_key));
		if ($query->num_rows() != 1)
			return FALSE; //failure
		$this->load->library('password_hash', array(8, FALSE));
		$this->db->where('username', $query->row()->username)->update('users', array('passchange_key'=>'', 'password' => $this->password_hash->HashPassword($newpassword)));
		return TRUE; //success
	}


	// ------------------------------------------------------------------------


	/**
	 * Get All Users Table (for users page)
	 */
	public function get_all_users(){
		return $this->db->order_by('role', 'asc')->get('users')->result_array();
	}


	// ------------------------------------------------------------------------


	/**
	 * Saves positions of dashboard widgets in database
	 */
	public function save_widget_positions($username, $positions){
		$this->db->where('username', $username)->update('users', array('dashboard_widget_positions'=>$positions));
	}


	// ------------------------------------------------------------------------


	/**
	 * Returns positions of dashboard widgets from database
	 */
	public function get_widget_positions($username){
		return json_decode($this->db->select('dashboard_widget_positions')->get_where('users', array('username'=>$username))->row()->dashboard_widget_positions, TRUE);
	}

}