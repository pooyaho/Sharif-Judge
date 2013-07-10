<?php
/**
 * Sharif Judge online judge
 * @file profile.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'profile')); ?>
	<div id="main_container">
		<div id="page_title"><img src="<?php echo site_url('assets/images/icons/profile.png') ?>"/> <span><?php echo $title ?></span></div>
		<div id="main_content">
			<?php echo form_open('profile/update') ?>
			<p class="input_p">
				<label for="username">Username:</label><br/>
				<span class="form_comment">You cannot change your username.</span><br/>
				<input type="text" class="sharif_input" value="<?php echo $username ?>"  disabled/>
			</p>
			<p class="input_p">
				<label for="display_name">Display Name:</label><br/>
				<input type="text" name="display_name" class="sharif_input" value="<?php echo $display_name ?>"/>
				<?php echo form_error('display_name','<div class="error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="email">Email Address:</label><br/>
				<input type="text" name="email" class="sharif_input" value="<?php echo $email ?>"/>
				<?php echo form_error('email','<div class="error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="password">Password:</label><br/>
				<span class="form_comment">If you don't want to change your password, leave this blank.</span><br/>
				<input type="password" name="password" class="sharif_input"/>
				<?php echo form_error('password','<div class="error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="password_again">Password, Again:</label><br/>
				<input type="password" name="password_again" class="sharif_input"/>
				<?php echo form_error('password_again','<div class="error">','</div>'); ?>
			</p>
			<p class="input_p">
				<input type="submit" value="Save" class="sharif_button"/>
			</p>
			</form>
		</div>
	</div>