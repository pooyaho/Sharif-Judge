<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file final_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Submissions extends CI_Controller{
	var $username;
	var $assignment;
	var $user_level;
	var $final_items;
	public function __construct(){
		parent::__construct();
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			redirect('login');
		}
		$this->load->model('submit_model');
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);
	}


	private function download_excel($view){
		$now=date('Y-m-d H:i:s',shj_now());
		$this->load->library('excel');
		$this->excel->set_file_name('judge_result.xls'); /* todo more relevant file name */
		$this->excel->addHeader("Time: $now");
		$this->excel->addHeader(NULL); //newline
		$row=array(/*"#1","#2",*/"Final","Submit ID","Username","Display Name","Problem","Submit Time","Score","Coefficient","Final Score","Status","#");
		$this->excel->addRow($row);
		if ($view=='final')
			$items = $this->submit_model->get_final_submissions($this->assignment['id'],$this->user_level,$this->username);
		else
			$items = $this->submit_model->get_all_submissions($this->assignment['id'],$this->user_level,$this->username);
		$finish = strtotime($this->assignment['finish_time']);
		foreach ($items as $item){
			if(!isset($name[$item['username']]))
				$name[$item['username']]=$this->user_model->get_user($item['username'])->display_name;

			$pi = $this->assignment_model->problem_info($this->assignment['id'],$item['problem']);

			$pre_score = ceil($item['pre_score']*$pi['score']/10000);

			$checked='';
			if ($view=='all'){
				if (isset($this->final_items[$item['username']][$item['problem']]['submit_id']))
					if ($this->final_items[$item['username']][$item['problem']]['submit_id'] == $item['submit_id'])
						$checked='*';
			}
			else
				$checked="*";


			$extra_time = $this->assignment['extra_time'];
			$delay = strtotime($item['time'])-$finish;
			ob_start();
			if ( eval($this->assignment['late_rule']) === FALSE ){
				$coefficient = "error";
				$final_score = 0;
			}
			else {
				$final_score = ceil($pre_score*$coefficient/100);
			}
			ob_end_clean();

			$row=array(
				/*"1",
				"2",*/
				$checked,
				$item['submit_id'],
				$item['username'],
				$name[$item['username']],
				$item['problem']." (".$pi['name'].")",
				$item['time'],
				$pre_score,
				$coefficient,
				$final_score,
				$item['status'],
				($view=="final"?$item['submit_count']:$item['submit_number'])
			);
			$this->excel->addRow($row);
		}
		$this->excel->sendFile();
	}


	public function the_final($type=FALSE){

		if ($type=="excel"){
			$this->download_excel('final');
			exit;
		}

		$data = array(
			'view'=>'final',
			'username'=>$this->username,
			'user_level'=>$this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'Final Submissions',
			'style'=>'main.css',
			'items'=>$this->submit_model->get_final_submissions($this->assignment['id'],$this->user_level,$this->username)
		);

		$this->load->view('templates/header',$data);
		$this->load->view('pages/submissions',$data);
		$this->load->view('templates/footer');
	}



	public function all($type=FALSE){

		$final = $this->submit_model->get_final_submissions($this->assignment['id'],$this->user_level,$this->username);
		$this->final_items=array();
		foreach ($final as $item){
			$this->final_items[$item['username']][$item['problem']]=$item;
		}

		if ($type=="excel"){
			$this->download_excel('all');
			exit;
		}

		$data = array(
			'view'=>'all',
			'username'=>$this->username,
			'user_level'=>$this->user_level,
			'all_assignments'=>$this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title'=>'All Submissions',
			'style'=>'main.css',
			'items'=>$this->submit_model->get_all_submissions($this->assignment['id'],$this->user_level,$this->username),
			'final_items' => $this->final_items
		);
		$this->load->view('templates/header',$data);
		$this->load->view('pages/submissions',$data);
		$this->load->view('templates/footer');
	}

	public function select(){ /* used by ajax request (for selecting final submission) */
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if (shj_now() > strtotime($this->assignment['finish_time'])+$this->assignment['extra_time'])
			die('shj_failed');
		$this->form_validation->set_rules('submit_id','Submit ID',"integer|greater_than[0]");
		$this->form_validation->set_rules('problem','problem',"integer|greater_than[0]");
		//echo $this->input->post('problem'); echo '<br>'; echo $this->input->post('submit_id');
		if($this->form_validation->run() && $this->submit_model->set_final_submission($this->username, $this->assignment['id'], $this->input->post('problem'), $this->input->post('submit_id'))){
			echo "shj_success";
		}
		else
			echo 'shj_failed';
	}

	public function view_code(){ /* for "view code" or "view result" or "view log" */
		$this->form_validation->set_rules('code','integer|greater_than[-1]|less_than[2]');
		$this->form_validation->set_rules('username','required|min_length[3]|max_length[20]|alpha_numeric|xss_clean');
		$this->form_validation->set_rules('assignment','integer|greater_than[0]');
		$this->form_validation->set_rules('problem','integer|greater_than[0]');
		$this->form_validation->set_rules('submit_id','integer|greater_than[0]');
		if($this->form_validation->run()){
			$submission = $this->submit_model->get_submission(
				$this->input->post('username'),
				$this->input->post('assignment'),
				$this->input->post('problem'),
				$this->input->post('submit_id')
			);
			if ($submission===FALSE)
				show_404();

			if ($this->user_level==0 && $this->input->post('code')==2)
				show_404();

			if ($this->user_level==0 && $this->username != $submission['username'])
				die("Don't try to see other users' codes. :)");

			$data=array(
				'username'=>$this->username,
				'user_level'=>$this->user_level,
				'all_assignments'=>$this->assignment_model->all_assignments(),
				'assignment' => $this->assignment,
				'title'=>'View Code',
				'style'=>'main.css',
				'code' => $this->input->post('code')
			);


			if($data['code']==0)
				$data['title']="View Result";
			else if($data['code']==2)
				$data['title']="View Log";


			if ($data['code']==0)
				$file_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
					"/assignment_{$submission['assignment']}/p{$submission['problem']}/{$submission['username']}/result-{$submission['submit_id']}.html";
			else if ($data['code']==1)
				$file_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
					"/assignment_{$submission['assignment']}/p{$submission['problem']}/{$submission['username']}/{$submission['file_name']}.{$submission['file_type']}";
			else if ($data['code']==2)
				$file_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
					"/assignment_{$submission['assignment']}/p{$submission['problem']}/{$submission['username']}/log";

			$data2 = array(
				'file_path'=>$file_path,
				'file_type'=>$submission['file_type'],
				'view_username'=>$submission['username'],
				'view_assignment'=>$this->assignment_model->assignment_info($submission['assignment']),
				'view_problem'=>$this->assignment_model->problem_info($submission['assignment'], $submission['problem'])
			);

			$data2['log']=FALSE;
			if($data['code']==2)
				$data2['log'] = TRUE;

			$this->load->view('templates/header',$data);
			$this->load->view('pages/view_code',$data2);
			$this->load->view('templates/footer');
		}
		else{
			die("Are you trying to see other users' codes? :)");
		}
	}

}