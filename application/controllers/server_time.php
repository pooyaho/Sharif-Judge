<?php
/**
 * Sharif Judge online judge
 * @file time.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

class Server_time extends CI_Controller {
	public function index(){
		echo standard_date('DATE_ISO8601',shj_now());
	}
}