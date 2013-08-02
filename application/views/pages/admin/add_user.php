<?php
/**
 * Sharif Judge online judge
 * @file add_user.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'users')); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo base_url('assets/images/icons/add_user.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<p>You can use this field to add multiple users at the same time.</p>
		<?php echo form_open('users/add') ?>
			<p class="input_p">
				<textarea name="new_users" rows="20" cols="80" class="sharif_input"><?php
					echo "# Lines starting with a # sign are comments.\n";
					echo "# The syntax of each line is:\n";
					echo "# USERNAME  EMAIL  PASSWORD\n";
				?></textarea>
			</p>
			<input type="submit" class="sharif_input" value="Add Users"/>
		</form>
	</div>
</div>