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
		<p>Selected Assignment: <span class="assignment_name"><?php echo $assignment['name'] ?></span></p>
		<?php foreach($all_assignments as $item): ?>
			<div class="assignment_block" id="<?php echo $item['id'] ?>">
				<div class="c1">
					<div class="select_assignment <?php echo ($item['id']==$assignment['id']?'check checked':'check') ?> i<?php echo $item['id'] ?>" id="<?php echo $item['id'] ?>"></div>
				</div>
				<div class="assignment_item"><?php echo $item['name'] ?></div>
			</div>
		<?php endforeach ?>
	</div>
</div>