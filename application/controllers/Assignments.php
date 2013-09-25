<?php
/**
 * Sharif Judge online judge
 * @file Assignments.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Assignments extends CI_Controller
{

	var $username;
	var $assignment;
	var $user_level;

	var $error_messages;
	var $success_messages;
	var $edit_assignment;
	var $edit;


	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();
		$this->load->driver('session');
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');

		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);

		$this->error_messages = array();
		$this->success_messages = array();
		$this->edit_assignment = array();
		$this->edit = FALSE;
	}


	// ------------------------------------------------------------------------


	public function index()
	{
		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'title' => 'Assignments',
			'style' => 'main.css',
			'success_messages' => $this->success_messages,
			'error_messages' => $this->error_messages
		);

		$this->form_validation->set_rules('assignment_select', 'Assignment', 'integer|greater_than[0]');

		if ($this->form_validation->run())
		{
			$this->assignment = $this->assignment_model->assignment_info($this->input->post('assignment_select'));
			$this->user_model->select_assignment($this->username, $this->assignment['id']);
		}

		$data['assignment'] = $this->assignment;

		$this->load->view('templates/header', $data);
		$this->load->view('pages/assignments', $data);
		$this->load->view('templates/footer');

	}


	// ------------------------------------------------------------------------


	/**
	 * Used by ajax request (for select assignment from top bar)
	 */
	public function select()
	{
		if ( ! $this->input->is_ajax_request() )
			show_404();

		$this->form_validation->set_rules('assignment_select', 'Assignment', 'integer|greater_than[0]');

		if ($this->form_validation->run())
		{
			$this->user_model->select_assignment($this->username, $this->input->post('assignment_select'));
			$this->assignment = $this->assignment_model->assignment_info($this->input->post('assignment_select'));
			echo $this->assignment['finish_time'].",".$this->assignment['extra_time'];
		}
		else
			echo 'shj_failed';
	}


	// ------------------------------------------------------------------------


	/**
	 * Compressing and downloading test data of an assignment to the browser
	 */
	public function downloadtests($assignment_id)
	{
		if ( $this->user_level <= 1)
			show_error('You have not enough permission to download test data.');

		$this->load->library('zip');

		$assignment = $this->assignment_model->assignment_info($assignment_id);

		$number_of_problems = $assignment['problems'];

		for ($i=1 ; $i<=$number_of_problems ; $i++)
		{
			$root_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
				"/assignment_{$assignment_id}";

			$path = $root_path."/p{$i}/in";
			$this->zip->read_dir($path, FALSE, $root_path);

			$path = $root_path."/p{$i}/out";
			$this->zip->read_dir($path, FALSE, $root_path);

			$path = $root_path."/p{$i}/tester.cpp";
			if (file_exists($path))
				$this->zip->add_data("p{$i}/tester.cpp", file_get_contents($path));
		}

		$this->zip->download("assignment{$assignment_id}_tests_".date('Y-m-d_H-i',shj_now()).'.zip');
	}


	// ------------------------------------------------------------------------


	/**
	 * Compressing and downloading final codes of an assignment to the browser
	 */
	public function download($assignment_id)
	{
		if ( $this->user_level == 0)
			show_error('You have not enough permission to download codes.');

		$this->load->model('submit_model');
		$items = $this->submit_model->get_final_submissions($assignment_id, $this->user_level, $this->username);

		$this->load->library('zip');

		foreach ($items as $item)
		{
			$file_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
				"/assignment_{$item['assignment']}/p{$item['problem']}/{$item['username']}/{$item['file_name']}.".filetype_to_extension($item['file_type']);
			if ( ! file_exists($file_path))
				continue;
			$file = file_get_contents($file_path);
			$this->zip->add_data("by_user/{$item['username']}/p{$item['problem']}.".filetype_to_extension($item['file_type']), $file);
			$this->zip->add_data("by_problem/problem_{$item['problem']}/{$item['username']}.".filetype_to_extension($item['file_type']), $file);
		}

		$this->zip->download("assignment{$assignment_id}_codes_".date('Y-m-d_H-i',shj_now()).'.zip');
	}


	// ------------------------------------------------------------------------


	/**
	 * Delete assignment
	 */
	public function delete($assignment_id)
	{
		if ($this->user_level <= 1)
			show_error('You have not enough permission to do this.');

		$assignment = $this->assignment_model->assignment_info($assignment_id);

		if ($assignment['id'] === 0)
			show_404();

		if ($this->input->post('delete') === 'delete')
		{
			$this->assignment_model->delete_assignment($assignment_id, $this->input->post('delete_codes')===NULL?FALSE:TRUE);
			redirect('assignments');
		}

		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title' => 'Delete Assignment',
			'style' => 'main.css',
			'id' => $assignment_id,
			'name' => $assignment['name']
		);

		$this->load->view('templates/header', $data);
		$this->load->view('pages/admin/delete_assignment', $data);
		$this->load->view('templates/footer');

	}



	// ------------------------------------------------------------------------


	/**
	 * This method gets inputs from user for adding/editing assignment
	 */
	public function add($input = FALSE)
	{

		if ($this->user_level <= 1)
			show_error('You have not enough permission to access this page.');

		if ($input !== FALSE)
			show_404();

		$this->load->library('upload');

		if ( ! empty($_POST) )
			if ($this->_add()){ // add/edit assignment
				if ( ! $this->edit) // if adding assignment (not editing)
				{
					// goto Assignment page
					$this->index();
					return;
				}
			}

		$data = array(
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title' => ($this->edit?'Edit':'Add').' Assignment',
			'style' => 'main.css',
			'error_messages' => $this->error_messages,
			'success_messages' => $this->success_messages,
			'edit' => $this->edit
		);

		if ($this->edit)
		{
			$data['edit_assignment'] = $this->assignment_model->assignment_info($this->edit_assignment);
			if ($data['edit_assignment']['id'] === 0)
				show_404();
			$data['problems'] = $this->assignment_model->all_problems($this->edit_assignment);
		}
		else
		{
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
			else
			{
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


	/**
	 * Add/Edit assignment
	 */
	private function _add()
	{

		if ($this->user_level <= 1)
			show_error('You have not enough permission to access this page.');

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

		if ( ! $this->form_validation->run())
			return FALSE;

		if ($this->edit)
			$the_id = $this->edit_assignment;
		else
			$the_id = $this->assignment_model->new_assignment_id();

		$config['upload_path'] = rtrim($this->settings_model->get_setting('assignments_root'), '/');
		shell_exec('rm '.$config['upload_path'].'/*.zip');

		$config['allowed_types'] = 'zip';
		$this->upload->initialize($config);

		if ($this->upload->do_upload('tests'))
		{
			$this->load->library('unzip');
			$this->unzip->allow(array('txt', 'cpp'));
			$assignment_dir = $config['upload_path']."/assignment_{$the_id}";
			if ( ! file_exists($assignment_dir))
				mkdir($assignment_dir, 0700);
			$u_data = $this->upload->data();
			$extract_result = $this->unzip->extract($u_data['full_path'], $assignment_dir);
			unlink($u_data['full_path']);
			if ( $extract_result !== FALSE){
				for ($i=1; $i <= $this->input->post('number_of_problems'); $i++)
					if ( ! file_exists($assignment_dir."/p$i"))
						mkdir($assignment_dir."/p$i", 0700);
				$this->assignment_model->add_assignment($the_id, $this->edit);
				$this->success_messages[] = 'Assignment '.($this->edit?'updated':'added').' successfully.';
				$this->success_messages[] = 'Tests uploaded successfully.';
				return TRUE;
			}
			else {
				$this->error_messages[] = 'Error extracting zip archive.';
				$this->error_messages = array_merge($this->error_messages , $this->unzip->errors_array());
				rmdir($assignment_dir);
				return FALSE;
			}
		}
		elseif ($this->edit)
		{
			$this->assignment_model->add_assignment($the_id, $this->edit);
			$this->success_messages[] = 'Assignment '.($this->edit?'updated':'added').' successfully.';
			return TRUE;
		}
		return FALSE;
	}


	// ------------------------------------------------------------------------


	public function edit($assignment_id)
	{

		if ($this->user_level <= 1)
			show_error('You have not enough permission to access this page.');

		$this->edit_assignment = $assignment_id;
		$this->edit = TRUE;
		$this->add();
	}


}