<?php
/**
 * Sharif Judge online judge
 * @file settings.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'settings')); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo base_url('assets/images/icons/settings.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<p class="input_p">
		<?php if ($form_status=="ok"): ?>
			<div class="ok">Settings updated successfully.</div>
		<?php elseif ($form_status=="error"): ?>
			<div class="error">Error updating settings.</div>
		<?php elseif ($form_status=="defc"): ?>
			<div class="ok">Settings updated.</div>
			<div class="error">But file defc.h is not writable.</div>
		<?php elseif ($form_status=="defcpp"): ?>
			<div class="ok">Settings updated.</div>
			<div class="error">But file defcpp.h is not writable.</div>
		<?php elseif ($form_status=="defcdefcpp"): ?>
			<div class="ok">Settings updated.</div>
			<div class="error">But files defc.h and defcpp.h are not writable.</div>
		<?php endif ?>
		<?php if ($defc===FALSE): ?>
			<div class="error">"Tester path" is not correct.</div>
		<?php endif ?>
		</p>
		<?php echo form_open('settings/update') ?>
		<div class="panel_left">
			<p class="input_p">
				<label for="timezones">Timezone:</label><br/>
				<?php echo timezone_menu($tz,'sharif_input long') ?>
				<?php echo form_error('timezone','<div class="error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="tester_path">Tester path:</label><br/>
				<input type="text" name="tester_path" class="sharif_input long" value="<?php echo $tester_path ?>"/>
			</p>
			<p class="input_p">
				<label for="assignments_root">Assignments root directory:</label><br/>
				<input type="text" name="assignments_root" class="sharif_input long" value="<?php echo $assignments_root ?>"/>
			</p>
			<p class="input_p">
				<label for="file_size_limit">Upload file size limit (kB):</label><br/>
				<input type="text" name="file_size_limit" class="sharif_input long" value="<?php echo $file_size_limit ?>"/>
				<?php echo form_error('file_size_limit','<div class="error">','</div>'); ?>
			</p>
			<p class="input_p">
				<input type="checkbox" name="enable_easysandbox" value="1" <?php if ($enable_easysandbox) echo 'checked' ?>/> C/C++ Sandbox<br>
				<p class="form_comment long">Enable or disable EasySandbox (using seccomp filters, for C/C++).
				You must <a href="#">build EasySandbox</a> before enabling it.</p>
			</p>
			<p class="input_p">
				<input type="checkbox" name="enable_shield" value="1" <?php if ($enable_shield) echo 'checked' ?>/> C/C++ Shield<br>
				<span class="form_comment">Enable or disable <a href="#">Shield</a> for C/C++</span>
			</p>
			<p class="input_p">
				<input type="checkbox" name="enable_java_policy" value="1" <?php if ($enable_java_policy) echo 'checked' ?>/> Java Policy<br>
				<span class="form_comment">Enable or disable <a href="#">Java Policy</a> for Java</span>
			</p>
			<p class="input_p">
				<input type="checkbox" name="enable_log" value="1" <?php if ($enable_log) echo 'checked' ?>/> Log<br>
				<span class="form_comment">Enable or disable Log</span>
			</p>
			<p class="input_p">
				<input type="submit" value="Save" class="sharif_input"/>
			</p>
		</div>
		<div class="panel_right">
			<p class="input_p">
				<label for="default_late_rule">Default coefficient rule (PHP script without <?php echo htmlspecialchars('<?php ?>') ?> tags):</label><br>
				<textarea name="default_late_rule" rows="10" class="sharif_input add_text"><?php echo $default_late_rule ?></textarea>
			</p>
			<p class="input_p">
				<label for="def_c">Shield rules (for C):</label><br>
				<textarea name="def_c" rows="10" class="sharif_input add_text"><?php if($defc!==FALSE) echo $defc ?></textarea>
			</p>
			<p class="input_p">
				<label for="def_cpp">Shield rules (for C++):</label><br>
				<textarea name="def_cpp" rows="10" class="sharif_input add_text"><?php if($defcpp!==FALSE) echo $defcpp ?></textarea>
			</p>
		</div>
		</form>
	</div>
</div>