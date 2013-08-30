<?php
/**
 * Sharif Judge online judge
 * @file queue_process.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */



/* Database Connection Settings */
$db_host        = 'localhost'; // database host
$db_user        = '';          // database username
$db_pass        = '';          // database password
$db_database    = '';          // database name
$prefix         = 'shj_';      // table prefix


// Connecting to database
$conn = mysql_connect($db_host,$db_user,$db_pass);
if(!$conn){
	echo 'dberror';
	return;
}
mysql_set_charset('utf8', $conn);
mysql_select_db($db_database,$conn);





function addJudgeResultToDB($sr){
	global $prefix;
	$submit_id=$sr['submit_id'];
	$username=$sr['username'];
	$assignment=$sr['assignment'];
	$problem=$sr['problem'];
	$time=$sr['time'];
	$status=$sr['status'];
	$pre_score=$sr['pre_score'];
	$submit_count=$sr['submit_number'];
	$file_name=$sr['file_name'];
	$main_file_name=$sr['main_file_name'];
	$file_type=$sr['file_type'];

	$r = mysql_fetch_assoc(mysql_query("SELECT * FROM {$prefix}final_submissions WHERE username='$username' AND assignment='$assignment' AND problem='$problem'"));

	if($r==null){
		mysql_query("INSERT INTO {$prefix}final_submissions
					( submit_id, username, assignment, problem, time, status, pre_score, submit_count, file_name, main_file_name, file_type)
					VALUES ('$submit_id','$username','$assignment','$problem','$time','$status','$pre_score','$submit_count','$file_name','$main_file_name','$file_type') ");
	}
	else{
		mysql_query("UPDATE {$prefix}final_submissions
					SET submit_id='$submit_id', time='$time', status='$status', pre_score='$pre_score', submit_count='$submit_count', file_name='$file_name', main_file_name='$main_file_name', file_type='$file_type'
					WHERE username='$username' AND assignment='$assignment' AND problem='$problem' ");
	}


	mysql_query("UPDATE {$prefix}all_submissions
				SET status='$status', pre_score='$pre_score'
				WHERE submit_id='$submit_id' AND username='$username' AND assignment='$assignment' AND problem='$problem'");
}










sleep(rand(0,15)/10); // for avoiding collision and decreasing server load

$qr = mysql_fetch_assoc(mysql_query("SELECT * FROM {$prefix}queue LIMIT 1")); // queuerow

if($qr==null){ // if queue is empty
	mysql_query("UPDATE {$prefix}settings SET shj_value=0 WHERE shj_key='queue_is_working'");
	return;
}

$q = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='queue_is_working'"));
$q=$q['shj_value'];
//	echo $q;
if($q==1)
	return;

mysql_query("UPDATE {$prefix}settings SET shj_value=1 WHERE shj_key='queue_is_working'");

//sleep(25.4+rand(0,50)/10);   // DO WE REALLY NEED THIS LINE ?????????????????????? ANSWER: NO :)

do{

	$submit_id = $qr['submit_id'];
	$username = $qr['username'];
	$assignment = $qr['assignment'];
	$problem = $qr['problem'];

	$srrr = mysql_fetch_assoc(mysql_query("SELECT c_time_limit,java_time_limit,python_time_limit,memory_limit,diff_cmd,diff_arg FROM {$prefix}problems WHERE assignment='$assignment' AND id='$problem'"));

	$c_time_limit = $srrr['c_time_limit']/1000;
	$java_time_limit = $srrr['java_time_limit']/1000;
	$python_time_limit = $srrr['python_time_limit']/1000;
	$memory_limit = $srrr['memory_limit'];
	$diff_cmd = $srrr['diff_cmd'];
	$diff_arg = $srrr['diff_arg'];


	$sr = mysql_fetch_assoc(mysql_query("SELECT * FROM {$prefix}all_submissions WHERE username='$username' AND assignment='$assignment' AND problem='$problem' AND submit_id='$submit_id'")); // submitrow
	$file_type = $sr['file_type'];
	$file_extension = $file_type;
	if ($file_extension==='py2' || $file_extension==='py3')
		$file_extension = 'py';
	$raw_filename=$sr['file_name'];
	$main_filename=$sr['main_file_name'];

	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='assignments_root'"));
	$assignments_dir = rtrim($srrr['shj_value'],'/');
	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='tester_path'"));
	$tester_path = rtrim($srrr['shj_value'],'/');
	$problemdir = $assignments_dir."/assignment_$assignment/p$problem";
	$userdir = "$problemdir/$username";
	$the_file = "$userdir/$raw_filename.$file_extension";

	// python shield settings
	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='enable_py2_shield'"));
	$enable_py2_shield = $srrr['shj_value'];
	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='enable_py3_shield'"));
	$enable_py3_shield = $srrr['shj_value'];

	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='enable_log'"));
	$op1 = $srrr['shj_value'];
	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='enable_easysandbox'"));
	$op2 = $srrr['shj_value'];
	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='enable_c_shield'"));
	$op3 = $srrr['shj_value'];
	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='enable_java_policy'"));
	$op4 = $srrr['shj_value'];

	// compiling and judging the code (with tester.sh) :
	
	if ($file_type=="c" OR $file_type == "cpp")
		$time_limit = $c_time_limit;
	else if ($file_type=="java")
		$time_limit = $java_time_limit;
	else if ($file_type=="py2" OR $file_type=="py3")
		$time_limit = $python_time_limit;
	
	$time_limit = round($time_limit, 3);

	$time_limit_int = floor($time_limit) +1;
	
	$cmd = "cd $tester_path;\n./tester.sh $problemdir $username $main_filename $raw_filename $file_type $time_limit $time_limit_int $memory_limit $diff_cmd $diff_arg $op1 $op2 $op3 $op4";

	file_put_contents($userdir."/log",$cmd);


	// adding shield to python source if shield is on for python
	if ($file_type=='py2' && $enable_py2_shield==1){
		$source = file_get_contents($the_file);
		file_put_contents($the_file, file_get_contents($tester_path.'/shield/shield_py2.py').$source);
	}
	if ($file_type=='py3' && $enable_py3_shield==1){
		$source = file_get_contents($the_file);
		file_put_contents($the_file, file_get_contents($tester_path.'/shield/shield_py3.py').$source);
	}


	// running tester (judging the code)
	$output = shell_exec($cmd);


	// removing shield from python source if shield is on for python
	if ($file_type=='py2' && $enable_py2_shield==1)
		file_put_contents($the_file, $source);
	if ($file_type=='py3' && $enable_py3_shield==1)
		file_put_contents($the_file, $source);

	// deleting the jail folder, if still exists
	shell_exec("cd $tester_path; rm -r jail*");

	$output = trim($output);

	// saving judge result
	$from= $userdir."/result.html"; $to = $userdir."/result-".($submit_id).".html" ;
	copy($from, $to);


	$stat = "OK";
	$score = ($output<0?0:$output);
	if($output==-1) $stat = 'Compilation Error';
	else if($output==-2) $stat = 'Syntax Error';
	else if($output==-3) $stat = 'Bad System Call';
	else if($output==-4) $stat = 'Invalid Special Judge';
	else if($output==-5) $stat = 'File Format not Supported';
	else if($score==0)  $stat = 'WRONG';

	//$score = ceil($score * $problem_score / 10000) ;

	$sr['status']=$stat;
	$sr['pre_score']=$score;
	addJudgeResultToDB($sr);


	mysql_query("DELETE FROM {$prefix}queue WHERE submit_id='$submit_id' AND username='$username' AND assignment='$assignment' AND problem='$problem'");
	$qr = mysql_fetch_assoc(mysql_query("SELECT * FROM {$prefix}queue LIMIT 1"));

}while($qr!=null);

mysql_query("UPDATE {$prefix}settings SET shj_value=0 WHERE shj_key='queue_is_working'");