<?php
/**
 * Sharif Judge online judge
 * @file all_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<script>
	$(document).ready(function(){
		$(".set_final").click(
			function(){
				var submit_id = $(this).attr('submit_id');
				var problem = $(this).attr('problem');
				$.ajax({
					type: 'POST',
					url: '<?php echo site_url('submissions/select') ?>',
					data: {submit_id:submit_id,
							problem: problem
					},
					timeout: 1000,
					success: function(a) {
						//if (a != "shj_failed"){
						if (a == "shj_success"){
							$(".set_final.p"+problem).removeClass('checked');
							$("#sf"+submit_id+"_"+problem).addClass('checked');
						}
					}
				});
			}
		);
	});
</script>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>"{$view}_submissions")); ?>
	<div id="main_container">
		<div id="page_title"><img src="<?php echo base_url("assets/images/icons/{$view}_submissions.png") ?>"/> <span><?php echo $title ?></span></div>
		<div id="main_content">
			<p><?php echo ucfirst($view); ?> Submissions of <?php echo $assignment['name']; ?> (<?php echo anchor("submissions/{$view}/excel",'Excel'); ?>)</p>
			<table class="sharif_table">
				<thead>
					<tr>
					<?php if ($view=='all'): ?>
						<th width="5%" rowspan="2">Final</th>
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
							<th width="6%" rowspan="2">Problem</th>
							<th width="14%" rowspan="2">Submit Time</th>
							<th width="6%" colspan="3">Score</th>
							<th width="12%" rowspan="2">Status</th>
							<th width="12%" rowspan="2">Code</th>
							<th width="5%" rowspan="2">#</th>
						</tr>
						<tr>
							<th width="6%" class="score">Score</th>
							<th width="6%" class="score">Coefficient</th>
							<th width="6%" class="score">Final Score</th>
						</tr>
					<?php else: ?>
							<th width="10%" rowspan="2">Problem</th>
							<th width="30%" rowspan="2">Submit Time</th>
							<th width="7%" colspan="3">Score</th>
							<th width="30%" rowspan="2">Status</th>
							<th width="15%" rowspan="2">Code</th>
							<th width="5%" rowspan="2">#</th>
						</tr>
						<tr>
							<th width="7%" class="score">Score</th>
							<th width="7%" class="score">Coefficient</th>
							<th width="7%" class="score">Final Score</th>
						</tr>
					<?php endif ?>
				</thead>
				<?php foreach ($items as $item): ?>
					<tr>
					<?php if ($view=='all'): ?>
						<td>
						<?php if($item['username']==$username): ?>
						<?php
							$checked='';
							if (isset($final_items[$item['username']][$item['problem']]['submit_id']))
								if ($final_items[$item['username']][$item['problem']]['submit_id'] == $item['submit_id'])
									$checked='checked';
						?>
						<div submit_id="<?php echo $item['submit_id'] ?>" problem="<?php echo $item['problem'] ?>" class="set_final check p<?php echo $item['problem'] ?> <?php echo $checked ?>" id="<?php echo "sf".$item['submit_id']."_".$item['problem'] ?>"></div>
						<?php endif ?>
						</td>
					<?php endif ?>
					<?php if ($user_level>0): ?>
						<?php if ($view=='all'): ?>
							<td><?php echo $item['submit_id'] ?></td>
						<?php else: ?>
							<td>1</td>
							<td>2</td>
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
							echo $item['problem']." (".$pi['name'].")";
						?></td>
						<td><?php echo $item['time'] ?></td>
						<td><?php echo $item['pre_score'] ?></td>
						<td>ToDo</td>
						<td>ToDo</td>
						<td>
							<?php if (substr($item['status'],0,8)=="Uploaded"): ?>
								<?php echo $item['status'] ?>
							<?php else: ?>
								<?php echo form_open('submissions/view_code') ?>
								<input type="hidden" name="code" value="0"/>
								<input type="hidden" name="username" value="<?php echo $item['username'] ?>"/>
								<input type="hidden" name="assignment" value="<?php echo $item['assignment'] ?>"/>
								<input type="hidden" name="problem" value="<?php echo $item['problem'] ?>"/>
								<input type="hidden" name="submit_id" value="<?php echo $item['submit_id'] ?>"/>
								<input type="submit" class="btn <?php echo strtolower($item['status']) ?>" value="<?php echo $item['status'] ?>"/>
								</form>
							<?php endif ?>
						</td>
						<td>
							<?php if ($item['file_type']=="zip"): ?>
								---
							<?php else: ?>
								<?php echo form_open('submissions/view_code') ?>
									<input type="hidden" name="code" value="1"/>
									<input type="hidden" name="username" value="<?php echo $item['username'] ?>"/>
									<input type="hidden" name="assignment" value="<?php echo $item['assignment'] ?>"/>
									<input type="hidden" name="problem" value="<?php echo $item['problem'] ?>"/>
									<input type="hidden" name="submit_id" value="<?php echo $item['submit_id'] ?>"/>
									<input type="submit" class="btn view_code" value="View Code"/>
								</form>
							<?php endif ?>
						</td>
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
		</div>
	</div>