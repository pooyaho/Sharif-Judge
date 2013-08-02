<?php
/**
 * Sharif Judge online judge
 * @file side_bar.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<script type= "text/javascript">
	var offset;
	var time;
	var finish_time;
	var extra_time;
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
		var now = moment().add('milliseconds',offset);
		$('#timer').html('Server Time: '+now.format('MMM DD - HH:mm:ss'));
		var countdown = finish_time.diff(now);
		if (countdown<=0 && countdown + extra_time.asMilliseconds()>=0){
			countdown = countdown + extra_time.asMilliseconds();
			$("#extra_time").css("display","block");
		}
		else
			$("#extra_time").css("display","none");
		if (countdown<=0){
			countdown=0;
		}

		countdown = Math.floor(moment.duration(countdown).asSeconds());
		var seconds = countdown%60; countdown=(countdown-seconds)/60;
		var minutes = countdown%60; countdown=(countdown-minutes)/60;
		var hours = countdown%24; countdown=(countdown-hours)/24;
		var days = countdown;
		$("#time_days").html(days);
		$("#time_hours").html(hours);
		$("#time_minutes").html(minutes);
		$("#time_seconds").html(seconds);
		if(days==1)
			$("#days_label").css("display","none");
		else
			$("#days_label").css("display","inline");
		if(hours==1)
			$("#hours_label").css("display","none");
		else
			$("#hours_label").css("display","inline");
		if(minutes==1)
			$("#minutes_label").css("display","none");
		else
			$("#minutes_label").css("display","inline");
		if(seconds==1)
			$("#seconds_label").css("display","none");
		else
			$("#seconds_label").css("display","inline");

	}
	$(document).ready(function() {
		time = moment();
		finish_time = moment("<?php echo $assignment['finish_time'] ?>");
		extra_time = moment.duration(<?php echo $assignment['extra_time'] ?>, 'seconds');
		sync_server_time();
		update_clock();
		window.setInterval(update_clock,1000);
	});
</script>
<div id="side_bar">
	<ul>
		<div class="side_box"><a href="<?php echo site_url('dashboard') ?>"><li <?php echo ($selected=='dashboard'?'class="selected"':'') ?>><i class="splashy-home_green"></i> Dashboard</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('profile') ?>"><li <?php echo ($selected=='profile'?'class="selected"':'') ?>><i class="splashy-contact_grey"></i> Profile</li></a></div>
		<?php if ($user_level==3): ?>
		<div class="side_box"><a href="<?php echo site_url('settings') ?>"><li <?php echo ($selected=='settings'?'class="selected"':'') ?>><i class="splashy-sprocket_light"></i> Settings</li></a></div>
		<?php endif ?>
		<?php if ($user_level>=2): ?>
		<div class="side_box"><a href="<?php echo site_url('add_assignment') ?>"><li <?php echo ($selected=='add_assignment'?'class="selected"':'') ?>><i class="splashy-add"></i> Add Assignment</li></a></div>
		<?php endif ?>
		<?php if ($user_level==3): ?>
		<div class="side_box"><a href="<?php echo site_url('users') ?>"><li <?php echo ($selected=='users'?'class="selected"':'') ?>><i class="splashy-group_blue"></i> Users</li></a></div>
		<?php endif ?>
		<div class="side_box"><a href="<?php echo site_url('assignments') ?>"><li <?php echo ($selected=='assignments'?'class="selected"':'') ?>><i class="splashy-folder_modernist_opened"></i> Assignments</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('submit') ?>"><li <?php echo ($selected=='submit'?'class="selected"':'') ?>><i class="splashy-arrow_large_up"></i> Submit</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('submissions/final') ?>"><li <?php echo ($selected=='final_submissions'?'class="selected"':'') ?>><i class="splashy-marker_rounded_violet"></i> Final Submissions</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('submissions/all') ?>"><li <?php echo ($selected=='all_submissions'?'class="selected"':'') ?>><i class="splashy-view_list_with_thumbnail"></i> All Submissions</li></a></div>
		<div class="side_box"><a href="<?php echo site_url('scoreboard') ?>"><li <?php echo ($selected=='scoreboard'?'class="selected"':'') ?>><i class="splashy-star_boxed_full"></i> Scoreboard</li></a></div>
	</ul>
	<div id="about">
		<p><a href="https://github.com/mjnaderi/Sharif-Judge" >Sharif Judge</a></p>
		<p id="timer"></p>
	</div>
</div>

