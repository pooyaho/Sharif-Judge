<?php
/**
 * Sharif Judge online judge
 * @file Install.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('string');
	}


	// ------------------------------------------------------------------------


	public function _lowercase($string)
	{
		if (strtolower($string) === $string)
			return TRUE;
		return FALSE;
	}


	// ------------------------------------------------------------------------


	public function index($input = FALSE)
	{
		if ($input !== FALSE)
			show_404();
		if ($this->db->table_exists('sessions'))
			show_error('It seems that Sharif Judge has been installed already.');

		$this->form_validation->set_message('_lowercase', '%s must be lowercase.');
		$this->form_validation->set_rules('username', 'username', 'required|min_length[3]|max_length[20]|alpha_numeric|callback__lowercase');
		$this->form_validation->set_rules('email', 'email', 'required|max_length[40]|valid_email|callback__lowercase');
		$this->form_validation->set_rules('password', 'password', 'required|min_length[6]|max_length[30]|alpha_numeric');
		$this->form_validation->set_rules('password_again', 'password confirmation', 'required|matches[password]');

		$data['status'] = '';

		if ($this->form_validation->run()){

			$query = "CREATE TABLE IF NOT EXISTS  `".$this->db->dbprefix('sessions')."` (
				session_id varchar(40) DEFAULT '0' NOT NULL,
				ip_address varchar(45) DEFAULT '0' NOT NULL,
				user_agent varchar(120) NOT NULL,
				last_activity int(10) unsigned DEFAULT 0 NOT NULL,
				user_data text NOT NULL,
				PRIMARY KEY (session_id),
				KEY `last_activity_idx` (`last_activity`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('all_submissions')."` (
				`submit_id` int(11) NOT NULL,
				`username` varchar(20) NOT NULL,
				`assignment` smallint(4) NOT NULL,
				`problem` smallint(4) NOT NULL,
				`time` datetime NOT NULL,
				`status` varchar(100) NOT NULL,
				`pre_score` int(11) NOT NULL,
				`submit_number` smallint(4) NOT NULL,
				`file_name` varchar(30) NOT NULL,
				`main_file_name` varchar(30) NOT NULL,
				`file_type` varchar(6) NOT NULL,
				KEY `submit_id` (`submit_id`),
				KEY `assignment` (`assignment`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('assignments')."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(50) CHARACTER SET utf8 NOT NULL,
				`problems` smallint(4) NOT NULL,
				`total_submits` int(11) NOT NULL,
				`open` tinyint(1) NOT NULL,
				`scoreboard` tinyint(1) NOT NULL,
				`description` text CHARACTER SET utf8 NOT NULL,
				`start_time` datetime NOT NULL,
				`finish_time` datetime NOT NULL,
				`extra_time` int(11) NOT NULL,
				`late_rule` text CHARACTER SET utf8 NOT NULL,
				`participants` text CHARACTER SET utf8 NOT NULL,
				`moss_update` varchar(30) CHARACTER SET utf8 NOT NULL DEFAULT 'Never',
				PRIMARY KEY (`id`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('final_submissions')."` (
				`submit_id` int(11) NOT NULL,
				`username` varchar(20) NOT NULL,
				`assignment` smallint(4) NOT NULL,
				`problem` smallint(4) NOT NULL,
				`time` datetime NOT NULL,
				`status` varchar(100) NOT NULL,
				`pre_score` int(11) NOT NULL,
				`submit_count` smallint(4) NOT NULL,
				`file_name` varchar(30) NOT NULL,
				`main_file_name` varchar(30) NOT NULL,
				`file_type` varchar(6) NOT NULL,
				KEY `assignment` (`assignment`),
				KEY `username` (`username`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('notifications')."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`title` varchar(200) CHARACTER SET utf8 NOT NULL,
				`text` text CHARACTER SET utf8 NOT NULL,
				`time` datetime NOT NULL,
				PRIMARY KEY (`id`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('problems')."` (
				`assignment` smallint(4) NOT NULL,
				`id` smallint(4) NOT NULL,
				`name` varchar(50) CHARACTER SET utf8 NOT NULL,
				`score` int(11) NOT NULL,
				`is_upload_only` tinyint(1) NOT NULL,
				`c_time_limit` int(11) NOT NULL DEFAULT '500',
				`java_time_limit` int(11) NOT NULL DEFAULT '2000',
				`python_time_limit` int(11) NOT NULL DEFAULT '1000',
				`memory_limit` int(11) NOT NULL DEFAULT '50000',
				`allowed_languages` text CHARACTER SET utf8 NOT NULL,
				`diff_cmd` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT 'diff',
				`diff_arg` varchar(20) CHARACTER SET utf8 NOT NULL DEFAULT '-iw',
				KEY `assignment` (`assignment`),
				KEY `id` (`id`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('queue')."` (
				`submit_id` int(11) NOT NULL,
				`username` varchar(20) CHARACTER SET utf8 NOT NULL,
				`assignment` smallint(4) NOT NULL,
				`problem` smallint(4) NOT NULL,
				`type` varchar(8) NOT NULL DEFAULT 'judge'
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('settings')."` (
				`shj_key` varchar(50) CHARACTER SET utf8 NOT NULL,
				`shj_value` text CHARACTER SET utf8 NOT NULL,
				KEY `shj_key` (`shj_key`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");


			$query = "INSERT INTO `".$this->db->dbprefix('settings')."` (`shj_key`, `shj_value`) VALUES
				('timezone', 'Asia/Tehran'),
				('tester_path', '/home/shj/tester'),
				('assignments_root', '/home/shj/assignments'),
				('file_size_limit', '20'),
				('output_size_limit', '1024'),
				('queue_is_working', '0'),
				('default_late_rule', '".'/* \n * Put coefficient (from 100) in variable $coefficient.\n * You can use variables $extra_time and $delay.\n * $extra_time is the total extra time given to users\n * (in seconds) and $delay is number of seconds passed\n * from finish time (can be negative).\n *  In this example, $extra_time is 172800 (2 days):\n */\n\nif ($delay<=0)\n  // no delay\n  $coefficient = 100;\n\nelseif ($delay<=3600)\n  // delay less than 1 hour\n  $coefficient = ceil(100-((30*$delay)/3600));\n\nelseif ($delay<=86400)\n  // delay more than 1 hour and less than 1 day\n  $coefficient = 70;\n\nelseif (($delay-86400)<=3600)\n  // delay less than 1 hour in second day\n  $coefficient = ceil(70-((20*($delay-86400))/3600));\n\nelseif (($delay-86400)<=86400)\n  // delay more than 1 hour in second day\n  $coefficient = 50;\n\nelseif ($delay > $extra_time)\n  // too late\n  $coefficient = 0;'."'),
				('enable_easysandbox', '1'),
				('enable_c_shield', '1'),
				('enable_cpp_shield', '1'),
				('enable_py2_shield', '1'),
				('enable_py3_shield', '1'),
				('enable_java_policy', '1'),
				('enable_log', '1'),
				('submit_penalty', '300'),
				('enable_registration', '0'),
				('registration_code', '0'),
				('mail_from', 'shj@sharifjudge.ir'),
				('mail_from_name', 'Sharif Judge'),
				('reset_password_mail', '<p>\nSomeone requested to reset the password for account with this email address at {SITE_URL}.\n</p>\n<p>\nTo change your password, visit this link:\n</p>\n<p>\n<a href=\"{RESET_LINK}\">Reset Password</a>\n</p>\n<p>\nThe link is valid for {VALID_TIME}. If you don''t want to change your password, just ignore this email.\n</p>'),
				('add_user_mail', '<p>\nHello! You are registered in Sharif Judge at {SITE_URL} as {ROLE}.\n</p>\n<p>\nYour username: {USERNAME}\n</p>\n<p>\nYour password: {PASSWORD}\n</p>\n<p>\nYou can log in at <a href=\"{LOGIN_URL}\">{LOGIN_URL}</a>\n</p>'),
				('moss_userid', ''),
				('results_per_page', '40'),
				('week_start', '0');";
			if ( ! $this->db->simple_query($query))
				show_error("Error adding data to table 'settings'");


			$query = "CREATE TABLE IF NOT EXISTS `".$this->db->dbprefix('users')."` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`username` varchar(20) CHARACTER SET utf8 NOT NULL,
				`password` varchar(100) CHARACTER SET utf8 NOT NULL,
				`display_name` varchar(40) CHARACTER SET utf8 NOT NULL,
				`email` varchar(40) CHARACTER SET utf8 NOT NULL,
				`role` varchar(20) CHARACTER SET utf8 NOT NULL,
				`passchange_key` varchar(60) CHARACTER SET utf8 NOT NULL,
				`passchange_time` datetime NOT NULL,
				`first_login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`last_login_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`selected_assignment` smallint(4) NOT NULL DEFAULT '0',
				`dashboard_widget_positions` varchar(500) NOT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `username` (`username`)
				);";
			if ( ! $this->db->simple_query($query))
				show_error("Error creating database table");

			$this->user_model->add_user(
				$this->input->post('username'),
				$this->input->post('email'),
				$this->input->post('password'),
				'admin'
			);

			// Using a random string as encryption key
			$config_path = rtrim(APPPATH,'/').'/config/config.php';
			$config_content = file_get_contents($config_path);
			$random_key = random_string('alnum', 32);
			$res = file_put_contents($config_path, str_replace('919RgokTjymS34AhPzF76tcLjTVYMV8T', $random_key, $config_content));
			if ($res === FALSE)
				$data['key_changed'] = FALSE;
			else
				$data['key_changed'] = TRUE;

			$data['status'] = 'Installed';
		}

		$data['title'] = 'Installation';
		$data['style'] = 'main.css';

		$this->load->view('templates/header', $data);
		$this->load->view('pages/admin/install', $data);
		$this->load->view('templates/footer');

	}
}