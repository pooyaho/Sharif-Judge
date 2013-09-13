<?php
/**
 * Sharif Judge online judge
 * @file add_assignment.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui-1.10.3.custom.min.js") ?>"></script>
<script type="text/javascript" src="<?php echo base_url("assets/js/jquery-ui-timepicker-addon.js") ?>"></script>
<link rel="stylesheet" href="<?php echo base_url("assets/styles/flick/jquery-ui-1.10.3.custom.min.css") ?>"/>
<script>
	var numOfProblems=<?php echo count($problems); ?>;
	var row1='<tr>\
		<td>';
	var row2='</td>\
		<td><input type="text" name="name[]" class="sharif_input short" value="Problem "/></td>\
		<td><input type="text" name="score[]" class="sharif_input tiny2" value="100"/></td>\
		<td><input type="text" name="c_time_limit[]" class="sharif_input tiny2" value="500"/></td>\
		<td><input type="text" name="python_time_limit[]" class="sharif_input tiny2" value="1500"/></td>\
		<td><input type="text" name="java_time_limit[]" class="sharif_input tiny2" value="2000"/></td>\
		<td><input type="text" name="memory_limit[]" class="sharif_input tiny" value="50000"/></td>\
		<td><input type="text" name="languages[]" class="sharif_input short2" value="C,C++,Python 2,Python 3,Java"/></td>\
		<td><input type="text" name="diff_cmd[]" class="sharif_input tiny" value="diff"/></td>\
		<td><input type="text" name="diff_arg[]" class="sharif_input tiny" value="-iw"/></td>\
		<td><input type="checkbox" name="is_upload_only[]" class="check" value="';
	var row3='"/></td>\
	</tr>';
	$(document).ready(function(){
		$("#add").click(function(){
			$("#problems_table").children().last().after(row1+(numOfProblems+1)+row2+(numOfProblems+1)+row3);
			numOfProblems++;
			$('#nop').attr('value',numOfProblems);
		});
		$("#remove").click(function(){
			if (numOfProblems==1) return;
			$("#problems_table tr:last").remove();
			numOfProblems--;
			$('#nop').attr('value',numOfProblems);
		});
		$('#start_time').datetimepicker();
		$('#finish_time').datetimepicker();
	});

</script>
<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'assignments')); ?>
<div id="main_container">
	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/add.png') ?>"/>
		<span><?php echo $title ?></span>
		<span class="title_menu_item">
			<a href="http://docs.sharifjudge.ir/add_assignment" target="_blank"><i class="splashy-help"></i> Help</a>
		</span>
	</div>
	<div id="main_content">
		<?php if ($form_status=='ok'||$form_status=='tests_updated'): ?>
			<div class="shj_ok">Assignment <?php echo $edit?'updated':'added' ?> successfully.</div>
		<?php elseif ($form_status=='error'): ?>
			<div class="shj_error">Error <?php echo $edit?'updating':'adding' ?> assignment.</div>
		<?php elseif ($form_status=='corrupted'): ?>
			<div class="shj_error">Error <?php echo $edit?'updating':'adding' ?> assignment. Unable to unzip uploaded file.</div>
		<?php endif ?>
		<?php if ($form_status=='tests_updated'): ?>
			<div class="shj_ok">Tests <?php echo $edit?'updated':'added' ?> successfully.</div>
		<?php endif ?>

		<?php echo form_open_multipart($edit?'assignments/edit/'.$edit_assignment['id']:'add_assignment/add') ?>
		<div class="panel_left">
			<input type="hidden" name="number_of_problems" id="nop" value="<?php echo $edit?$edit_assignment['problems']:1; ?>"/>
			<p class="input_p">
				<label for="assignment_name">Assignment Name</label>
				<input type="text" name="assignment_name" class="sharif_input medium" value="<?php
					if ($edit)
						echo $edit_assignment['name'];
					else
						echo set_value('assignment_name');
				?>"/>
				<?php echo form_error('assignment_name','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="start_time">Start Time</label>
				<input type="text" name="start_time" id="start_time" class="sharif_input medium" value="<?php
					if ($edit)
						echo date('m/d/Y H:i',strtotime($edit_assignment['start_time']));
					else
						echo set_value('start_time');
				?>" />
				<?php echo form_error('start_time','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="finish_time">Finish Time</label>
				<input type="text" name="finish_time" id="finish_time" class="sharif_input medium" value="<?php
					if ($edit)
						echo date('m/d/Y H:i',strtotime($edit_assignment['finish_time']));
					else
						echo set_value('finish_time');
				?>" />
				<?php echo form_error('finish_time','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p clear">
				<label for="extra_time">
					Extra Time (minutes)<br>
					<span class="form_comment">Extra time for late submissions.</span>
				</label>
				<input type="text" name="extra_time" id="extra_time" class="sharif_input medium" value="<?php
					if ($edit){
						$extra_time = floor($edit_assignment['extra_time']/60);
						if ($extra_time%60==0)
							echo ($extra_time/60).'*60';
						else
							echo $extra_time;
					}
					else
						echo set_value('extra_time');
				?>" />
				<?php echo form_error('extra_time','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p clear">
				<label for="participants">Participants<br>
					<span class="form_comment">Enter username of participants here (comma separated).
						Only these users are able to submit. You can use keyword "ALL".</span>
				</label>
				<textarea name="participants" rows="5" class="sharif_input medium"><?php
					if ($edit)
						echo $edit_assignment['participants'];
					else
						echo set_value('participants','ALL');
					?></textarea>
			</p>
			<p class="input_p clear">
				<label for="tests">Tests (zip file)<br>
					<span class="form_comment">
						<a href="http://docs.sharifjudge.ir/tests_structure" target="_blank">Use this structure</a>
					</span>
				</label>

				<input type="file" name="tests" class="sharif_input medium"/>
				<?php
					if (!$edit)
						echo $this->upload->display_errors('<div class="shj_error">','</div>');
				?>
			</p>
		</div>
		<div class="panel_right">
			<p class="input_p">
				<input type="checkbox" name="open" value="1" <?php if ($edit) echo $edit_assignment['open']?'checked':''; else echo set_checkbox('open','1') ?> /> Open<br>
				<span class="form_comment">Open or close this assignment.</span>
				<?php echo form_error('open','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p">
				<input type="checkbox" name="scoreboard" value="1" <?php if ($edit) echo $edit_assignment['scoreboard']?'checked':''; else echo set_checkbox('scoreboard','1') ?> /> Scoreboard<br>
				<span class="form_comment">Check this to enable scoreboard.</span>
				<?php echo form_error('scoreboard','<div class="shj_error">','</div>'); ?>
			</p>
			<p class="input_p">
				<label for="late_rule">Coefficient rule (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#coefficient_rule">?</a>)</label><br>
				<span class="form_comment medium clear" style="display: block;">PHP script without <?php echo htmlspecialchars('<?php ?>') ?> tags</span>
				<textarea name="late_rule" rows="14" class="sharif_input add_text"><?php
						if ($edit)
							echo $edit_assignment['late_rule'];
						else
							echo set_value('late_rule', $this->settings_model->get_setting('default_late_rule'))
				?></textarea>
				<?php echo form_error('late_rule','<div class="shj_error">','</div>'); ?>
			</p>
		</div>
		<p class="input_p" id="add_problems">Problems <i class="splashy-add" id="add"></i> <i class="splashy-remove_minus_sign" id="remove"></i>
		<table id="problems_table">
			<thead>
			<tr>
				<th rowspan="2"></th>
				<th rowspan="2">Name</th>
				<th rowspan="2">Score</th>
				<th colspan="3" style="border-bottom: 1px solid #BDBDBD">Time Limit (ms)</th>
				<th rowspan="2">Memory<br>Limit (kB)</th>
				<th rowspan="2">Allowed<br>Languages (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#allowed_languages">?</a>)</th>
				<th rowspan="2">Diff<br>Command (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#diff_command">?</a>)</th>
				<th rowspan="2">Diff<br>Argument (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#diff_arguments">?</a>)</th>
				<th rowspan="2">Upload<br>Only (<a target="_blank" href="http://docs.sharifjudge.ir/add_assignment#upload_only">?</a>)</th>
			</tr>
			<tr>
				<th>C/C++</th><th>Python</th><th>Java</th>
			</tr>
			</thead>
			<?php foreach ($problems as $problem): ?>
				<tr>
					<td><?php echo $problem['id']?></td>
					<td><input type="text" name="name[]" class="sharif_input short" value="<?php echo $problem['name'] ?>"/></td>
					<td><input type="text" name="score[]" class="sharif_input tiny2" value="<?php echo $problem['score'] ?>"/></td>
					<td><input type="text" name="c_time_limit[]" class="sharif_input tiny2" value="<?php echo $problem['c_time_limit'] ?>"/></td>
					<td><input type="text" name="python_time_limit[]" class="sharif_input tiny2" value="<?php echo $problem['python_time_limit'] ?>"/></td>
					<td><input type="text" name="java_time_limit[]" class="sharif_input tiny2" value="<?php echo $problem['java_time_limit'] ?>"/></td>
					<td><input type="text" name="memory_limit[]" class="sharif_input tiny" value="<?php echo $problem['memory_limit'] ?>"/></td>
					<td><input type="text" name="languages[]" class="sharif_input short2" value="<?php echo $problem['allowed_languages'] ?>"/></td>
					<td><input type="text" name="diff_cmd[]" class="sharif_input tiny" value="<?php echo $problem['diff_cmd'] ?>"/></td>
					<td><input type="text" name="diff_arg[]" class="sharif_input tiny" value="<?php echo $problem['diff_arg'] ?>"/></td>
					<td><input type="checkbox" name="is_upload_only[]" class="check" value="<?php echo $problem['id'] ?>" <?php if ($problem['is_upload_only']) echo "checked" ?>/></td>
				</tr>
			<?php endforeach ?>
		</table>
		</p>
		<?php echo form_error('name[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('score[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('c_time_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('python_time_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('java_time_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('memory_limit[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('languages[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('diff_cmd[]','<div class="shj_error">','</div>'); ?>
		<?php echo form_error('diff_arg[]','<div class="shj_error">','</div>'); ?>
		<p class="input_p">
			<input type="submit" value="<?php echo $edit?'Edit':'Add' ?> Assignment" class="sharif_input"/>
		</p>
		</form>
	</div>
</div>