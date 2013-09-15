<?php
/**
 * Sharif Judge online judge
 * @file view_code.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<pre><?php
	echo "$file_name  |  User: $view_username  |  Assignment {$view_assignment['id']} ({$view_assignment['name']})  |  Problem {$view_problem['id']} ({$view_problem['name']})";
	if($file_type==='py2' || $file_type==='py3')
		$file_type='python';
?></pre>

<?php if ($code==1): ?>
	<pre class="syntax <?php echo $file_type ?>"><?php
		if (file_exists($file_path))
			echo htmlspecialchars(file_get_contents($file_path));
		else
			echo "File not found";
	?></pre>
<?php else: ?>
	<?php if ($log): ?>
		<span class="shj_error">Please note:</span><br>
		This is the log file for the <b>last judged submission</b> of user "<?php echo $view_username ?>" for problem <?php echo "{$view_problem['id']} ({$view_problem['name']})" ?>.<br>
		This may be different from the final submission selected by "<?php echo $view_username ?>".
	<?php endif ?>
	<pre class="shj_code"><?php
		if (file_exists($file_path))
			echo file_get_contents($file_path);
		else
			echo "File not found";
	?></pre>
<?php endif ?>