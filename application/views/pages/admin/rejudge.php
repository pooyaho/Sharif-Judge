<?php
/**
 * Sharif Judge online judge
 * @file rejudge.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/rejudge.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">
		<?php foreach ($msg as $message): ?>
			<p class="shj_ok"><?php echo $message ?></p>
		<?php endforeach ?>
		<p>
			Selected Assignment: <?php echo $assignment['name'] ?>
		</p>
		<p>
			By clicking on rejudge, all submissions of selected problem will go in <code>PENDING</code> state. Then
			Sharif Judge rejudges them one by one.
		</p>
		<p>
			If you want to rejudge a single submission, you can click on rejudge button in <?php echo anchor('submissions/all', 'All Submissions') ?> or <?php echo anchor('submissions/final', 'Final Submissions') ?> page.
		</p>
		<?php foreach ($problems as $problem): ?>
			<?php echo form_open('rejudge') ?>
				<input type="hidden" name="problem_id" value="<?php echo $problem['id'] ?>"/>
				<input type="submit" class="sharif_input" value="Rejudge Problem <?php echo $problem['id'] ?> (<?php echo $problem['name'] ?>)"/>
			</form>
		<?php endforeach ?>

	</div> <!-- main_content -->

</div> <!-- main_container -->