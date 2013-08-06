<?php
/**
 * Sharif Judge online judge
 * @file install.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<div id="main_container">
	<div id="page_title"><span><?php echo $title ?></span></div>
	<div id="main_content">
		<?php if($status=="Installed"): ?>
			<p class="shj_ok">Sharif Judge installed! Now you can <?php echo anchor('login','login') ?>.</p>
		<?php else: ?>
		<?php echo form_open('install') ?>
		<p class="input_p">
			<label for="username">Admin username:</label><br>
			<input class="sharif_input" type="text" name="username"  value="<?php echo set_value('username'); ?>"/>
			<?php echo form_error('username','<div class="shj_error">','</div>'); ?>
		</p>
		<p class="input_p">
			<label for="email">Admin email:</label><br>
			<input class="sharif_input" type="text" name="email" value="<?php echo set_value('email'); ?>"/>
			<?php echo form_error('email','<div class="shj_error">','</div>'); ?>
		</p>
		<p class="input_p">
			<label for="username">Admin password:</label><br>
			<input class="sharif_input" type="password" name="password"/>
			<?php echo form_error('password','<div class="shj_error">','</div>'); ?>
		</p>
		<p class="input_p">
			<label for="username">Password, again:</label><br>
			<input class="sharif_input" type="password" name="password_again"/>
			<?php echo form_error('password_again','<div class="shj_error">','</div>'); ?>
		</p>
		<p class="input_p">
			<input class="sharif_input" type="submit" value="Continue"/>
		</p>
		</form>
		<?php endif ?>
	</div>
</div>