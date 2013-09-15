<?php
/**
 * Sharif Judge online judge
 * @file notifications.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'notifications')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/notifications.png') ?>"/>
		<span><?php echo $title ?></span>
		<?php if ($user_level>=2): ?>
		<span class="title_menu_item"><a href="<?php echo site_url('notifications/add') ?>"><i class="splashy-add_small"></i> Add</a></span>
		<?php endif ?>
	</div>

	<div id="main_content">
		<?php $this->view('pages/list_notifications',array('type'=>'all')) ?>
	</div>

</div>