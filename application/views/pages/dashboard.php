<?php
/**
 * Sharif Judge online judge
 * @file dashboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'dashboard')); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo base_url('assets/images/icons/dashboard.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<p>This is your dashboard!</p>
	</div>
</div>