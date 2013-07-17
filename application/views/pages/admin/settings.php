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
		<?php echo form_open('settings/update') ?>
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
		</p>
		<p class="input_p">
			<input type="submit" value="Save" class="sharif_input"/>
		</p>
		</form>
	</div>
</div>