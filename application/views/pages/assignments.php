<?php
/**
 * Sharif Judge online judge
 * @file assignments.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo base_url('assets/images/icons/assignments.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<!--<p>Selected Assignment: <span class="assignment_name"><?php echo $assignment['name'] ?></span></p>-->
		<?php foreach($all_assignments as $item): ?>
			<div class="assignment_block" id="<?php echo $item['id'] ?>">
				<div class="c1">
					<div class="select_assignment <?php echo ($item['id']==$assignment['id']?'check checked':'check') ?> i<?php echo $item['id'] ?>" id="<?php echo $item['id'] ?>"></div>
				</div>
				<div class="assignment_item">
					<div class="assignment_subitem"><?php echo $item['name'] ?></div>
					<div class="assignment_subitem"><?php echo $item['problems'] ?> problems</div>
					<div class="assignment_subitem"><?php echo $item['total_submits'] ?> submits</div>
					<div class="assignment_subitem"><?php
						$extra_time = $item['extra_time'];
						$delay = shj_now()-strtotime($item['finish_time']);;
						ob_start();
						if ( eval($item['late_rule']) === FALSE )
							$coefficient = "error";
						if (!isset($coefficient))
							$coefficient = "error";
						ob_end_clean();
						if ($delay>$item['extra_time'])
							echo '<span style="color: red;">Finished</span>';
						else
							echo $coefficient." %";?>
					</div>
					<div class="assignment_subitem"><?php echo date("Y-m-d H:i",strtotime($item['start_time']))." ---  ".date("Y-m-d H:i",strtotime($item['finish_time'])) ?></div>
					<div class="assignment_subitem">
					<?php if($item['open']): ?>
						<span style="color: green;">Open</span>
					<?php else: ?>
						<span style="color: red;">Close</span>
					<?php endif ?>
					</div>
					<?php if ($user_level>=2): ?>
					<div class="assignment_subitem"><?php echo anchor('assignments/edit/'.$item['id'],'Edit') ?></div>
					<?php endif ?>
				</div>
			</div>
		<?php endforeach ?>
	</div>
</div>