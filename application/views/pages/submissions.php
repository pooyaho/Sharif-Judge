<?php
/**
 * Sharif Judge online judge
 * @file all_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript" src="<?php echo base_url("assets/jquery-syntax/jquery.syntax.min.js") ?>"></script>

<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/reveal/reveal.css") ?>'/>
<script type='text/javascript' src="<?php echo base_url("assets/reveal/jquery.reveal.js") ?>"></script>

<script>
	$(document).ready(function(){
		$(".btn").click(function(){
			button = $(this);
			row = button.parents('tr');
			var view_code_request = $.post(
				'<?php echo site_url('submissions/view_code') ?>',
				{
					code: button.attr('code'),
					username: row.attr('u'),
					assignment: row.attr('a'),
					problem: row.attr('p'),
					submit_id: row.attr('s'),
					<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
				},
				function(data){
					$(".modal_inside").html(data);
					$.syntax({
						blockLayout: 'fixed',
						theme: 'paper'
					});
				}
			);
			$('#shj_modal').reveal(
				{
					on_close_modal: function(){
						$(".modal_inside").html('<div style="text-align: center;">Loading<br><img src="<?php echo base_url('assets/images/loading.gif') ?>"/></div>');
						view_code_request.abort();
					}
				}
			);

		});
		$(".set_final").click(
			function(){
				var submit_id = $(this).attr('submit_id');
				var problem = $(this).attr('problem');
				$.post(
					'<?php echo site_url('submissions/select') ?>',
					{
						submit_id:submit_id,
						problem: problem,
						<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
					},
					function(a) {
						//if (a != "shj_failed"){
						if (a == "shj_success"){
							$(".set_final.p"+problem).removeClass('checked');
							$(".set_final#sf"+submit_id+"_"+problem).addClass('checked');
						}
						else if (a == "shj_finished" ){
							alert("This assignment is finished. You cannot change your final submissions.");
						}
					}
				);
			}
		);
	});
</script>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>"{$view}_submissions")); ?>
<?php
$finish = strtotime($assignment['finish_time']);
?>
<div id="main_container">
	<div id="page_title">
		<img src="<?php echo base_url("assets/images/icons/{$view}_submissions.png") ?>"/>
		<span><?php echo $title ?></span>
		<span class="title_menu_item">
			<a href="<?php echo site_url("submissions/{$view}/excel") ?>"><i class="splashy-document_small_download"></i> Excel</a>
		</span>
	</div>
	<div id="main_content">
		<p><?php echo ucfirst($view); ?> Submissions of <?php echo $assignment['name']; ?></p>
		<?php if ($view == "all"): ?>
		<p>You cannot change your final submissions when assignment finishes.</p>
		<?php endif ?>
		<table class="sharif_table">
			<thead>
				<tr>
				<?php if ($view=='all'): ?>
					<th width="1%" rowspan="2">Final</th>
				<?php endif ?>
				<?php if ($user_level>0): ?>
						<?php if ($view=='all'): ?>
						<th width="5%" rowspan="2">submit ID</th>
						<?php else: ?>
						<th width="3%" rowspan="2">#1</th>
						<th width="3%" rowspan="2">#2</th>
						<?php endif ?>
						<th width="6%" rowspan="2">Username</th>
						<th width="14%" rowspan="2">Display Name</th>
						<th width="10%" rowspan="2">Problem</th>
						<th width="14%" rowspan="2">Submit Time</th>
						<th colspan="3">Score</th>
						<th width="1%" rowspan="2">Language</th>
						<th width="6%" rowspan="2">Status</th>
						<th width="6%" rowspan="2">Code</th>
						<?php if ($view=="final"): ?>
						<th width="6%" rowspan="2">Log</th>
						<?php endif ?>
						<th width="1%" rowspan="2">#</th>
					</tr>
					<tr>
						<th width="5%" class="score">Score</th>
						<th width="5%" class="score">%</th>
						<th width="5%" class="score">Final Score</th>
					</tr>
				<?php else: ?>
						<th width="10%" rowspan="2">Problem</th>
						<th width="30%" rowspan="2">Submit Time</th>
						<th width="7%" colspan="3">Score</th>
						<th width="1%" rowspan="2">Language</th>
						<th width="30%" rowspan="2">Status</th>
						<th width="15%" rowspan="2">Code</th>
						<th width="5%" rowspan="2">#</th>
					</tr>
					<tr>
						<th width="7%" class="score">Score</th>
						<th width="7%" class="score">%</th>
						<th width="7%" class="score">Final Score</th>
					</tr>
				<?php endif ?>
			</thead>
			<?php $i=0; $j=0; $un=''; ?>
			<?php foreach ($items as $item): ?>
				<?php
				$i++;
				if ($item['username']!=$un)
					$j++;
				$un = $item['username'];
				?>
				<tr u="<?php echo $item['username'] ?>" a="<?php echo $item['assignment'] ?>" p="<?php echo $item['problem'] ?>" s="<?php echo $item['submit_id'] ?>" <?php if ($view=='final' && $j%2==0){ echo 'class="hl"';} ?>>
				<?php if ($view=='all'): ?>
					<td>
					<?php //if($item['username']==$username): ?>
					<?php
						$checked='';
						if (isset($final_items[$item['username']][$item['problem']]['submit_id']))
							if ($final_items[$item['username']][$item['problem']]['submit_id'] == $item['submit_id'])
								$checked='checked';
					?>
					<div title="Set as Final Submission" submit_id="<?php echo $item['submit_id'] ?>" problem="<?php echo $item['problem'] ?>" class="<?php if ($item['username']==$username) echo 'set_final' ?> check p<?php echo $item['problem'] ?> <?php echo $checked ?>" id="<?php echo "sf".$item['submit_id']."_".$item['problem'] ?>"></div>
					<?php //endif ?>
					</td>
				<?php endif ?>
				<?php if ($user_level>0): ?>
					<?php if ($view=='all'): ?>
						<td><?php echo $item['submit_id'] ?></td>
					<?php else: ?>
						<td><?php echo $i; ?></td>
						<td><?php echo $j; ?></td>
					<?php endif ?>

					<td><?php echo $item['username'] ?></td>
					<td><?php
						if(!isset($name[$item['username']]))
							$name[$item['username']]=$this->user_model->get_user($item['username'])->display_name;
						echo $name[$item['username']];
					?></td>
				<?php endif ?>
					<td><?php
						$pi = $this->assignment_model->problem_info($assignment['id'],$item['problem']);
						echo '<span dir>'.$pi['name'].'</span> <span>('.$item['problem'].')</span>';
					?></td>
					<td><?php echo $item['time'] ?></td>
					<td><?php
						$pre_score = ceil($item['pre_score']*$pi['score']/10000);
						echo $pre_score;
					?></td>
					<td><?php
						$extra_time = $assignment['extra_time'];
						$delay = strtotime($item['time'])-$finish;
						ob_start();
						if ( eval($assignment['late_rule']) === FALSE ){
							$coefficient = 'error';
							$final_score = 0;
						}
						else {
							$final_score = ceil($pre_score*$coefficient/100);
						}
						if (!isset($coefficient))
							$coefficient = 'error';
						ob_end_clean();
						echo $coefficient;
					?></td>
					<td style="font-weight: bold;"><?php echo $final_score ?> </td>
					<td>
						<?php echo filetype_to_language($item['file_type']) ?>
					</td>
					<td>
						<?php if (substr($item['status'],0,8) == 'Uploaded'): ?>
							<?php echo $item['status'] ?>
						<?php else: ?>
							<div class="btn <?php echo strtolower($item['status']) ?>" code="0" ><?php echo $item['status'] ?></div>
						<?php endif ?>
					</td>
					<td>
						<?php if ($item['file_type']=="zip"): ?>
							---
						<?php else: ?>
							<div class="btn view_code" code="1" >Code</div>
						<?php endif ?>
					</td>
					<?php if($view=="final" && $user_level>0): ?>
						<td>
							<?php if ($item['file_type']=="zip"): ?>
								---
							<?php else: ?>
								<div class="btn" code="2" >Log</div>
							<?php endif ?>
						</td>
					<?php endif ?>
					<td><?php
						if ($view=="final")
							echo $item['submit_count'];
						else
							echo $item['submit_number'];
						?>
					</td>
				</tr>
			<?php endforeach ?>
		</table>
		<p>
		<?php echo $this->pagination->create_links(); ?>
		</p>
	</div>
</div>

<div id="shj_modal" class="reveal-modal xlarge">
	<div class="modal_inside">
		<div style="text-align: center;">Loading<br><img src="<?php echo base_url('assets/images/loading.gif') ?>"/></div>
	</div>
	<a class="close-reveal-modal">&#215;</a>
</div>