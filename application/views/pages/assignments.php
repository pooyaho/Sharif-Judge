<?php
/**
 * Sharif Judge online judge
 * @file assignments.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/assignments.png') ?>"/>
		<span><?php echo $title ?></span>
		<?php if ($user_level>=2): ?>
		<span class="title_menu_item"><a href="<?php echo site_url('assignments/add') ?>"><i class="splashy-add_small"></i> Add</a></span>
		<?php endif ?>
	</div>

	<div id="main_content">

		<?php foreach ($success_messages as $success_message): ?>
			<p class="shj_ok"><?php echo $success_message ?></p>
		<?php endforeach ?>
		<?php foreach ($error_messages as $error_message): ?>
			<p class="shj_error"><?php echo $error_message ?></p>
		<?php endforeach ?>

		<?php if (count($all_assignments)==0): ?>
			<p style="text-align: center;">Nothing to show...</p>
		<?php endif ?>
		<?php foreach($all_assignments as $item): ?>
			<div class="assignment_block" id="<?php echo $item['id'] ?>">
				<div class="c1">
					<div class="select_assignment <?php echo ($item['id']==$assignment['id']?'check checked':'check') ?> i<?php echo $item['id'] ?>" id="<?php echo $item['id'] ?>"></div>
				</div>
				<div class="assignment_item">
					<div class="assignment_subitem"><?php echo $item['name'] ?></div>
					<div class="assignment_subitem"><?php echo $item['problems'] ?> problem<?php echo $item['problems']!=1?'s':'' ?></div>
					<div class="assignment_subitem"><?php echo $item['total_submits'] ?> submit<?php echo $item['total_submits']!=1?'s':'' ?></div>
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
						<div class="assignment_subitem_zero"><a href="<?php echo site_url('assignments/downloadtests/'.$item['id']) ?>"><i title="Download Tests" class="splashy-folder_classic_down"></i></a></div>
					<?php endif ?>
					<?php if ($user_level>=1): ?>
						<div class="assignment_subitem_zero"><a href="<?php echo site_url('assignments/download/'.$item['id']) ?>"><i title="Download Final Codes" class="splashy-download"></i></a></div>
					<?php endif ?>
					<?php if ($user_level>=2): ?>
						<div class="assignment_subitem_zero"><a href="<?php echo site_url('moss/'.$item['id']) ?>"><i title="Detect Similar Codes" class="splashy-shield"></i></a></div>
					<?php endif ?>
					<?php if ($user_level>=2): ?>
						<div class="assignment_subitem_zero"><a href="<?php echo site_url('assignments/edit/'.$item['id']) ?>"><i title="Edit" class="splashy-pencil"></i></a></div>
					<?php endif ?>
					<?php if ($user_level>=2): ?>
						<div class="assignment_subitem_zero"><a href="<?php echo site_url('assignments/delete/'.$item['id']) ?>"><i title="Delete" class="splashy-remove_outline"></i></a></div>
					<?php endif ?>
				</div>
				</div>
		<?php endforeach ?>

	</div> <!-- main_content -->

</div> <!-- main_container -->