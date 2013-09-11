<?php
/**
 * Sharif Judge online judge
 * @file Rejudge.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Rejudge extends CI_Controller {

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
		$this->problems = $this->assignment_model->all_problems($this->assignment['id']);
	}


	// ------------------------------------------------------------------------


	public function index($input = FALSE) {
		if ($input !== FALSE)
			show_404();

		$this->form_validation->set_rules('problem_id', 'problem id', 'required|integer');

		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title' => 'Rejudge',
			'style' => 'main.css',
			'problems' => $this->problems,
			'msg' => array()
		);

		if ($this->form_validation->run()){
			$problem_id = $this->input->post('problem_id');
			$this->load->model('queue_model');
			$this->queue_model->rejudge($this->assignment['id'], $problem_id);
			shell_exec('php '.rtrim($this->settings_model->get_setting('tester_path'),'/').'/queue_process.php >/dev/null 2>/dev/null &');
			$data['msg'] = array('Rejudge in progress');
		}

		$this->load->view('templates/header', $data);
		$this->load->view('pages/admin/rejudge', $data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------


	public function rejudge_one($input = FALSE){
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($input !== FALSE)
			show_404();
		$this->form_validation->set_rules('submit_id', 'submit id', 'required|integer');
		$this->form_validation->set_rules('username', 'username', 'required|alpha_numeric');
		$this->form_validation->set_rules('assignment', 'assignment', 'required|integer');
		$this->form_validation->set_rules('problem', 'problem', 'required|integer');
		if ($this->form_validation->run()){
			$this->load->model('queue_model');
			$this->queue_model->rejudge_one(array(
				'submit_id' => $this->input->post('submit_id'),
				'username' => $this->input->post('username'),
				'assignment' => $this->input->post('assignment'),
				'problem' => $this->input->post('problem'),
			));
			shell_exec('php '.rtrim($this->settings_model->get_setting('tester_path'),'/').'/queue_process.php >/dev/null 2>/dev/null &');
			echo 'success';
		}
		else
			echo 'failed';
	}

}