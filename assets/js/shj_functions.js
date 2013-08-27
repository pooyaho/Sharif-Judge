/**
 * Sharif Judge
 * @file shj_functions.js
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */

function sync_server_time() {
	$.post(time_url,
		{},
		function(data) {
			offset = moment(data).diff(moment());
		}
	);
}

function update_clock(){
	if (Math.abs(moment().diff(time))>3500){
		//console.log('moment: '+moment()+' time: '+time+' diff: '+Math.abs(moment().diff(time)));
		sync_server_time();
	}
	time = moment();
	var now = moment().add('milliseconds',offset);
	$('.timer').html('Server Time: '+now.format('MMM DD - HH:mm:ss'));
	var countdown = finish_time.diff(now);
	if (countdown<=0 && countdown + extra_time.asMilliseconds()>=0){
		countdown = countdown + extra_time.asMilliseconds();
		$("div#extra_time").css("display","block");
	}
	else
		$("div#extra_time").css("display","none");
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

function sidebar_open(time){
	if (time==0){
		$(".sidebar_text").css('display','inline-block');
		$("#sidebar_bottom p").css('display','block');
		$("#side_bar").css('width', '170px');
		$("#main_container").css('margin-left','170px');
	}
	else{
		$("#side_bar").animate({width: '170px'},time,function(){$(".sidebar_text").css('display','inline-block');$("#sidebar_bottom p").css('display','block');});
		$("#main_container").animate({'margin-left':'170px'},time*1.7);
	}
	$("i#collapse").removeClass("splashy-pagination_1_next");
	$("i#collapse").addClass("splashy-pagination_1_previous");
}

function sidebar_close(time){
	if (time==0){
		$(".sidebar_text").css('display','none');
		$("#sidebar_bottom p").css('display','none');
		$("#side_bar").css('width', '40px');
		$("#main_container").css('margin-left','40px');
	}
	else{
		$("#side_bar").animate({width: '40px'},time,function(){$(".sidebar_text").css('display','none');$("#sidebar_bottom p").css('display','none');});
		$("#main_container").animate({'margin-left':'40px'},time*1.7);
	}
	$("i#collapse").removeClass("splashy-pagination_1_previous");
	$("i#collapse").addClass("splashy-pagination_1_next");
}

function toggle_collapse(){
	if (sidebar == "open"){
		sidebar = "close";
		sidebar_close(200);
		$.cookie('shj_sidebar','close',{path:'/', expires: 365});
	}
	else if (sidebar == "close"){
		sidebar = "open";
		sidebar_open(200);
		$.cookie('shj_sidebar','open',{path:'/', expires: 365});
	}
}