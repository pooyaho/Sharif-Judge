<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file scoreboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Settings extends CI_Controller{
	var $username;
	var $assignment;
	var $user_level;
	public function __construct(){
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		if ( $this->user_model->get_user_level($this->username) == 0)
			show_404();
		$this->form_status = "";
	}

	/*
	 * This function validates input filed 'timezone'
	 */
	public function _check_timezone($str){
		$timezones = array('UM1','UM2','UM3','UM35','UM4','UM45','UM5','UM6','UM7','UM8','UM9','UM95','UM10','UM11','UM12',
			'UTC',
			'UP1','UP2','UP3','UP35','UP4','UP45','UP5','UP55','UP575','UP6','UP65','UP7','UP8','UP875','UP9','UP95','UP10',
			'UP105','UP11','UP115','UP12','UP1275','UP13','UP14'
		);
		if (in_array($str,$timezones) )
			return TRUE;
		return FALSE;
	}

	public function index(){
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Settings',
			'style'=>'main.css',
			'tz'=>$this->settings_model->get_setting('timezone'),
			'tester_path'=>$this->settings_model->get_setting('tester_path'),
			'assignments_root'=>$this->settings_model->get_setting('assignments_root'),
			'file_size_limit'=>$this->settings_model->get_setting('file_size_limit'),
			'default_late_rule'=>$this->settings_model->get_setting('default_late_rule'),
			'enable_easysandbox'=>$this->settings_model->get_setting('enable_easysandbox'),
			'enable_shield'=>$this->settings_model->get_setting('enable_shield'),
			'enable_java_policy'=>$this->settings_model->get_setting('enable_java_policy'),
			'enable_log'=>$this->settings_model->get_setting('enable_log'),
			'form_status' => $this->form_status
		);
		$data ['defc'] = file_get_contents(rtrim($this->settings_model->get_setting('tester_path'),'/')."/shield/defc.h");
		$data ['defcpp'] = file_get_contents(rtrim($this->settings_model->get_setting('tester_path'),'/')."/shield/defcpp.h");
		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/settings',$data);
		$this->load->view('templates/footer');
	}

	public function update(){
		$this->form_validation->set_message('_check_timezone','Wrong Timezone.');
		$this->form_validation->set_rules('timezones','timezone','callback__check_timezone');
		$this->form_validation->set_rules('file_size_limit','File size limit','integer|greater_than[-1]');
		if($this->form_validation->run()){
			$this->settings_model->set_setting('timezone',$this->input->post('timezones'));
			$this->settings_model->set_setting('tester_path',$this->input->post('tester_path'));
			$this->settings_model->set_setting('assignments_root',$this->input->post('assignments_root'));
			$this->settings_model->set_setting('file_size_limit',$this->input->post('file_size_limit'));
			$this->settings_model->set_setting('default_late_rule',$this->input->post('default_late_rule'));
			$this->settings_model->set_setting('enable_easysandbox',$this->input->post('enable_easysandbox')===FALSE?0:1);
			$this->settings_model->set_setting('enable_shield',$this->input->post('enable_shield')===FALSE?0:1);
			$this->settings_model->set_setting('enable_java_policy',$this->input->post('enable_java_policy')===FALSE?0:1);
			$this->settings_model->set_setting('enable_log',$this->input->post('enable_log')===FALSE?0:1);
			ob_start();
			$this->form_status = "";
			if (file_put_contents(rtrim($this->settings_model->get_setting('tester_path'),'/')."/shield/defc.h",$this->input->post('def_c'))===FALSE)
				$this->form_status .= "defc";
			if (file_put_contents(rtrim($this->settings_model->get_setting('tester_path'),'/')."/shield/defcpp.h",$this->input->post('def_cpp'))===FALSE)
				$this->form_status .= "defcpp";
			ob_end_clean();
			if ($this->form_status=="")
				$this->form_status = "ok";
		}
		else
			$this->form_status = "error";
		$this->index();
	}
}