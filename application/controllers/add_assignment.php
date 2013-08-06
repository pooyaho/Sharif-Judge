<?php
/**
 * Sharif Judge online judge
 * @file add_assignment.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Add_assignment extends CI_Controller{
	var $username;
	var $assignment;
	var $user_level;
	var $form_status;
	var $edit;
	public function __construct(){
		parent::__construct();
		$this->load->library('session');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
		if ( $this->user_level <=1)
			show_error("You have not enough permission to access this page.");
		$this->load->library('upload');
		$this->form_status = "";
		$this->edit_assignment=array();
		$this->edit=FALSE;
	}

	public function index(){
		$this->load->model('user_model');
		$user=$this->user_model->get_user($this->username);
		$data = array(
			'username'=>$this->username,
			'user_level' => $this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=> ($this->edit?"Edit":"Add").' Assignment',
			'style'=>'main.css',
			'form_status' => $this->form_status,
			'edit'=> $this->edit
		);

		if($this->edit){
			$data['edit_assignment'] = $this->assignment_model->assignment_info($this->edit_assignment);
			$data['problems']=$this->assignment_model->all_problems($this->edit_assignment);
		}

		$this->load->view('templates/header',$data);
		$this->load->view('pages/admin/add_assignment',$data);
		$this->load->view('templates/footer');
	}

	public function add(){
		$this->form_validation->set_rules('assignment_name','assignment name','required|max_length[50]');
		$this->form_validation->set_rules('start_time','start time','required');
		$this->form_validation->set_rules('finish_time','finish time','required');
		$this->form_validation->set_rules('extra_time','extra time','required');
		$this->form_validation->set_rules('name[]','problem name','required|max_length[50]');
		$this->form_validation->set_rules('score[]','problem score','required|integer');
		$this->form_validation->set_rules('c_time_limit[]','time limit','required|integer');
		$this->form_validation->set_rules('python_time_limit[]','time limit','required|integer');
		$this->form_validation->set_rules('java_time_limit[]','time limit','required|integer');
		$this->form_validation->set_rules('memory_limit[]','memory limit','required|integer');
		$this->form_validation->set_rules('filetypes[]','file types','required');
		$this->form_status='error';
		if ($this->form_validation->run()){
			if ($this->edit)
				$the_id = $this->edit_assignment;
			else
				$the_id = $this->assignment_model->last_assignment_id()+1;

			$config['upload_path'] = rtrim($this->settings_model->get_setting('assignments_root'),'/');
			$config['allowed_types'] = 'zip';
			$this->upload->initialize($config);
			if($this->upload->do_upload('tests')){
				$this->load->library('unzip');
				$this->unzip->allow(array('txt'));
				$assignment_dir = $config['upload_path']."/assignment_{$the_id}";
				if (!file_exists($assignment_dir))
					mkdir($assignment_dir,0700);
				$u_data = $this->upload->data();
				if ( $this->unzip->extract($u_data['full_path'], $assignment_dir) ){
					$this->assignment_model->add_assignment($the_id,$this->edit);
					$this->form_status='tests_updated';
				}
				else{
					$this->form_status='corrupted';
					rmdir($assignment_dir);
				}
				unlink($u_data['full_path']);
			}
			else if($this->edit){
				$this->assignment_model->add_assignment($the_id,$this->edit);
				$this->form_status='ok';
			}
		}
		$this->index();
	}

	public function edit($assignment_id){
		$this->edit_assignment=$assignment_id;
		$this->edit=TRUE;
		if($this->input->post('number_of_problems')===FALSE){
			$this->index();
		}
		else{
			$this->add();
		}
	}
}