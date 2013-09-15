<?php
/**
 * Sharif Judge online judge
 * @file moss.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/shield.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">

		<h3>What is Moss?</h3>
		<p><?php echo anchor("http://theory.stanford.edu/~aiken/moss/",'Moss','target="_blank"') ?> (for a Measure Of Software Similarity) is an automatic system for determining the similarity of programs.
			To date, the main application of Moss has been in detecting plagiarism in programming classes. Since its
			development in 1994, Moss has been very effective in this role. The algorithm behind moss is a significant
			improvement over other cheating detection algorithms.</p>

		<br>

		<h3>Moss user id</h3>
		<?php if ($moss_userid==""): ?>
			<p class="shj_error">You have not entered your Moss user id.</p>
		<?php endif ?>
		<p>
			Read <a href="http://theory.stanford.edu/~aiken/moss/">this page</a> and register for Moss,
			then find your user id in the script sent to your email by Moss and enter your user id here.
		</p>
		<?php echo form_open('moss/update/'.$moss_assignment['id']) ?>
		<p class="input_p">
			<label for="moss_userid">Your Moss user id is:</label>
			<input type="text" name="moss_userid" class="sharif_input" value="<?php echo $moss_userid ?>"/>
		</p>
		<input type="submit" class="sharif_input" value="Save"/>
		</form>

		<br>

		<h3>Detect similar submissions of assignment "<?php echo $moss_assignment['name'] ?>":</h3>
		<p>
		<?php echo form_open('moss/detect/'.$moss_assignment['id']) ?>
		You can send final submissions of assignment "<?php echo $moss_assignment['name'] ?>" to Moss by clicking on this button.<br>
		Zip files will not be sent.<br>
		It may take a minute. Please be patient.<br>
		<input type="submit" class="sharif_input" value="Detect similar codes"/>
		</form>
		</p>

		<br>

		<h3>Moss results for assignment "<?php echo $moss_assignment['name'] ?>":</h3>
		<p>
			Links will expire after some time. (last update: <?php echo $update_time; ?>) <br>
			<ul>
			<?php for ($i=1;$i<=$moss_assignment['problems'];$i++): ?>
				<li>Problem <?php echo $i ?>:
				<?php
					if ($moss_problems[$i]===FALSE)
						echo "link not found";
					else
						echo anchor($moss_problems[$i],$moss_problems[$i],'target="_blank"')
				?></li>
			<?php endfor ?>
			</ul>
		</p>

	</div> <!-- main_content -->

</div> <!-- main_container -->