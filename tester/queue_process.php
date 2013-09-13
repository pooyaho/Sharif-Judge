<?php

/**
 * Sharif Judge online judge
 * @file queue_process.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 *
 * This file will be executed by php-cli
 *
 */



/* Database Connection Settings */
$db_host        = 'localhost'; // database host
$db_user        = '';          // database username
$db_pass        = '';          // database password
$db_database    = '';          // database name
$prefix         = 'shj_';      // table prefix


// Connecting to database

$db = new mysqli($db_host, $db_user, $db_pass, $db_database);
if ($db->connect_errno > 0){
	exit('Unable to connect to database [' . $db->connect_error . ']');
}


// ------------------------------------------------------------------------


function addJudgeResultToDB($sr, $type){

	global $db;
	global $prefix;

	$submit_id = $sr['submit_id'];
	$username = $sr['username'];
	$assignment = $sr['assignment'];
	$problem = $sr['problem'];
	$time = $sr['time'];
	$status = $sr['status'];
	$pre_score = $sr['pre_score'];
	$submit_count = $sr['submit_number'];
	$file_name = $sr['file_name'];
	$main_file_name = $sr['main_file_name'];
	$file_type = $sr['file_type'];

	$res = $db->query(
		"SELECT *
		FROM {$prefix}final_submissions
		WHERE username='$username' AND assignment='$assignment' AND problem='$problem'"
	);
	$r = $res->fetch_assoc();

	if ($r === NULL)
	{
		$db->query(
			"INSERT INTO {$prefix}final_submissions
			( submit_id, username, assignment, problem, time, status, pre_score, submit_count, file_name, main_file_name, file_type)
			VALUES ('$submit_id','$username','$assignment','$problem','$time','$status','$pre_score','$submit_count','$file_name','$main_file_name','$file_type') "
		);
	}
	else
	{
		$sid = $r['submit_id'];
		if ( $type === 'judge' OR ($type === 'rejudge' && $sid === $submit_id) ){
			$db->query(
				"UPDATE {$prefix}final_submissions
				SET submit_id='$submit_id', time='$time', status='$status', pre_score='$pre_score', submit_count='$submit_count', file_name='$file_name', main_file_name='$main_file_name', file_type='$file_type'
				WHERE username='$username' AND assignment='$assignment' AND problem='$problem' "
			);
		}
	}

	$db->query(
		"UPDATE {$prefix}all_submissions
		SET status='$status', pre_score='$pre_score'
		WHERE submit_id='$submit_id' AND username='$username' AND assignment='$assignment' AND problem='$problem'"
	);

}


// ------------------------------------------------------------------------


$res = $db->query("SELECT * FROM {$prefix}queue LIMIT 1");
$queue_row = $res->fetch_assoc();

if ($queue_row === NULL){ // if queue is empty
	$db->query("UPDATE {$prefix}settings SET shj_value=0 WHERE shj_key='queue_is_working'");
	exit;
}

// get ''settings'' table
$settings_res = $db->query("SELECT * FROM {$prefix}settings");
$setting = array();
while ($row = $settings_res->fetch_assoc()){
	$setting[$row['shj_key']] = $row['shj_value'];
}
$settings_res->free();

if ($setting['queue_is_working'])
	exit;

$db->query("UPDATE {$prefix}settings SET shj_value=1 WHERE shj_key='queue_is_working'");

// multiplied by 1024 to convert to bytes (from kB)
$output_size_limit = $setting['output_size_limit'] * 1024;

