<?php
/**
 * Sharif Judge online judge
 * @file add_assignment.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui-1.10.3.custom.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui-timepicker-addon.js") ?>"></script>
<link rel="stylesheet" href="<?php echo base_url("assets/styles/flick/jquery-ui-1.10.3.custom.min.css") ?>"/>
<script>
	var numOfProblems=1;
	var row1='<tr>\
		<td>';
	var row2='</td>\
		<td><input type="text" name="name[]" class="sharif_input short"/></td>\
		<td><input type="text" name="score[]" class="sharif_input short"/></td>\
		<td><input type="text" name="time_limit[]" class="sharif_input short" value="1000"/></td>\
		<td><input type="text" name="memory_limit[]" class="sharif_input short" value="50000"/></td>\
		<td><input type="text" name="filetypes[]" class="sharif_input short" value="c,cpp,java"/></td>\
		<td><input type="checkbox" name="judge[]" class="check" value="';
	var row3='" checked/></td>\
	</tr>';
	$(document).ready(function(){
		$("#add").click(function(){
			$("#problems_table").children().last().after(row1+(numOfProblems+1)+row2+(numOfProblems+1)+row3);
			numOfProblems++;
		});
		$("#remove").click(function(){
			if (numOfProblems==1) return;
			$("#problems_table").children().last().remove();
			numOfProblems--;
		});
		$('#start_time').datetimepicker();
		$('#finish_time').datetimepicker();
	});

</script>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'add_assignment')); ?>
<div id="main_container">
	<div id="page_title"><img src="<?php echo base_url('assets/images/icons/profile.png') ?>"/> <span><?php echo $title ?></span></div>
	<div id="main_content">
		<?php echo form_open_multipart('add_assignment/add') ?>
		<input type="hidden" name="number_of_problems" value="1"/>
		<p class="input_p">
			<label for="assignment_name">Assignment Name:</label><br/>
			<input type="text" name="assignment_name" class="sharif_input medium" value="<?php echo set_value('assignment_name'); ?>"/>
			<?php echo form_error('assignment_name','<div class="error">','</div>'); ?>
		</p>
		<p class="input_p">
			<input type="checkbox" name="open" value="1" checked/> Open<br>
			<span class="form_comment">Open or close this assignment.</span>
			<?php echo form_error('open','<div class="error">','</div>'); ?>
		</p>
		<p class="input_p">
			<input type="checkbox" name="scoreboard" value="1" checked/> Scoreboard<br>
			<span class="form_comment">Check this to enable scoreboard for this assignment.</span>
			<?php echo form_error('scoreboard','<div class="error">','</div>'); ?>
		</p>
		<p class="input_p">
			<label for="start_time">Start Time:</label><br>
			<input type="text" id="start_time" class="sharif_input medium"/>
			<?php echo form_error('start_time','<div class="error">','</div>'); ?>
		</p>
		<p class="input_p">
			<label for="finish_time">Finish Time:</label><br>
			<input type="text" id="finish_time" class="sharif_input medium"/>
			<?php echo form_error('finish_time','<div class="error">','</div>'); ?>
		</p>
		<p class="input_p">
			<label for="extra_time">Extra Time (seconds):</label><br>
			<input type="text" id="extra_time" class="sharif_input medium"/>
			<?php echo form_error('extra_time','<div class="error">','</div>'); ?>
		</p>
		<p class="input_p">Problems <i class="splashy-add" id="add"></i> <i class="splashy-remove_minus_sign" id="remove"></i>
		<table id="problems_table">
			<thead><tr><th></th><th>Problem Name</th><th>Problem Score</th><th>Time Limit (ms)</th><th>Memory Limit (kB)</th><th>Allowed Filetypes</th><th>Judge?</th></tr></thead>
			<tr>
				<td>1</td>
				<td><input type="text" name="name[]" class="sharif_input short"/></td>
				<td><input type="text" name="score[]" class="sharif_input short"/></td>
				<td><input type="text" name="time_limit[]" class="sharif_input short" value="1000"/></td>
				<td><input type="text" name="memory_limit[]" class="sharif_input short" value="50000"/></td>
				<td><input type="text" name="filetypes[]" class="sharif_input short" value="c,cpp,java"/></td>
				<td><input type="checkbox" name="judge[]" class="check" value="1" checked/></td>
			</tr>
		</table>
		</p>
		<p class="input_p">
			<label for="participants">Participants:</label><br>
			<textarea name="participants" cols="45" rows="5" class="sharif_input"></textarea>
		</p>
		<p class="input_p">
			<label for="tests">Tests (zip file):</label><br>
			<input type="file" name="tests" class="sharif_input"/>
		</p>
		<p class="input_p">
			<input type="submit" value="Add Assignment" class="sharif_input"/>
		</p>
		</form>
	</div>
</div>