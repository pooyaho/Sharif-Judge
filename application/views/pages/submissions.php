<?php
/**
 * Sharif Judge online judge
 * @file all_submissions.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'final_submissions')); ?>
	<div id="main_container">
		<div id="page_title"><img src="<?php echo base_url('assets/images/icons/final_submissions.png') ?>"/> <span><?php echo $title ?></span></div>
		<div id="main_content">
			<p>Final Submissions of <?php echo $assignment['name']; ?></p>
			<table class="sharif_table" border="1">
				<thead>
					<?php if ($user_level>0): ?>
						<tr>
							<th width="3%" rowspan="2">#1</th>
							<th width="3%" rowspan="2">#2</th>
							<th width="7%" rowspan="2">Username</th>
							<th width="15%" rowspan="2">Display Name</th>
							<th width="6%" rowspan="2">Problem</th>
							<th width="15%" rowspan="2">Submit Time</th>
							<th width="6%" colspan="3">Score</th>
							<th width="12%" rowspan="2">Status</th>
							<th width="13%" rowspan="2">Code</th>
							<th width="5%" rowspan="2">#</th>
						</tr>
						<tr>
							<th width="6%" class="score">Score</th>
							<th width="6%" class="score">Coefficient</th>
							<th width="6%" class="score">Final Score</th>
						</tr>
					<?php else: ?>
						<tr>
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
					<?php if ($user_level>0): ?>
						<td>1</td>
						<td>2</td>
						<td><?php echo $item['username'] ?></td>
						<td><?php
							if(!isset($name[$item['username']]))
								$name[$item['username']]=$this->user_model->get_user($item['username'])->display_name;
							echo $name[$item['username']];
						?></td>
					<?php endif ?>
						<td><?php echo $item['problem'] ?></td>
						<td><?php echo $item['time'] ?></td>
						<td><?php echo $item['pre_score'] ?></td>
						<td>ToDo</td>
						<td>ToDo</td>
						<td><?php echo $item['status'] ?></td>
						<td>ToDo</td>
						<td><?php echo $item['submit_count'] ?></td>
					</tr>
				<?php endforeach ?>
			</table>
		</div>
	</div>