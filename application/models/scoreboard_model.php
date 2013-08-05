<?php
/**
 * Sharif Judge online judge
 * @file scoreboard_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Scoreboard_model extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->database();
	}

	public function get_scoreboard($assignment_id){
		$assignment = $this->assignment_model->assignment_info($assignment_id);
		$submissions = $this->db->get_where("final_submissions",array('assignment'=>$assignment_id))->result_array();
		$scoreboard = array(
			'username'=>array(),
			'score'=>array(),
			'submit_penalty'=>array()
		);
		$total_score = array();
		$penalty = array();
		$users = array();
		$finish = strtotime($assignment['finish_time']);
		$submit_penalty = $this->settings_model->get_setting('submit_penalty');
		$scores=array();
		foreach ($submissions as $submission){

			$pi = $this->assignment_model->problem_info($this->assignment['id'],$submission['problem']);

			$pre_score = ceil($submission['pre_score']*$pi['score']/10000);
			$extra_time = $assignment['extra_time'];
			$delay = strtotime($submission['time'])-$finish;
			ob_start();
			if ( eval($assignment['late_rule']) === FALSE ){
				$coefficient = "error";
				$final_score = 0;
			}
			else {
				$final_score = ceil($pre_score*$coefficient/100);
			}
			ob_end_clean();
			$delay = strtotime($submission['time'])-strtotime($assignment['start_time']);
			$scores[$submission['username']][$submission['problem']]['score'] = $final_score;
			$scores[$submission['username']][$submission['problem']]['time'] = $delay;

			if (!isset($total_score[$submission['username']]))
				$total_score[$submission['username']]=0;
			if (!isset($penalty[$submission['username']]))
				$penalty[$submission['username']]=0;

			$total_score[$submission['username']] += $final_score;
			$penalty[$submission['username']] += $delay+$submission['submit_count']*$submit_penalty;
			$users[$submission['username']]=1;
		}
		foreach($users as $user => $tmp){
			array_push($scoreboard['username'],$user);
			array_push($scoreboard['score'],$total_score[$user]);
			array_push($scoreboard['submit_penalty'],$penalty[$user]);
		}
		array_multisort(
			$scoreboard['score'],SORT_NUMERIC,SORT_DESC,
			$scoreboard['submit_penalty'],SORT_NUMERIC,SORT_ASC,
			$scoreboard['username']
		);
		return array($scores, $scoreboard);
	}
}