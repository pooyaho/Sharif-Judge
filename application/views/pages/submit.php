<?php
/**
 * Sharif Judge online judge
 * @file submit.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<script>
	p=[];
	<?php foreach ($problems as $problem){
		$filetypes = explode(",",$problem['allowed_file_types']);
		$types="";
		foreach ($filetypes as $filetype){
			$types = $types."'".trim($filetype)."',";
		}
		$types = substr($types,0,strlen($types)-1);
		echo "p[{$problem['id']}] = [{$types}];";
	} ?>
	$(document).ready(function(){
		$("select#problems").change(function(){
			var v = $(this).val();
			var text = '<option value="0" selected="selected">-- Select One --</option>\n';
			if (v!=0)
				for (i=0;i<p[v].length;i++){
					text += '<option value="'+p[v][i]+'">'+p[v][i]+'</option>\n';
				}
			$("select#filetypes").html(text);
		});
	});
</script>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'submit')); ?>
<?php $now = shj_now(); ?>
	<div id="main_container">
		<div id="page_title"><img src="<?php echo base_url('assets/images/icons/submit.png') ?>"/> <span><?php echo $title ?></span></div>
		<div id="main_content">
			<?php if ($assignment['id']==0): ?>
				<p>Please select an assignment first.</p>
			<?php elseif ($this->user_model->get_user_level($username)==0 && !$assignment['open']): ?>
			<?php // if assignment is closed, non-student users (admin, instructors) still can submit ?>
				<p>Selected assignment has been closed.</p>
			<?php elseif ($now < strtotime($assignment['start_time'])): ?>
				<p>Selected assignment has not started.</p>
			<?php elseif ($now > strtotime($assignment['finish_time'])+$assignment['extra_time']): // deadline = finish_time + extra_time?>
				<p>Selected assignment has finished.</p>
			<?php elseif ( !$this->assignment_model->is_participant($assignment['participants'],$username) ): ?>
				<p>You are not registered for submitting.</p>
			<?php else: ?>
				<?php echo form_open_multipart('submit') ?>
				<p class="input_p">
					<label for="problem">Problem:</label><br>
					<select id="problems" name ="problem" class="sharif_input">
						<option value="0" selected="selected">-- Select One --</option>
						<?php foreach ($problems as $problem): ?>
							<option value="<?php echo $problem['id'] ?>"><?php echo $problem['name'] ?></option>
						<?php endforeach ?>
					</select>
					<?php echo form_error('problem','<div class="error">','</div>'); ?>
				</p>
				<p class="input_p">
					<label for="problem">File Type:</label><br>
					<select id="filetypes" name="filetype" class="sharif_input">
						<option value="0" selected="selected">-- Select One --</option>
					</select>
					<?php echo form_error('filetype','<div class="error">','</div>'); ?>
				</p>
				<p class="input_p">
					<label for="userfile">File:</label><br>
					<input type="file" id="file" class="sharif_input" name="userfile" />
					<?php if ($upload_state==='error'): ?>
					<div class="error">Error uploading file.</div>
					<?php elseif ($upload_state==='ok'): ?>
					<div class="ok">File uploaded successfully. See the result in 'All Submissions'.</div>
					<?php endif ?>
					<?php echo $this->upload->display_errors('<div class="error">','</div>'); ?>
				</p>
				<p class="input_p">
					<input type="submit" value="Submit" class="sharif_input"/>
				</p>
				</form>
			<?php endif ?>
		</div>
	</div>