<?php
/**
 * Sharif Judge online judge
 * @file Add_assignment.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Add_assignment extends CI_Controller{

	var $username;
	var $assignment;
	var $user_level;
	var $form_status;
	var $edit_assignment;
	var $edit;


	// ------------------------------------------------------------------------


	public function __construct() {
		parent::__construct();
		$this->load->driver('session');
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		if ($this->user_level <= 1)
			show_error('You have not enough permission to access this page.');
		$this->load->library('upload');
		$this->form_status = '';
		$this->edit_assignment = array();
		$this->edit = FALSE;
	}


	// ------------------------------------------------------------------------


	public function index($input = FALSE){
		if ($input !== FALSE)
			show_404();
		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title' => ($this->edit?'Edit':'Add').' Assignment',
			'style' => 'main.css',
			'form_status' => $this->form_status,
			'edit' => $this->edit
		);

		if ($this->edit){
			$data['edit_assignment'] = $this->assignment_model->assignment_info($this->edit_assignment);
			if ($data['edit_assignment']['id'] === 0)
				show_404();
			$data['problems'] = $this->assignment_model->all_problems($this->edit_assignment);
		}else {
			$names = $this->input->post('name');
			if ($names === NULL)
				$data['problems'] = array(
					array(
						'id' => 1,
						'name' => 'Problem ',
						'score' => 100,
						'c_time_limit' => 500,
						'python_time_limit' => 1500,
						'java_time_limit' => 2000,
						'memory_limit' => 50000,
						'allowed_languages' => 'C,C++,Python 2,Python 3,Java',
						'diff_cmd' => 'diff',
						'diff_arg' => '-iw',
						'is_upload_only' => 0
					)
				);
			else {
				$names = $this->input->post('name');
				$scores = $this->input->post('score');
				$c_tl = $this->input->post('c_time_limit');
				$py_tl = $this->input->post('python_time_limit');
				$java_tl = $this->input->post('java_time_limit');
				$ml = $this->input->post('memory_limit');
				$ft = $this->input->post('languages');
				$dc = $this->input->post('diff_cmd');
				$da = $this->input->post('diff_arg');
				$data['problems'] = array();
				$uo = $this->input->post('is_upload_only');
				if ($uo === NULL)
					$uo = array();
				for ($i=0; $i<count($names); $i++){
					array_push($data['problems'], array(
						'id' => $i+1,
						'name' => $names[$i],
						'score' => $scores[$i],
						'c_time_limit' => $c_tl[$i],
						'python_time_limit' => $py_tl[$i],
						'java_time_limit' => $java_tl[$i],
						'memory_limit' => $ml[$i],
						'allowed_languages' => $ft[$i],
						'diff_cmd' => $dc[$i],
						'diff_arg' => $da[$i],
						'is_upload_only' => in_array($i+1,$uo)?1:0,
					));
				}
			}
		}

		$this->load->view('templates/header', $data);
		$this->load->view('pages/admin/add_assignment', $data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------


	public function add($input = FALSE){
		if ($input !== FALSE)
			show_404();
		$this->form_validation->set_rules('assignment_name', 'assignment name', 'required|max_length[50]');
		$this->form_validation->set_rules('start_time', 'start time', 'required');
		$this->form_validation->set_rules('finish_time', 'finish time', 'required');
		$this->form_validation->set_rules('extra_time', 'extra time', 'required');
		$this->form_validation->set_rules('participants', 'participants', '');
		$this->form_validation->set_rules('late_rule', 'coefficient rule', 'required');
		$this->form_validation->set_rules('open', 'open', '');
		$this->form_validation->set_rules('scoreboard', 'scoreboard rule', '');
		$this->form_validation->set_rules('name[]', 'problem name', 'required|max_length[50]');
		$this->form_validation->set_rules('score[]', 'problem score', 'required|integer');
		$this->form_validation->set_rules('c_time_limit[]', 'C/C++ time limit', 'required|integer');
		$this->form_validation->set_rules('python_time_limit[]', 'python time limit', 'required|integer');
		$this->form_validation->set_rules('java_time_limit[]', 'java time limit', 'required|integer');
		$this->form_validation->set_rules('memory_limit[]', 'memory limit', 'required|integer');
		$this->form_validation->set_rules('languages[]', 'languages', 'required');
		$this->form_validation->set_rules('diff_cmd[]', 'diff command', 'required');
		$this->form_validation->set_rules('diff_arg[]', 'diff argument', 'required');
		$this->form_status = 'error';
		if ($this->form_validation->run()){
			if ($this->edit)
				$the_id = $this->edit_assignment;
			else
				$the_id = $this->assignment_model->new_assignment_id();

			$config['upload_path'] = rtrim($this->settings_model->get_setting('assignments_root'), '/');
			shell_exec('rm '.$config['upload_path'].'/*.zip');
			$config['allowed_types'] = 'zip';
			$this->upload->initialize($config);
			if ($this->upload->do_upload('tests')){
				$this->load->library('unzip');
				$this->unzip->allow(array('txt', 'cpp'));
				$assignment_dir = $config['upload_path']."/assignment_{$the_id}";
				if ( ! file_exists($assignment_dir))
					mkdir($assignment_dir, 0700);
				$u_data = $this->upload->data();
				if ($this->unzip->extract($u_data['full_path'], $assignment_dir)){
					for ($i=1; $i <= $this->input->post('number_of_problems'); $i++)
						if ( ! file_exists($assignment_dir."/p$i"))
							mkdir($assignment_dir."/p$i", 0700);
					$this->assignment_model->add_assignment($the_id, $this->edit);
					$this->form_status = 'tests_updated';
				}
				else {
					$this->form_status = 'corrupted';
					rmdir($assignment_dir);
				}
				unlink($u_data['full_path']);
			}
			elseif ($this->edit){
				$this->assignment_model->add_assignment($the_id, $this->edit);
				$this->form_status = 'ok';
			}
		}
		$this->index();
	}


	// ------------------------------------------------------------------------


	public function edit($assignment_id){
		$this->edit_assignment = $assignment_id;
		$this->edit = TRUE;
		if ($this->input->post('number_of_problems') === NULL)
			$this->index();
		else
			$this->add();
	}



}