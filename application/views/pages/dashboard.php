<?php
/**
 * Sharif Judge online judge
 * @file dashboard.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/fullcalendar/fullcalendar.css") ?>'/>
<script type='text/javascript' src="<?php echo base_url("assets/fullcalendar/fullcalendar.min.js") ?>"></script>

<link rel='stylesheet' type='text/css' href='<?php echo base_url("assets/gridster/jquery.gridster.css") ?>'/>
<script type='text/javascript' src="<?php echo base_url("assets/gridster/jquery.gridster.min.js") ?>"></script>

<script type='text/javascript' src="<?php echo base_url("assets/js/jquery.autoellipsis-1.0.10.min.js") ?>"></script>

<?php $now = shj_now() ?>
<script>
	$(document).ready(function () {

		$('#calendar').fullCalendar({
			timeFormat: 'HH:mm { - HH:mm}',
			editable: false,
			height: 280,
			firstDay: <?php echo $week_start ?>,
			events: [<?php
				$i=0;
				$colors = array ('#812C8C','#FF750D','#2C578C','#013440','#A6222C','#42758C','#02A300','#BA6900');
				foreach ($all_assignments as $assignment){
					echo '{';
					echo 'id:'.$assignment['id'].',';
					echo 'title:\''.$assignment['name'].'\',';
					echo 'start:\''.$assignment['start_time'].'\',';
					echo 'end:\''.$assignment['finish_time'].'\',';
					echo 'allDay:false,';
					echo 'color:\''.$colors[($i)%count($colors)].'\'';
					echo '}';
					$i++;
					if ($i!==count($all_assignments))
						echo ',';
				}
			?>]
		});

		var gridster = $(".gridster ul").gridster({
			widget_margins: [10, 10],
			widget_base_dimensions: [390, 390],
			serialize_params: function ($w, wgd) {
				return {
					r: wgd.row,
					c: wgd.col,
					x: wgd.size_x,
					y: wgd.size_y
				}
			},
			draggable: {
				handle: '.widget_title',
				stop: function (event, ui) { // send widget positions to server for saving in database
					var positions = JSON.stringify(gridster.serialize());
					$.post(
						"<?php echo site_url('dashboard/widget_positions') ?>",
						{
							positions: positions,
							<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
						},
						function (data) {
						}
					);
				}
			}
		}).data('gridster');

		$('.notif_text').ellipsis();

		$('.widget_scrollable').nanoScroller();
	});
</script>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar', array('selected' => 'dashboard')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/dashboard.png') ?>"/>
		<span><?php echo $title ?></span>
	</div>

	<div id="main_content">
		<?php foreach($errors as $error): ?>
			<p class="shj_error"><?php echo $error ?></p>
		<?php endforeach ?>
		<div style="height: 15px;"></div>
		<div id="result"></div>
		<div class="gridster">
			<?php $i = 0; ?>
			<ul>
				<?php
				$position = 'data-row="1" data-col="1" data-sizex="1" data-sizey="1"'; //default position
				if (isset($widget_positions[$i]))
					$position = 'data-row="' . $widget_positions[$i]['r'] . '" data-col="' . $widget_positions[$i]['c'] . '" data-sizex="' . $widget_positions[$i]['x'] . '" data-sizey="' . $widget_positions[$i]['y'] . '"';
				$i++;
				?>
				<li <?php echo $position ?>>
					<div class="shj_widget">
						<div class="widget_title"><i class="splashy-calendar_month"></i> Calendar</div>
						<div class="widget_scrollable nano">
							<div class="content">
								<div id='calendar'></div>
							</div>
						</div>
					</div>
				</li>

				<?php
				$position = 'data-row="1" data-col="2" data-sizex="1" data-sizey="1"'; //default position
				if (isset($widget_positions[$i]))
					$position = 'data-row="' . $widget_positions[$i]['r'] . '" data-col="' . $widget_positions[$i]['c'] . '" data-sizex="' . $widget_positions[$i]['x'] . '" data-sizey="' . $widget_positions[$i]['y'] . '"';
				$i++;
				?>
				<li <?php echo $position ?>>
					<div class="shj_widget">
						<div class="widget_title"><i class="splashy-comments"></i> Latest Notifications</div>
						<div class="widget_scrollable nano">
							<div class="content">
								<?php $this->view('pages/list_notifications',array('type'=>'latest')) ?>
							</div>
						</div>
					</div>
				</li>

				<?php
				$position = 'data-row="2" data-col="1" data-sizex="1" data-sizey="1"'; //default position
				if (isset($widget_positions[$i]))
					$position = 'data-row="' . $widget_positions[$i]['r'] . '" data-col="' . $widget_positions[$i]['c'] . '" data-sizex="' . $widget_positions[$i]['x'] . '" data-sizey="' . $widget_positions[$i]['y'] . '"';
				$i++;
				?>
				<li <?php echo $position ?>>

				</li>
			</ul>
		</div>

	</div> <!-- main_content -->

</div> <!-- main_container -->