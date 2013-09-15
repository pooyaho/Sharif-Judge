<?php
/**
 * Sharif Judge online judge
 * @file notifications_box.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script>
	$(document).ready(function () {
		$(".delete_notif").click(function () {
			var r = confirm("Are you sure you want to delete this notification?");
			if (r == true) {
				var id = $(this).attr('id');
				$.post(
					'<?php echo site_url('notifications/delete') ?>',
					{
						id: id,
						<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
					},
					function (data) {
						location.reload();
					}
				);
			}
		});
	});
</script>

<?php if (count($notifications)==0): ?>
<p style="text-align: center;">Nothing yet...</p>
<?php endif ?>

<?php foreach ($notifications as $notification): ?>
<div class="notif">
	<div class="notif_title" dir="auto">
		<?php echo $notification['title']; ?>
		<?php if ($type=="all"): ?>
		<div class="notif_meta">
		<?php elseif ($type=="latest"): ?>
		<span class="notif_meta" dir="ltr">
		<?php endif ?>
			<?php echo $notification['time'] ?>
			<?php if ($user_level >= 2): ?>
				<?php echo anchor('notifications/edit/' . $notification['id'], 'Edit') ?>
				<a href="#" id="<?php echo $notification['id'] ?>" class="delete_notif">Delete</a>
			<?php endif ?>
		<?php if ($type=="all"): ?>
		</div>
		<?php elseif ($type=="latest"): ?>
		</span>
		<?php endif ?>
	</div>
	<div class="notif_text<?php if ($type=="latest"){ echo ' latest'; } ?>" dir="auto">
		<?php
			if ($type=="all")
				echo $notification['text'];
			else if ($type=="latest"){
				$text = substr(trim(strip_tags($notification['text'])),0,200);
				$text = str_replace("&nbsp;",' ',$text);
				$text = str_replace("&#160;",' ',$text);
				echo $text;
			}
		?>
	</div>
</div>
<?php endforeach ?>