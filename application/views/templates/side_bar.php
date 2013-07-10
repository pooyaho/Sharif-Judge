<?php
/**
 * Sharif Judge online judge
 * @file side_bar.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<script type= "text/javascript">
	var offset;
	var time;
	function sync_server_time() {
		$.ajax({
			type: 'POST',
			url: '<?php echo site_url('server_time') ?>',
			timeout: 1000,
			success: function(data) {
				offset = moment(data).diff(moment());
			}
		});
	}
	function update_clock(){
		if (Math.abs(moment().diff(time))>1500){
			sync_server_time();
		}
		time = moment();
		$('#timer').html('Server Time: '+moment().add('milliseconds',offset).format('HH:mm:ss'));
	}
	$(document).ready(function() {
		time = moment();
		sync_server_time();
		//window.setInterval(sync_server_time,30000);
		window.setInterval(update_clock,1000);
	});
</script>
<div id="side_bar">
	<ul>
		<div class="side_box"><a href="<?php echo site_url('dashboard') ?>"><li <?php echo ($selected=='dashboard'?'class="selected"':'') ?>><i class="splashy-home_green"></i> Dashboard</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('settings') ?>"><li <?php echo ($selected=='settings'?'class="selected"':'') ?>><i class="splashy-sprocket_light"></i> Settings</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('profile') ?>"><li <?php echo ($selected=='profile'?'class="selected"':'') ?>><i class="splashy-contact_grey"></i> Profile</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('assignments') ?>"><li <?php echo ($selected=='assignments'?'class="selected"':'') ?>><i class="splashy-folder_modernist_opened"></i> Assignments</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('submit') ?>"><li <?php echo ($selected=='submit'?'class="selected"':'') ?>><i class="splashy-arrow_large_up"></i> Submit</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('final_submissions') ?>"><li <?php echo ($selected=='final_submissions'?'class="selected"':'') ?>><i class="splashy-marker_rounded_violet"></i> Final Submissions</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('all_submissions') ?>"><li <?php echo ($selected=='all_submissions'?'class="selected"':'') ?>><i class="splashy-view_list_with_thumbnail"></i> All Submissions</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('scoreboard') ?>"><li <?php echo ($selected=='scoreboard'?'class="selected"':'') ?>><i class="splashy-star_boxed_full"></i> Scoreboard</li></a></div>
	</ul>
	<div id="about">
		<p><a href="https://github.com/mjnaderi/Sharif-Judge" >Sharif Judge</a></p>
		<p id="timer"></p>
	</div>
</div>

