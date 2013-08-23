<?php
/**
 * Sharif Judge online judge
 * @file Server_time.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Server_time extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->driver('session');
		if ( ! $this->session->userdata('logged_in')){ // if not logged in
			exit;
		}
	}


	// ------------------------------------------------------------------------


	/*
	 * prints server time, used for server synchronization by jquery script which shows server time to users
	 */
	public function index(){
		echo date(DATE_ISO8601,shj_now());
	}
}