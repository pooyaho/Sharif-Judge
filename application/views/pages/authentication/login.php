<?php
/**
 * Sharif Judge online judge
 * @file login.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script>
	$(document).ready(function(){
		$("#username").focus();
	});
</script>

<?php echo form_open('login') ?>
	<div class="box login">

		<div class="judge_logo">
			<a href="<?php echo site_url() ?>"><img src="<?php echo base_url("assets/images/banner.png") ?>"/></a>
		</div>

		<div class="login_form">
			<div class="login1">
				<p>
					<label for="username">Username</label><br/>
					<input type="text" id="username" name="username" class="sharif_input" value="<?php echo set_value('username'); ?>"/>
					<?php echo form_error('username','<div class="shj_error">','</div>'); ?>
				</p>
				<p>
					<label for="password">Password</label><br/>
					<input type="password" name="password" class="sharif_input"/>
					<?php echo form_error('password','<div class="shj_error">','</div>'); ?>
				</p>
				<?php if ($error): ?>
					<div class="shj_error">Incorrect username or password.</div>
				<?php endif ?>
			</div>
			<div class="login2">
				<p style="margin:0;">
					<?php if ($this->settings_model->get_setting('enable_registration')): ?>
					<?php echo anchor("register","Register") ?> |
					<?php endif ?>
					<?php echo anchor('login/lost','Lost?') ?>
					<input type="submit" value="Login" id="sharif_submit"/>
				</p>
			</div>
		</div>

	</div>
</form>