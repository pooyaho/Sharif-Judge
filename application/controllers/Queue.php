<?php
/**
 * Sharif Judge online judge
 * @file Queue.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Queue extends CI_Controller {

	var $username;
	var $assignment;
	var $user_level;

	// ------------------------------------------------------------------------


	public function __construct(){
		parent::__construct();
		$this->load->driver('session');
		if ( ! $this->session->userdata('logged_in')) // if not logged in
		redirect('login');
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		if ( $this->user_level <= 1)
			show_error('You have not enough permission to access this page.');
		$this->load->model('queue_model');
	}


	// ------------------------------------------------------------------------


	public function index($input = FALSE) {

		if ($input !== FALSE)
			show_404();

		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title' => 'Submission Queue',
			'style' => 'main.css',
			'queue' => $this->queue_model->get_queue(),
			'working' => $this->settings_model->get_setting('queue_is_working')
		);

		$this->load->view('templates/header', $data);
		$this->load->view('pages/admin/queue', $data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------


	public function pause($input = FALSE){
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($input !== FALSE)
			show_404();
		$this->settings_model->set_setting('queue_is_working','0');
		echo 'success';
	}


	// ------------------------------------------------------------------------


	public function resume($input = FALSE){
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($input !== FALSE)
			show_404();
		shell_exec('php '.rtrim($this->settings_model->get_setting('tester_path'), '/').'/queue_process.php >/dev/null 2>/dev/null &');
		echo 'success';
	}


	// ------------------------------------------------------------------------


	public function empty_queue($input = FALSE){
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($input !== FALSE)
			show_404();
		$this->queue_model->empty_queue();
		echo 'success';
	}
}