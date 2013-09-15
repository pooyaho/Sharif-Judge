<?php
/**
 * Sharif Judge online judge
 * @file add_notification.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'notifications')); ?>

<script type='text/javascript' src="<?php echo base_url("assets/tinymce/tinymce.min.js") ?>"></script>

<script>
	$(document).ready(function(){
		tinymce.init({
			selector: 'textarea#notif_text',
			toolbar_items_size: 'small',
			relative_urls: false,
			width: 700,
			height: 200,
			resize: false,
			plugins: 'directionality emoticons textcolor link code',
			toolbar1: "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | ltr rtl",
			toolbar2: "forecolor backcolor | emoticons | link unlink anchor image media code | removeformat"
		});
	});
</script>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/notifications.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">
		<?php echo form_open('notifications/'.($notif_edit===FALSE?'add':'edit/'.$notif_edit['id'])) ?>
		<?php if ($notif_edit!==FALSE): ?>
			<input type="hidden" name="id" value="<?php echo $notif_edit['id'] ?>"/>
		<?php endif ?>
		<p class="input_p">
			<label for="title" class="tiny">Title:</label>
			<input name="title" type="text" class="sharif_input" value="<?php if ($notif_edit!==FALSE) { echo $notif_edit['title']; }?>"/>
		</p>
		<p class="input_p">
			<label for="text" class="tiny">Text:</label><br><br>
			<textarea id="notif_text" name="text"><?php if ($notif_edit!==FALSE) { echo $notif_edit['text']; }?></textarea>
		</p>
		<p class="input_p">
			<input type="submit" value="<?php echo ($notif_edit===FALSE?'Add':'Edit') ?>"" class="sharif_input"/>
		</p>
		</form>

	</div> <!-- main_content -->

</div> <!-- main_container -->