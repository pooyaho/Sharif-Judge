<?php
/**
 * Sharif Judge online judge
 * @file scoreboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'scoreboard')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/scoreboard.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">
		<?php if ($assignment['id']===0): ?>
		<p>No assignment is selected.</p>
		<?php elseif (!isset($scoreboard)): ?>
		<p>Scoreboard is disabled.</p>
		<?php else: ?>
			<p>Scoreboard of <?php echo $assignment['name'] ?></p>
			<?php //print_r($scoreboard) ?>
			<table class="sharif_table">
				<thead>
				<tr>
					<th>#</th><th>Username</th><th>Name</th>
					<?php foreach ($problems as $problem): ?>
						<th><?php echo "Problem ".$problem['id']."<br>(".$problem['name'].")" ?></th>
					<?php endforeach ?>
					<th>Total</th>
				</tr>
				</thead>
				<?php $i=0; ?>
				<?php foreach ($scoreboard['username'] as $sc_username): ?>
					<tr>
						<td><?php echo ($i+1) ?></td>
						<td><?php echo $sc_username ?></td>
						<td><?php
							if(!isset($name[$sc_username]))
								$name[$sc_username]=$this->user_model->get_user($sc_username)->display_name;
							echo $name[$sc_username];
						?></td>
						<?php foreach($problems as $problem): ?>
							<td>
								<?php if (isset($scores[$sc_username][$problem['id']]['score'])): ?>
										<?php echo $scores[$sc_username][$problem['id']]['score']; ?>
										<br>
										<span class="scoreboard_hours" title="time"><?php echo floor($scores[$sc_username][$problem['id']]['time']/360)/10 ?> hours</span>
								<?php else: ?>
										-
								<?php endif ?>
							</td>
						<?php endforeach ?>
						<td>
							<span style="font-weight: bold;"><?php echo $scoreboard['score'][$i] ?></span>
							<br>
							<span class="scoreboard_hours" title="total time + submit penalty"><?php echo floor($scoreboard['submit_penalty'][$i]/360)/10 ?> hours</span>
						</td>
					</tr>
					<?php $i++ ?>
				<?php endforeach ?>
			</table>
		<?php endif ?>

	</div> <!-- main_content -->

</div> <!-- main_container -->