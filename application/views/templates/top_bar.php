<?php
/**
 * Sharif Judge online judge
 * @file top_bar.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */?>
<script>
	$(document).ready(function(){
		$("#select_assignment_top").hoverIntent (
			function(){
				$('#select_assignment_menu').show();
			},
			function(){
				$('#select_assignment_menu').hide();
			}
		);
		$("#user_top").hoverIntent (
			function(){
				$('#user_menu').show();
			},
			function(){
				$('#user_menu').hide();
			}
		);
		$(".check").click(
			function(){
				var id = $(this).attr('id');
				$.ajax({
					type: 'POST',
					url: '<?php echo site_url('assignments/select') ?>',
					data: {assignment_select:id},
					timeout: 1000,
					success: function(a) {
						//if (a != "shj_failed"){
						if (a == "shj_success"){
							$(".check").removeClass('checked');
							$(".i"+id).addClass('checked');
							$(".assignment_name").html($("#"+id+" .assignment_item").html());
						}
					}
				});
			}
		);
	});
</script>
<div id="top_bar">
	<div class="top_object" id="user_top">
		<?php echo anchor('profile',$username,'id="profile_link"') ?>
		<div class="top_menu" id="user_menu">
			<?php echo anchor('profile','Profile') ?><br>
			<?php echo anchor('logout','Log Out'); ?>
		</div>
	</div>
	<div class="top_object" id="select_assignment_top">
		<a href="<?php echo site_url('assignments') ?>"><span class="assignment_name"><?php echo $assignment['name'] ?></span></a>
		<div class="top_menu" id="select_assignment_menu">
			<?php foreach($all_assignments as $item): ?>
				<div class="assignment_block" id="<?php echo $item['id'] ?>">
					<div class="c1">
						<div class="<?php echo ($item['id']==$assignment['id']?'check checked':'check') ?> i<?php echo $item['id'] ?>" id="<?php echo $item['id'] ?>"></div>
					</div>
					<div class="assignment_item"><?php echo $item['name'] ?></div>
				</div>
			<?php endforeach ?>
		</div>
	</div>
	<div id="shj_logo">
		<a href="<?php echo site_url('/'); ?>"><img src="<?php echo base_url('assets/images/logo_small.png'); ?>"/></a>
	</div>
</div>