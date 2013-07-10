<?php
/**
 * Sharif Judge online judge
 * @file register.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php echo form_open('login/register') ?>
	<div class="box register">
		<div class="judge_logo">
			<a href="#"><img src="<?php echo site_url("assets/images/logo.png") ?>"/></a>
		</div>
		<div class="login_form">
			<div class="login1">
				<p>
					<label for="username">Username</label><br/>
					<input type="text" name="username" class="sharif_input" value="<?php echo set_value('username'); ?>"/>
					<?php echo form_error('username','<div class="error">','</div>'); ?>
				</p>
				<p>
					<label for="email">Email</label><br/>
					<input type="text" name="email" class="sharif_input" value="<?php echo set_value('email'); ?>"/>
					<?php echo form_error('email','<div class="error">','</div>'); ?>
				</p>
				<p>
					<label for="password">Password</label><br/>
					<input type="password" name="password" class="sharif_input"/>
					<?php echo form_error('password','<div class="error">','</div>'); ?>
				</p>
				<p>
					<label for="password_again">Password, Again</label><br/>
					<input type="password" name="password_again" class="sharif_input"/>
					<?php echo form_error('password_again','<div class="error">','</div>'); ?>
				</p>
			</div>
			<div class="login2">
				<p style="margin:0;">
					<?php echo anchor("login","Login") ?></a>
					<input type="submit" value="Register" id="sharif_submit"/>
				</p>
			</div>
		</div>
	</div>
</form>