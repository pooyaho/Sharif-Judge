<?php
/**
 * Sharif Judge online judge
 * @file top_bar.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<div id="top_bar">
	<div id="user_menu"><?php echo $username ?> | <?php echo anchor('logout','Logout'); ?></div>
	<div id="shj_logo"><a href="<?php echo site_url('/'); ?>"><img src="<?php echo site_url('assets/images/logo_small.png'); ?>"/></a></div>
</div>