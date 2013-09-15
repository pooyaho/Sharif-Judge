<?php
/**
 * Sharif Judge online judge
 * @file register.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="box success">
	<div class="judge_logo">
		<a href="<?php echo site_url() ?>"><img src="<?php echo base_url("assets/images/banner.png") ?>"/></a>
	</div>
	<div class="login_form">
		<div class="login1">
			<p style="width:100%;">
				Registered successfully!
			</p>
		</div>
		<div class="login2">
			<p style="margin:0;">
				<?php echo anchor('login','Login'); ?>
			</p>
		</div>
	</div>
</div>