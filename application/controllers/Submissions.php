<?php
/**
 * Sharif Judge online judge
 * @file Submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Submissions extends CI_Controller
{

	var $username;
	var $assignment;
	var $user_level;
	var $final_items;

	var $filter_user;
	var $filter_problem;
	var $page_number;

	// ------------------------------------------------------------------------


	public function __construct()
	{
		parent::__construct();
		$this->load->driver('session');
		if ( ! $this->session->userdata('logged_in')) // if not logged in
			redirect('login');
		$this->load->model('submit_model');
		$this->username = $this->session->userdata('username');
		$this->assignment = $this->assignment_model->assignment_info($this->user_model->selected_assignment($this->username));
		$this->user_level = $this->user_model->get_user_level($this->username);

		$input = $this->uri->uri_to_assoc();
		$this->filter_user = $this->filter_problem = NULL;
		$this->page_number = 1;
		if (array_key_exists('user', $input) && $input['user'])
			if ($this->user_level > 0) // students are not able to filter submissions by user
				$this->filter_user = $this->form_validation->alpha_numeric($input['user'])?$input['user']:NULL;
		if (array_key_exists('problem', $input) && $input['problem'])
			$this->filter_problem = is_numeric($input['problem'])?$input['problem']:NULL;
		if (array_key_exists('page', $input) && $input['page'])
			$this->page_number = is_numeric($input['page'])?$input['page']:1;

	}


	// ------------------------------------------------------------------------


	private function _download_excel($view)
	{

		$now=date('Y-m-d H:i:s', shj_now());
		$this->load->library('excel');
		$this->excel->set_file_name('judge_'.$view.'_submissions.xls');
		$this->excel->addHeader(array('Assignment:', $this->assignment['name']));
		$this->excel->addHeader(array('Time:', $now));
		$this->excel->addHeader(NULL); //newline
		if ($this->user_level === 0)
			$row=array('Final','Problem','Submit Time','Score','Coefficient','Final Score','Language','Status','#');
		else{
			$row=array('Final','Submit ID','Username','Display Name','Problem','Submit Time','Score','Coefficient','Final Score','Language','Status','#');
			if ($view === 'final'){
				array_unshift($row, "#2");
				array_unshift($row, "#1");
			}
		}
		$this->excel->addRow($row);
		if ($view === 'final')
			$items = $this->submit_model->get_final_submissions($this->assignment['id'], $this->user_level, $this->username, NULL, $this->filter_user, $this->filter_problem);
		else
			$items = $this->submit_model->get_all_submissions($this->assignment['id'], $this->user_level, $this->username, NULL, $this->filter_user, $this->filter_problem);
		$finish = strtotime($this->assignment['finish_time']);
		$i=0; $j=0; $un='';
		foreach ($items as $item){
			$i++;
			if ($item['username'] != $un)
				$j++;
			$un = $item['username'];
			if ( ! isset($name[$item['username']]))
				$name[$item['username']] = $this->user_model->get_user($item['username'])->display_name;

			$pi = $this->assignment_model->problem_info($this->assignment['id'], $item['problem']);

			$pre_score = ceil($item['pre_score']*$pi['score']/10000);

			$checked='';
			if ($view === 'all'){
				if (isset($this->final_items[$item['username']][$item['problem']]['submit_id']))
					if ($this->final_items[$item['username']][$item['problem']]['submit_id'] == $item['submit_id'])
						$checked='*';
			}
			else
				$checked='*';

			$extra_time = $this->assignment['extra_time'];
			$delay = strtotime($item['time'])-$finish;
			ob_start();
			if ( eval($this->assignment['late_rule']) === FALSE ){
				$coefficient = 'error';
				$final_score = 0;
			}
			else
				$final_score = ceil($pre_score*$coefficient/100);
			ob_end_clean();
			if ($this->user_level === 0)
				$row = array(
					$checked,
					$item['problem'].' ('.$pi['name'].')',
					$item['time'],
					$pre_score,
					$coefficient,
					$final_score,
					filetype_to_language($item['file_type']),
					$item['status'],
					($view==='final'?$item['submit_count']:$item['submit_number'])
				);
			else {
				$row = array(
					$checked,
					$item['submit_id'],
					$item['username'],
					$name[$item['username']],
					$item['problem'].' ('.$pi['name'].')',
					$item['time'],
					$pre_score,
					$coefficient,
					$final_score,
					filetype_to_language($item['file_type']),
					$item['status'],
					($view==='final'?$item['submit_count']:$item['submit_number'])
				);
				if ($view === 'final'){
					array_unshift($row,$j);
					array_unshift($row,$i);
				}
			}
			$this->excel->addRow($row);
		}
		$this->excel->sendFile();
	}


	// ------------------------------------------------------------------------


	public function final_excel()
	{
		$this->_download_excel('final');
	}

	public function all_excel()
	{
		$final = $this->submit_model->get_final_submissions($this->assignment['id'], $this->user_level, $this->username, NULL, $this->filter_user, $this->filter_problem);
		$this->final_items = array();
		foreach ($final as $item){
			$this->final_items[$item['username']][$item['problem']] = $item;
		}
		$this->_download_excel('all');
	}


	// ------------------------------------------------------------------------


	public function the_final()
	{

		if ( ! is_numeric($this->page_number))
			show_404();

		if ($this->page_number<1)
			show_404();

		$this->load->library('pagination');
		$pagination_config = array(
			'base_url' => site_url('submissions/final'.($this->filter_user?'/user/'.$this->filter_user:'').($this->filter_problem?'/problem/'.$this->filter_problem:'').'/page'),
			'total_rows' => $this->submit_model->count_final_submissions($this->assignment['id'], $this->user_level, $this->username, $this->filter_user, $this->filter_problem),
			'per_page' => $this->settings_model->get_setting('results_per_page'),
			'use_page_numbers' => TRUE,
			'num_links' => 3,
			'full_tag_open' => '<ul class="shj_pagination">',
			'full_tag_close' => '</ul>',
			'first_tag_open' => '<li>',
			'first_tag_close' => '</li>',
			'last_tag_open' => '<li>',
			'last_tag_close' => '</li>',
			'next_tag_open' => '<li>',
			'next_tag_close' => '</li>',
			'prev_tag_open' => '<li>',
			'prev_tag_close' => '</li>',
			'cur_tag_open' => '<li class="current_page"><span>',
			'cur_tag_close' => '</span></li>',
			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>'
		);
		$this->pagination->initialize($pagination_config);

		$data = array(
			'view' => 'final',
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title' => 'Final Submissions',
			'style' => 'main.css',
			'items' => $this->submit_model->get_final_submissions($this->assignment['id'], $this->user_level, $this->username, $this->page_number, $this->filter_user, $this->filter_problem),
			'excel_link' => site_url('submissions/final_excel'.($this->filter_user?'/user/'.$this->filter_user:'').($this->filter_problem?'/problem/'.$this->filter_problem:'')),
			'filter_user' => $this->filter_user,
			'filter_problem' => $this->filter_problem
		);

		$this->load->view('templates/header', $data);
		$this->load->view('pages/submissions', $data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------


	public function all()
	{

		$final = $this->submit_model->get_final_submissions($this->assignment['id'], $this->user_level, $this->username, NULL, $this->filter_user, $this->filter_problem);
		$this->final_items = array();
		foreach ($final as $item){
			$this->final_items[$item['username']][$item['problem']] = $item;
		}

		if ( ! is_numeric($this->page_number))
			show_404();

		if ($this->page_number < 1)
			show_404();

		$this->load->library('pagination');
		$pagination_config = array(
			'base_url' => site_url('submissions/all'.($this->filter_user?'/user/'.$this->filter_user:'').($this->filter_problem?'/problem/'.$this->filter_problem:'').'/page'),
			'total_rows' => $this->submit_model->count_all_submissions($this->assignment['id'], $this->user_level, $this->username, $this->filter_user, $this->filter_problem),
			'per_page' => $this->settings_model->get_setting('results_per_page'),
			'use_page_numbers' => TRUE,
			'num_links' => 3,
			'full_tag_open' => '<ul class="shj_pagination">',
			'full_tag_close' => '</ul>',
			'first_tag_open' => '<li>',
			'first_tag_close' => '</li>',
			'last_tag_open' => '<li>',
			'last_tag_close' => '</li>',
			'next_tag_open' => '<li>',
			'next_tag_close' => '</li>',
			'prev_tag_open' => '<li>',
			'prev_tag_close' => '</li>',
			'cur_tag_open' => '<li class="current_page"><span>',
			'cur_tag_close' => '</span></li>',
			'num_tag_open' => '<li>',
			'num_tag_close' => '</li>'
		);
		$this->pagination->initialize($pagination_config);

		$data = array(
			'view' => 'all',
			'username' => $this->username,
			'user_level' => $this->user_level,
			'all_assignments' => $this->assignment_model->all_assignments(),
			'assignment' => $this->assignment,
			'title' => 'All Submissions',
			'style' => 'main.css',
			'items' => $this->submit_model->get_all_submissions($this->assignment['id'], $this->user_level, $this->username, $this->page_number, $this->filter_user, $this->filter_problem),
			'final_items' => $this->final_items,
			'excel_link' => site_url('submissions/all_excel'.($this->filter_user?'/user/'.$this->filter_user:'').($this->filter_problem?'/problem/'.$this->filter_problem:'')),
			'filter_user' => $this->filter_user,
			'filter_problem' => $this->filter_problem
		);
		$this->load->view('templates/header', $data);
		$this->load->view('pages/submissions', $data);
		$this->load->view('templates/footer');
	}


	// ------------------------------------------------------------------------

	/**
	 * Used by ajax request (for selecting final submission)
	 */
	public function select($input = FALSE)
	{
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($input !== FALSE)
			exit('error');
		if (shj_now() > strtotime($this->assignment['finish_time'])+$this->assignment['extra_time'])
			exit('shj_finished');
		$this->form_validation->set_rules('submit_id', 'Submit ID', 'integer|greater_than[0]');
		$this->form_validation->set_rules('problem', 'problem', 'integer|greater_than[0]');
		//echo $this->input->post('problem'); echo '<br>'; echo $this->input->post('submit_id');
		if ($this->form_validation->run() && $this->submit_model->set_final_submission($this->username, $this->assignment['id'], $this->input->post('problem'), $this->input->post('submit_id')))
			echo 'shj_success';
		else
			echo 'shj_failed';
	}


	// ------------------------------------------------------------------------


	/**
	 * For "view code" or "view result" or "view log"
	 */
	public function view_code($input = FALSE)
	{
		if ( ! $this->input->is_ajax_request() )
			show_404();
		if ($input !== FALSE)
			show_404();
		$this->form_validation->set_rules('code','code','integer|greater_than_equal_to[0]|less_than_equal_to[2]');
		$this->form_validation->set_rules('username','username','required|min_length[3]|max_length[20]|alpha_numeric|xss_clean');
		$this->form_validation->set_rules('assignment','assignment','integer|greater_than[0]');
		$this->form_validation->set_rules('problem','problem','integer|greater_than[0]');
		$this->form_validation->set_rules('submit_id','submit_id','integer|greater_than[0]');
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
				exit('Don\'t try to see other users\' codes. :)');

			$code = $this->input->post('code');

			if ($code==0)
				$file_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
					"/assignment_{$submission['assignment']}/p{$submission['problem']}/{$submission['username']}/result-{$submission['submit_id']}.html";
			elseif ($code==1)
				$file_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
					"/assignment_{$submission['assignment']}/p{$submission['problem']}/{$submission['username']}/{$submission['file_name']}.".filetype_to_extension($submission['file_type']);
			elseif ($code==2)
				$file_path = rtrim($this->settings_model->get_setting('assignments_root'),'/').
					"/assignment_{$submission['assignment']}/p{$submission['problem']}/{$submission['username']}/log";

			$data = array(
				'file_path'=>$file_path,
				'file_type'=>$submission['file_type'],
				'file_name'=>$submission['main_file_name'].'.'.filetype_to_extension($submission['file_type']),
				'view_username'=>$submission['username'],
				'view_assignment'=>$this->assignment_model->assignment_info($submission['assignment']),
				'view_problem'=>$this->assignment_model->problem_info($submission['assignment'], $submission['problem']),
				'code'=>$code
			);


			$data['log']=FALSE;
			if($this->input->post('code')==2)
				$data['log'] = TRUE;

			$this->load->view('pages/view_code',$data);
		}
		else{
			exit('Are you trying to see other users\' codes? :)');
		}
	}




}