<?php
/**
 * Sharif Judge online judge
 * @file scoreboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Settings extends CI_Controller{
	var $username;
	var $assignment;
	public function __construct(){
		parent::__construct();
		$this->load->helper('url');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username)); /* needed? */
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->load->helper('date');
		$this->load->model('settings_model');
	}

	public function check_timezone($str){
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
			'assignment' => $this->assignment, /* needed? */
			'title'=>'Settings',
			'style'=>'main.css',
			'tz'=>$this->settings_model->get_timezone()
		);
		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/settings',$data);
		$this->load->view('templates/footer');
	}

	public function update(){
		$this->form_validation->set_message('check_timezone','Wrong Timezone.');
		$this->form_validation->set_rules('timezones','timezone','callback_check_timezone');
		if($this->form_validation->run()){
			$this->settings_model->set_timezone($this->input->post('timezones'));
		}
		$this->index();
	}
}