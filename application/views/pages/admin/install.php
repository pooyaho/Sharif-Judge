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
			<?php if (!$key_changed): ?>
				<p class="shj_error">
					It seems that the file <code style="border: 1px solid #C7C7C7; background-color: #ECECEC;">application/config/config.php</code> is not writable by PHP.
				</p>
				<p>
					So, for security, you should change the encryption key manually.<br>
					Open <code style="border: 1px solid #C7C7C7; background-color: #ECECEC;">application/config/config.php</code> and change the encryption key in this line:
				</p>
				<pre>$config['encryption_key'] = '919RgokTjymS34AhPzF76tcLjTVYMV8T';</pre>
				<p>
					The key should be a 32-characters string as random as possible, with numbers and uppercase and lowercase letters.<br>
					You can use this random string: <code style="border: 1px solid #C7C7C7; background-color: #ECECEC;"><?php echo random_string('alnum',32) ?></code>
				</p>
				<br>
			<?php endif ?>
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