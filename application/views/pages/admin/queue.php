<?php
/**
 * Sharif Judge online judge
 * @file queue.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'')); ?>

<script>
	$(document).ready(function(){
		$(".shj_act").click(function(){
			var action=$(this).attr('id');
			$.post(
				'<?php echo site_url("queue") ?>/'+action,
				{<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'},
				function(data){
					if (data=='success')
						location.reload();
				}
			);
		});
	});
</script>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/queue.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">
		<p>
			<?php if ($working): ?>
				<i class="splashy-media_controls_dark_play"></i> Queue is working
			<?php else: ?>
				<i class="splashy-media_controls_dark_pause"></i> Queue is not working
			<?php endif ?>
			| Total submissions in queue: <?php echo count($queue) ?>
		</p>
		<p>
			<a href="#" class="shj_act" id="pause"><i class="splashy-media_controls_pause_small"></i> Pause</a> |
			<a href="#" class="shj_act" id="resume"><i class="splashy-media_controls_play_small"></i> Resume</a> |
			<a href="#" class="shj_act" id="empty_queue"><i class="splashy-close"></i> Empty Queue</a>
		</p>
		<table class="sharif_table">
			<thead>
			<tr>
				<th>#</th>
				<th>Submit ID</th>
				<th>Usename</th>
				<th>Assignment</th>
				<th>Problem</th>
				<th>Type (judge/rejudge)</th>
			</tr>
			</thead>
			<?php
			foreach ($all_assignments as $item){
				$assignment[$item['id']]=$item;
			}
			$i=0;
			?>
			<?php foreach ($queue as $item): ?>
				<tr>
					<td><?php echo ++$i ?></td>
					<td><?php echo $item['submit_id'] ?></td>
					<td><?php echo $item['username'] ?></td>
					<td><?php echo $item['assignment'] ?> (<?php echo $assignment[$item['assignment']]['name'] ?>)</td>
					<td><?php echo $item['problem'] ?></td>
					<td><?php echo $item['type'] ?></td>
				</tr>
			<?php endforeach ?>
		</table>

	</div> <!-- main_content -->

</div> <!-- main_container -->