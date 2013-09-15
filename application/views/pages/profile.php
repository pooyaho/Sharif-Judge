<?php
/**
 * Sharif Judge online judge
 * @file profile.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'users')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/profile.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">
		<p class="input_p">
		<?php if ($form_status=="ok"): ?>
		<div class="shj_ok">Profile updated successfully.</div>
		<?php elseif ($form_status=="error"): ?>
			<div class="shj_error">Error updating profile.</div>
		<?php endif ?>
		</p>
		<?php echo form_open('profile/'.$id) ?>
		<p class="input_p clear">
			<label for="username" class="short2">Username:<br>
				<span class="form_comment">You cannot change username.</span>
			</label>
			<input type="text" name="username" class="sharif_input medium" value="<?php echo $edit_username ?>"  disabled/>
		</p>
		<p class="input_p clear">
			<label for="display_name" class="short2">Display Name:</label>
			<input type="text" name="display_name" class="sharif_input medium" value="<?php echo $display_name ?>"/>
			<?php echo form_error('display_name','<div class="shj_error">','</div>'); ?>
		</p>
		<p class="input_p clear">
			<label for="email" class="short2">Email Address:</label>
			<input type="text" name="email" class="sharif_input medium" value="<?php echo $email ?>"/>
			<?php echo form_error('email','<div class="shj_error">','</div>'); ?>
		</p>
		<p class="input_p clear">
			<label for="password" class="short2">Password:<br>
				<span class="form_comment">If you don't want to change password, leave this blank.</span>
			</label>
			<input type="password" name="password" class="sharif_input medium"/>
			<?php echo form_error('password','<br><span class="shj_error">','</span>'); ?>
		</p>
		<p class="input_p clear">
			<label for="password_again" class="short2">Password, Again:</label>
			<input type="password" name="password_again" class="sharif_input medium"/>
			<?php echo form_error('password_again','<div class="shj_error">','</div>'); ?>
		</p>
		<?php if ($user_level==3): ?>
		<p class="input_p clear">
			<label for="role" class="short2">User Role:</label>
			<select name="role" class="sharif_input">
				<option value="admin" <?php if ($role=="admin") echo 'selected="selected"' ?> >admin</option>
				<option value="head_instructor" <?php if ($role=="head_instructor") echo 'selected="selected"' ?> >head_instructor</option>
				<option value="instructor" <?php if ($role=="instructor") echo 'selected="selected"' ?> >instructor</option>
				<option value="student" <?php if ($role=="student") echo 'selected="selected"' ?> >student</option>
			</select>
			<?php echo form_error('role','<div class="shj_error">','</div>'); ?>
		</p>
		<?php endif ?>
		<p class="input_p">
			<input type="submit" value="Save" class="sharif_input"/>
		</p>
		</form>

	</div> <!-- main_content -->

</div> <!-- main_container -->