<?php
/**
 * Sharif Judge online judge
 * @file profile.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar'); ?>
	<div id="main_container">
		<div id="page_title"><img src="<?php echo site_url('images/icons/profile.png') ?>"/> <span><?php echo $title ?></span></div>
		<div id="main_content">
			<?php echo form_open('profile') ?>
			<p>
				<label for="display_name">Display Name:</label><br/>
				<input type="text" name="display_name" class="sharif_input"/>
			</p>
			<p>
				<label for="display_name">Email Address:</label><br/>
				<input type="text" name="email" class="sharif_input"/>
			</p>
			<p>
				<label for="display_name">Password:</label><br/>
				<input type="text" name="email" class="sharif_input"/>
			</p>
			<p>
				<label for="display_name">Password, Again:</label><br/>
				<input type="text" name="email" class="sharif_input"/>
			</p>
			<p>
				<input type="submit" value="Save" class="sharif_button"/>
			</p>
			</form>
		</div>
	</div>