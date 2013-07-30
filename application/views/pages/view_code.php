<?php
/**
 * Sharif Judge online judge
 * @file view_code.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<script type="text/javascript" src="<?php echo base_url("assets/jquery-syntax/jquery.syntax.min.js") ?>"></script>
<script type="text/javascript">
	// This function is executed when the page has finished loading.
	jQuery(function($) {
		// This function highlights (by default) pre and code tags which are annotated correctly.
		$.syntax({
			blockLayout: 'fixed',
			theme: 'paper'
		});
	});
</script>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>"")); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo base_url('assets/images/icons/code.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<?php
			if($file_type=="py")
				$file_type="python";
		?>
		<pre><?php
			echo "Username: $view_username\n";
			echo "Assignment {$view_assignment['id']} ({$assignment['name']})\n";
			echo "Problem {$view_problem['id']} ({$view_problem['name']})\n";
			echo "File Type: $file_type";
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
				Please note:<br>
				This is the log file for the last submission of user "<?php echo $view_username ?>" for problem <?php echo "{$view_problem['id']} ({$view_problem['name']})" ?>.<br>
				This may be different from the final submission selected by "<?php echo $view_username ?>".
			<?php endif ?>
			<pre class="shj_code"><?php
				if (file_exists($file_path))
					echo file_get_contents($file_path);
				else
					echo "File not found";
			?></pre>
		<?php endif ?>
	</div>
</div>