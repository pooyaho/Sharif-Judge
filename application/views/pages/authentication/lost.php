<?php
/**
 * Sharif Judge online judge
 * @file login.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<?php echo form_open('login/lost') ?>
<form method="post" action="">
	<div class="box login">
		<div class="judge_logo">
			<a href="#"><img src="<?php echo base_url("assets/images/banner.png") ?>"/></a>
		</div>
		<div class="login_form">
			<div class="login1">
				<p>
					<label for="email">Email</label><br/>
					<input type="text" name="email" class="sharif_input" value="<?php echo set_value('email'); ?>"/>
					<?php echo form_error('email','<div class="shj_error">','</div>'); ?>
				</p>
				<?php if ($sent): ?>
					<div class="shj_ok">We sent you an email containing a link to change your password.</div>
				<?php endif ?>
			</div>
			<div class="login2">
				<p style="margin:0;">
					<?php echo anchor("login","Login") ?>
					<input type="submit" value="Get New Password" id="sharif_submit"/>
				</p>
			</div>
		</div>
	</div>
</form>