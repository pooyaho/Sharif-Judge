<?php
/**
 * Sharif Judge online judge
 * @file Notifications.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {

	var $username;
	var $assignment;
	var $user_level;
	var $notif_edit;


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
		$this->load->model('notifications_model');
		$this->notif_edit = FALSE;
	}


	// ------------------------------------------------------------------------


	public function index(){

		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Notifications',
			'style'=>'main.css',
			'notifications' => $this->notifications_model->get_all_notifications()
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/notifications',$data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------


	public function add(){
		if ( $this->user_level <=1)
			show_error('You have not enough permission to access this page.');

		$this->form_validation->set_rules('title','title','trim');
		$this->form_validation->set_rules('text','text','');

		if($this->form_validation->run()){
			if ($this->input->post('id')===NULL)
				$this->notifications_model->add_notification($this->input->post('title'),$this->input->post('text'));
			else
				$this->notifications_model->update_notification($this->input->post('id'),$this->input->post('title'),$this->input->post('text'));
			redirect('notifications');
		}

		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Add Notification',
			'style'=>'main.css',
			'notif_edit' => $this->notif_edit
		);

		if ($this->notif_edit!==FALSE)
			$data['title'] = 'Edit Notification';


		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/add_notification',$data);
		$this->load->view('templates/footer');

	}


	// ------------------------------------------------------------------------


	public function edit($notif_id = FALSE) {
		if ( $this->user_level <=1)
			show_error('You have not enough permission to access this page.');
		if ($notif_id === FALSE)
			show_404();
		if (!is_numeric($notif_id))
			show_error('Wrong ID');
		$this->notif_edit = $this->notifications_model->get_notification($notif_id);
		$this->add();
	}


	// ------------------------------------------------------------------------


	public function delete($input = FALSE) {
		if ( $this->user_level <=1)
			show_error('You have not enough permission to access this page.');
		if ($input !== FALSE)
			show_404();
		if ($this->input->post('id')===NULL)
			show_404();
		$this->notifications_model->delete_notification($this->input->post('id'));
	}

}