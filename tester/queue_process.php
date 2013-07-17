<?php
/**
 * Sharif Judge online judge
 * @file queue_process.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */



/* Database config */
$prefix			= 'shj_'; // table prefix
$db_host		= 'localhost';
$db_user		= 'shj';
$db_pass		= '123';
$db_database	= 'shj';


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
	$late_time=$sr['late_time'];
	$status=$sr['status'];
	$pre_score=$sr['pre_score'];
	$submit_count=$sr['submit_number'];
	$file_name=$sr['file_name'];
	$main_file_name=$sr['main_file_name'];
	$file_type=$sr['file_type'];

	$r = mysql_fetch_assoc(mysql_query("SELECT * FROM {$prefix}final_submissions WHERE username='$username' AND assignment='$assignment' AND problem='$problem'"));

	if($r==null){
		mysql_query("INSERT INTO {$prefix}final_submissions ( submit_id, username, assignment, problem, time, late_time, status, pre_score, submit_count, file_name, main_file_name, file_type)
		VALUES ('$submit_id','$username','$assignment','$problem','$time','$late_time','$status','$pre_score','$submit_count','$file_name','$main_file_name','$file_type') ");
	}
	else{
		mysql_query("UPDATE {$prefix}final_submissions SET submit_id='$submit_id', time='$time', late_time='$late_time', status='$status', pre_score='$pre_score', submit_count='$submit_count', file_name='$file_name', main_file_name='$main_file_name', file_type='$file_type' WHERE username='$username' AND assignment='$assignment' AND problem='$problem' ");
	}


	mysql_query("UPDATE {$prefix}all_submissions SET status='$status', pre_score='$pre_score' WHERE submit_id='$submit_id' AND username='$username' AND assignment='$assignment' AND problem='$problem'");
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

sleep(25.4+rand(0,50)/10);   // DO WE REALLY NEED THIS LINE ?????????????????????? ANSWER: NO :)

do{

	$submit_id = $qr['submit_id'];
	$username = $qr['username'];
	$assignment = $qr['assignment'];
	$problem = $qr['problem'];

	$srrr = mysql_fetch_assoc(mysql_query("SELECT score,time_limit,memory_limit FROM {$prefix}problems WHERE assignment='$assignment' AND id='$problem'"));
	$problem_score = $srrr['score'];
	$time_limit = $srrr['time_limit']/1000;
	$memory_limit = $srrr['memory_limit'];


	$sr = mysql_fetch_assoc(mysql_query("SELECT * FROM {$prefix}all_submissions WHERE username='$username' AND assignment='$assignment' AND problem='$problem' AND submit_id='$submit_id'")); // submitrow
	$file_type = $sr['file_type'];
	$raw_filename=$sr['file_name'];
	$main_filename=$sr['main_file_name'];

	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='assignments_root'"));
	$assignments_dir = rtrim($srrr['shj_value'],'/');
	$srrr = mysql_fetch_assoc(mysql_query("SELECT shj_value FROM {$prefix}settings WHERE shj_key='tester_path'"));
	$tester_path = rtrim($srrr['shj_value'],'/');
	$problemdir = $assignments_dir."/assignment_$assignment/p$problem";
	$userdir = "$problemdir/$username";
	$the_file = "$userdir/$raw_filename.$file_type";


	// compiling and judging the code (with tester.sh) :
	$output="";
	$ret="";

	$cmd = " cd $tester_path; ./tester.sh $problemdir $username $main_filename $raw_filename $file_type $time_limit $memory_limit diff -iw"; /* todo */

	exec($cmd,$output, $ret);

	$output=$output[0];

	/*$score = shell_exec("cd $problemdir  && " .( __DIR__ ). "/tester/tester.sh $problemdir $username $raw_filename $file_type 1 50000 10 -bB");*/
	/*$score = trim($score);*/

	$score = trim($output);

	// saving judge result
	$from= $userdir."/result.html"; $to = $userdir."/result-".($submit_id).".html" ;
	copy($from, $to);


	$stat = "OK";
	if($ret!=0) $score=0;
	if($ret==1) $stat = 'Compilation Error';
	else if($ret==2) $stat = 'Syntax Error';
	else if($ret==3) $stat = 'Bad System Call';
	else if($ret==4) $stat = 'Invalid Special Judge';
	else if($ret==5) $stat = 'File Format not Supported';
	else if($score==0)  $stat = 'WRONG';

	$score = ceil($score * $problem_score / 10000) ;

	$sr['status']=$stat;
	$sr['pre_score']=$score;
	addJudgeResultToDB($sr);


	mysql_query("DELETE FROM {$prefix}queue WHERE submit_id='$submit_id' AND username='$username' AND assignment='$assignment' AND problem='$problem'");
	$qr = mysql_fetch_assoc(mysql_query("SELECT * FROM {$prefix}queue LIMIT 1"));

}while($qr!=null);

mysql_query("UPDATE {$prefix}settings SET shj_value=0 WHERE shj_key='queue_is_working'");

//exec("php ".( __DIR__ )."/queue_process.php >/dev/null 2>/dev/null &"); // DO WE REALLY NEED THIS LINE ?????????? I think we don't...