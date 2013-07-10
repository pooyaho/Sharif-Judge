<?php
/**
 * Sharif Judge online judge
 * @file settings_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Settings_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		$this->load->database();
	}
	public function get_timezone(){
		return $this->db->get_where('settings',array('key'=>'timezone'))->row()->value;
	}
	public function set_timezone($tz){
		$this->db->where('key','timezone')->update('settings',array('value'=>$tz));
	}
}