<?php
/**
 * Sharif Judge online judge
 * @file reset_password.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php echo form_open('login/reset/' . $key) ?>
	<div class="box login">

		<div class="judge_logo">
			<a href="<?php echo site_url() ?>"><img src="<?php echo base_url("assets/images/banner.png") ?>"/></a>
		</div>

		<div class="login_form">
			<div class="login1">
				<p>
					<label for="password">New Password:</label><br/>
					<input type="password" name="password" class="sharif_input"/>
					<?php echo form_error('password', '<div class="shj_error">', '</div>'); ?>
				</p>

				<p>
					<label for="password_again">New Password, Again:</label><br/>
					<input type="password" name="password_again" class="sharif_input"/>
					<?php echo form_error('password_again', '<div class="shj_error">', '</div>'); ?>
				</p>
				<?php if ($reset === TRUE): ?>
					<div class="shj_ok">Login with your new password!</div>
				<?php endif ?>
			</div>
			<div class="login2">
				<p style="margin:0;">
					<?php echo anchor("login", "Login") ?>
					<input type="submit" value="Set Password" id="sharif_submit"/>
				</p>
			</div>
		</div>

	</div>
</form>