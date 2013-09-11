<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file settings_model.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

/**
 * This model deals with global settings
 */

class Settings_model extends CI_Model {


	public function __construct() {
		parent::__construct();
		$this->load->database();
	}


	// ------------------------------------------------------------------------


	public function get_setting($key){
		return $this->db->get_where('settings', array('shj_key'=>$key))->row()->shj_value;
	}


	// ------------------------------------------------------------------------


	public function set_setting($key,$value){
		$this->db->where('shj_key', $key)->update('settings', array('shj_value'=>$value));
	}


}