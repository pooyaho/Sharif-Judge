<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file users.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Users extends CI_Controller {
	var $username;
	var $assignment;
	var $user_level;
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		if ( $this->user_level <= 2)
			show_error("You have not enough permission to access this page.");
	}

	public function index(){
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Users',
			'style'=>'main.css',
			'users'=>$this->user_model->get_all_users()
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/users',$data);
		$this->load->view('templates/footer');
	}

	public function add(){
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Add Users',
			'style'=>'main.css',
		);
		$this->form_validation->set_rules('new_users','New Users','required');
		if ($this->form_validation->run()) {
			list( $ok , $error) = $this->user_model->add_users($this->input->post('new_users'),$this->input->post('send_mail'),$this->input->post("delay"));
			echo '<p class="shj_ok">These users added successfully:</p>';
			if (count($ok)>0){
				echo '<ol>';
				foreach ($ok as $item){
					echo '<li>Usename: '.$item[0].' Email: '.$item[1].' Password: '.$item[2].' Role: '.$item[3].'</li>';
				}
				echo '</ol>';
			}
			else
				echo 'No users.';
			echo '<p class="shj_error">Error adding these users:</p>';
			if (count($error)>0){
				echo '<ol>';
				foreach ($error as $item){
					echo '<li>Usename: '.$item[0].' Email: '.$item[1].' Password: '.$item[2].' Role: '.$item[3].' ('.$item[4].')</li>';
				}
				echo '</ol>';
			}
			else
				echo 'No users.';
			exit;
		}

		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/add_user',$data);
		$this->load->view('templates/footer');
	}


	public function delete($user_id=FALSE) {
		if ($user_id===FALSE || !is_numeric($user_id)){
			die("Incorrect user id");
		}
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Delete User',
			'style'=>'main.css',
			'id'=>$user_id
		);
		if ($this->input->post('delete')=="delete"){
			$this->user_model->delete_user($user_id,$this->input->post('delete_submissions')===FALSE?FALSE:TRUE);
			$data['deleted'] = TRUE;
			$data['title']='Users';
			$data['users']=$this->user_model->get_all_users();
			$this->load->view('templates/header',$data);
			$this->load->view('pages/admin/users',$data);
			$this->load->view('templates/footer');
		}
		else{
			$this->load->view('templates/header',$data);
			$this->load->view('pages/admin/delete_user',$data);
			$this->load->view('templates/footer');
		}
	}
}