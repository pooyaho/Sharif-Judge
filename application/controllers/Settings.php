<?php
/**
 * Sharif Judge online judge
 * @file Settings.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CI_Controller{

	var $username;
	var $assignment;
	var $user_level;
	var $form_status;
	var $errors;


	// ------------------------------------------------------------------------


	public function __construct(){
		parent::__construct();
		$this->load->driver('session');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		if ( $this->user_level <= 2)
			show_error('You have not enough permission to access this page.');
		$this->form_status = '';
		$this->errors = array();
	}


	// ------------------------------------------------------------------------


	public function index($input = FALSE){
		if ($input !== FALSE)
			show_404();
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Settings',
			'style'=>'main.css',
			'sandbox_built'=>file_exists(rtrim($this->settings_model->get_setting('tester_path'),'/').'/easysandbox/EasySandbox.so'),
			'tz'=>$this->settings_model->get_setting('timezone'),
			'tester_path'=>$this->settings_model->get_setting('tester_path'),
			'assignments_root'=>$this->settings_model->get_setting('assignments_root'),
			'file_size_limit'=>$this->settings_model->get_setting('file_size_limit'),
			'output_size_limit'=>$this->settings_model->get_setting('output_size_limit'),
			'default_late_rule'=>$this->settings_model->get_setting('default_late_rule'),
			'enable_easysandbox'=>$this->settings_model->get_setting('enable_easysandbox'),
			'enable_c_shield'=>$this->settings_model->get_setting('enable_c_shield'),
			'enable_cpp_shield'=>$this->settings_model->get_setting('enable_cpp_shield'),
			'enable_py2_shield'=>$this->settings_model->get_setting('enable_py2_shield'),
			'enable_py3_shield'=>$this->settings_model->get_setting('enable_py3_shield'),
			'enable_java_policy'=>$this->settings_model->get_setting('enable_java_policy'),
			'enable_log'=>$this->settings_model->get_setting('enable_log'),
			'enable_registration'=>$this->settings_model->get_setting('enable_registration'),
			'mail_from'=>$this->settings_model->get_setting('mail_from'),
			'mail_from_name'=>$this->settings_model->get_setting('mail_from_name'),
			'reset_password_mail'=>$this->settings_model->get_setting('reset_password_mail'),
			'add_user_mail'=>$this->settings_model->get_setting('add_user_mail'),
			'results_per_page'=>$this->settings_model->get_setting('results_per_page'),
			'week_start'=>$this->settings_model->get_setting('week_start'),
			'form_status' => $this->form_status,
			'errors' => $this->errors
		);
		ob_start();
		$data ['defc'] = file_get_contents(rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/defc.h');
		$data ['defcpp'] = file_get_contents(rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/defcpp.h');
		$data ['shield_py2'] = file_get_contents(rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/shield_py2.py');
		$data ['shield_py3'] = file_get_contents(rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/shield_py3.py');
		ob_end_clean();
		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/settings',$data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------


	public function update($input = FALSE){
		if ($input !== FALSE)
			show_404();
		$this->form_validation->set_rules('timezone','timezone','required');
		$this->form_validation->set_rules('file_size_limit','File size limit','integer|greater_than_equal_to[0]');
		$this->form_validation->set_rules('output_size_limit','Output size limit','integer|greater_than_equal_to[0]');
		$this->form_validation->set_rules('results_per_page','results per page','integer|greater_than_equal_to[0]');
		if($this->form_validation->run()){
			ob_start();
			$this->form_status = 'ok';
			$defc_path = rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/defc.h';
			$defcpp_path = rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/defcpp.h';
			$shpy2_path = rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/shield_py2.py';
			$shpy3_path = rtrim($this->settings_model->get_setting('tester_path'),'/').'/shield/shield_py3.py';
			if ($this->input->post('def_c') !== file_get_contents($defc_path))
				if (file_exists($defc_path) && file_put_contents($defc_path,$this->input->post('def_c'))===FALSE)
					array_push($this->errors,'File defc.h is not writable. Edit it manually.');
			if ($this->input->post('def_cpp') !== file_get_contents($defcpp_path))
				if (file_exists($defcpp_path) && file_put_contents($defcpp_path,$this->input->post('def_cpp'))===FALSE)
					array_push($this->errors,'File defcpp.h is not writable. Edit it manually.');
			if ($this->input->post('shield_py2') !== file_get_contents($shpy2_path))
				if (file_exists($shpy2_path) && file_put_contents($shpy2_path,$this->input->post('shield_py2'))===FALSE)
					array_push($this->errors,'File shield_py2.py is not writable. Edit it manually.');
			if ($this->input->post('shield_py3') !== file_get_contents($shpy3_path))
				if (file_exists($shpy3_path) && file_put_contents($shpy3_path,$this->input->post('shield_py3'))===FALSE)
					array_push($this->errors,'File shield_py3.py is not writable. Edit it manually.');
			ob_end_clean();
			$timezone = $this->input->post('timezone');
			// if timezone is invalid, set it to 'Asia/Tehran' :
			if ( ! in_array($timezone, DateTimeZone::listIdentifiers()) )
				$timezone='Asia/Tehran';
			$this->settings_model->set_setting('timezone', $timezone);
			$this->settings_model->set_setting('tester_path', $this->input->post('tester_path'));
			$this->settings_model->set_setting('assignments_root', $this->input->post('assignments_root'));
			$this->settings_model->set_setting('file_size_limit', $this->input->post('file_size_limit'));
			$this->settings_model->set_setting('output_size_limit', $this->input->post('output_size_limit'));
			$this->settings_model->set_setting('default_late_rule', $this->input->post('default_late_rule'));
			$this->settings_model->set_setting('enable_easysandbox', $this->input->post('enable_easysandbox')===NULL?0:1);
			$this->settings_model->set_setting('enable_c_shield', $this->input->post('enable_c_shield')===NULL?0:1);
			$this->settings_model->set_setting('enable_cpp_shield', $this->input->post('enable_cpp_shield')===NULL?0:1);
			$this->settings_model->set_setting('enable_py2_shield', $this->input->post('enable_py2_shield')===NULL?0:1);
			$this->settings_model->set_setting('enable_py3_shield', $this->input->post('enable_py3_shield')===NULL?0:1);
			$this->settings_model->set_setting('enable_java_policy', $this->input->post('enable_java_policy')===NULL?0:1);
			$this->settings_model->set_setting('enable_log', $this->input->post('enable_log')===NULL?0:1);
			$this->settings_model->set_setting('enable_registration', $this->input->post('enable_registration')===NULL?0:1);
			$this->settings_model->set_setting('mail_from', $this->input->post('mail_from'));
			$this->settings_model->set_setting('mail_from_name', $this->input->post('mail_from_name'));
			$this->settings_model->set_setting('reset_password_mail', $this->input->post('reset_password_mail'));
			$this->settings_model->set_setting('add_user_mail', $this->input->post('add_user_mail'));
			$this->settings_model->set_setting('results_per_page', $this->input->post('results_per_page'));
			$this->settings_model->set_setting('week_start', $this->input->post('week_start'));
		}
		else
			$this->form_status = 'error';
		$this->index();
	}


}