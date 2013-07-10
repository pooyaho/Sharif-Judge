<?php
/**
 * Sharif Judge online judge
 * @file assignments.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo site_url('assets/images/icons/assignments.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<?php echo form_open('assignments') ?>
		<p>Selected Homework: <?php echo $assignment->name ?></p>
		<p><label for="assignment_select">Homework:</label>
			<select id="assignment_select" name ="assignment_select">
				<?php foreach($all_assignments as $item): ?>
					<option value="<?php echo $item['id'] ?>" <?php echo ($item['id']==$assignment->id?' selected="selected" ':"") ?> > <?php echo $item['name'] ?></option>
				<?php endforeach ?>
			</select>
			<?php echo form_error('assignment_select','<div class="error">','</div>'); ?>
		</p>
		<p><input type="submit" value="Select" class="button" /></p>
		</form>
	</div>
</div>