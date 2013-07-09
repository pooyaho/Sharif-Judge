<?php
/**
 * Sharif Judge online judge
 * @file login.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Login extends CI_Controller{

	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->model('user_model');
	}

	public function username_check($username){ // checks whether a user with this username exists (used for validating registration)
		if ($this->user_model->have_user($username))
			return FALSE;
		return TRUE;
	}
	public function email_check($email){ // checks whether a user with this email exists (used for validating registration)
		if ($this->user_model->have_email($email))
			return FALSE;
		return TRUE;
	}

	public function index(){ // login
		$this->form_validation->set_rules('username','Username','required|min_length[3]|max_length[20]|alpha_numeric');
		$this->form_validation->set_rules('password','Password','required|min_length[6]|max_length[30]|alpha_numeric');
		$data = array(
			'title' => "Login",
			'style' => "login.css",
			'error' => FALSE
		);
		$this->load->view('templates/header', $data);
		if($this->form_validation->run()){
			$username=$this->security->xss_clean($this->input->post('username'));
			$password=$this->input->post('password');
			if($this->user_model->validate_user($username,$password)){
				// setting the session and redirecting to dashboard:
				$login_data = array(
					'username'  => $username,
					//'email'     => 'johndoe@some-site.com',
					'logged_in' => TRUE
				);
				$this->session->set_userdata($login_data);
				redirect('/');
			}
			else
				// for displaying error message in 'pages/authentication/login' view
				$data['error'] = TRUE;
		}
		$this->load->view('pages/authentication/login', $data);
		$this->load->view('templates/footer');
	}

	public function register(){
		$this->form_validation->set_message('username_check','User with same %s exists.');
		$this->form_validation->set_message('email_check','User with same %s exists.');
		$this->form_validation->set_rules('username','username','required|min_length[3]|max_length[20]|alpha_numeric|callback_username_check');
		$this->form_validation->set_rules('email','email','required|max_length[40]|valid_email|callback_email_check');
		$this->form_validation->set_rules('password','password','required|min_length[6]|max_length[30]|alpha_numeric');
		$this->form_validation->set_rules('password_again','password confirmation','required|matches[password]');
		$data = array(
			'title' => "Register",
			'style' => "login.css",
		);
		$this->load->view('templates/header', $data);
		if ($this->form_validation->run()){
			$this->user_model->add_user();
			$this->load->view('pages/authentication/register_success');
		}
		else{
			$this->load->view('pages/authentication/register', $data);
		}
		$this->load->view('templates/footer');
	}

	public function logout(){ // logging out and redirecting to login page
		$this->session->sess_destroy();
		redirect('login');
	}

	public function lost(){
		$this->form_validation->set_rules('email','email','required|max_length[40]|valid_email');
		$data = array(
			'title' => "Lost Password",
			'style' => "login.css",
			'sent' => FALSE
		);
		$this->load->view('templates/header', $data);
		if ($this->form_validation->run()){
			$this->user_model->send_passchange_mail($this->input->post('email'));
			$data['sent']=TRUE;
		}
		$this->load->view('pages/authentication/lost', $data);
		$this->load->view('templates/footer');
	}
}