do {

	$qw = $db->query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='queue_is_working'")->fetch_assoc();
	if ( ! $qw['shj_value'])
		exit;

	$submit_id = $queue_row['submit_id'];
	$username = $queue_row['username'];
	$assignment = $queue_row['assignment'];
	$problem = $queue_row['problem'];
	$type = $queue_row['type'];  // $type can be 'judge' or 'rejudge'


	$res = $db->query("SELECT * FROM {$prefix}problems WHERE assignment='$assignment' AND id='$problem'");
	$srrr = $res->fetch_assoc();
	$c_time_limit = $srrr['c_time_limit']/1000;
	$java_time_limit = $srrr['java_time_limit']/1000;
	$python_time_limit = $srrr['python_time_limit']/1000;
	$memory_limit = $srrr['memory_limit'];
	$diff_cmd = $srrr['diff_cmd'];
	$diff_arg = $srrr['diff_arg'];


	$res = $db->query( // submitrow
		"SELECT *
		FROM {$prefix}all_submissions
		WHERE username='$username' AND assignment='$assignment' AND problem='$problem' AND submit_id='$submit_id'"
	);
	$sr = $res->fetch_assoc();
	$file_type = $sr['file_type'];
	$file_extension = $file_type;
	if ($file_extension === 'py2' OR $file_extension === 'py3')
		$file_extension = 'py';
	$raw_filename = $sr['file_name'];
	$main_filename = $sr['main_file_name'];

	$assignments_dir = rtrim($setting['assignments_root'], '/');
	$tester_path = rtrim($setting['tester_path'], '/');
	$problemdir = $assignments_dir."/assignment_$assignment/p$problem";
	$userdir = "$problemdir/$username";
	$the_file = "$userdir/$raw_filename.$file_extension";

	// python shield settings
	$enable_py2_shield = $setting['enable_py2_shield'];
	$enable_py3_shield = $setting['enable_py3_shield'];

	$op1 = $setting['enable_log'];
	$op2 = $setting['enable_easysandbox'];
	$op3 = 0;
	if ($file_type === 'c')
		$op3 = $setting['enable_c_shield'];
	elseif ($file_type === 'cpp')
		$op3 = $setting['enable_cpp_shield'];

	$op4 = $setting['enable_java_policy'];


	
	if ($file_type === 'c' OR $file_type === 'cpp')
		$time_limit = $c_time_limit;
	else if ($file_type === 'java')
		$time_limit = $java_time_limit;
	else if ($file_type === 'py2' OR $file_type === 'py3')
		$time_limit = $python_time_limit;
	
	$time_limit = round($time_limit, 3);

	$time_limit_int = floor($time_limit) +1;
	
	$cmd = "cd $tester_path;\n./tester.sh $problemdir $username $main_filename $raw_filename $file_type $time_limit $time_limit_int $memory_limit $output_size_limit $diff_cmd $diff_arg $op1 $op2 $op3 $op4";

	file_put_contents($userdir.'/log', $cmd);


	// adding shield to python source if shield is on for python
	if ($file_type === 'py2' && $enable_py2_shield){
		$source = file_get_contents($the_file);
		file_put_contents($the_file, file_get_contents($tester_path.'/shield/shield_py2.py').$source);
	}
	if ($file_type === 'py3' && $enable_py3_shield){
		$source = file_get_contents($the_file);
		file_put_contents($the_file, file_get_contents($tester_path.'/shield/shield_py3.py').$source);
	}


	// running tester (judging the code)
	$output = shell_exec($cmd);


	// removing shield from python source if shield is on for python
	if ($file_type === 'py2' && $enable_py2_shield)
		file_put_contents($the_file, $source);
	if ($file_type === 'py3' && $enable_py3_shield)
		file_put_contents($the_file, $source);


	// deleting the jail folder, if still exists
	shell_exec("cd $tester_path; rm -r jail*");

	$output = trim($output);

	// saving judge result
	if ( $output != -4 && $output != -5 && $output != -6 ){
		$from = $userdir."/result.html";
		$to = $userdir."/result-".($submit_id).".html";
		copy($from, $to);
	}


	$stat = 'OK';
	$score = ($output<0?0:$output);
	switch($output){
		case 0:  $stat = 'WRONG'; break;
		case -1: $stat = 'Compilation Error'; break;
		case -2: $stat = 'Syntax Error'; break;
		case -3: $stat = 'Bad System Call'; break;
		case -4: $stat = 'Invalid Special Judge'; break;
		case -5: $stat = 'File Format not Supported'; break;
		case -6: $stat = 'Judge Error'; break;
	}


	$sr['status'] = $stat;
	$sr['pre_score'] = $score;

	addJudgeResultToDB($sr, $type);


	$db->query(
		"DELETE FROM {$prefix}queue
		WHERE submit_id='$submit_id' AND username='$username' AND assignment='$assignment' AND problem='$problem'"
	);

	$res = $db->query("SELECT * FROM {$prefix}queue LIMIT 1");
	$queue_row = $res->fetch_assoc();

}while($queue_row !== NULL);

$res->free();

$db->query("UPDATE {$prefix}settings SET shj_value=0 WHERE shj_key='queue_is_working'");