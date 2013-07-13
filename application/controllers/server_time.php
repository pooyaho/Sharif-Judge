<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Sharif Judge online judge
 * @file time.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Server_time extends CI_Controller {
	/*
	 * prints server time, used for server synchronization by jquery script which shows server time to users
	 */
	public function index(){
		echo standard_date('DATE_ISO8601',shj_now());
	}
}