<?php
/**
 * Sharif Judge online judge
 * @file users.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<?php $this->view('templates/top_bar'); ?>
<?php $this->view('templates/side_bar',array('selected'=>'users')); ?>

<div id="main_container">

	<div id="page_title">
		<img src="<?php echo base_url('assets/images/icons/users.png') ?>"/>
		<span><?php echo $title ?></span>
		<span class="title_menu_item"><a href="http://docs.sharifjudge.ir/users" target="_blank"><i class="splashy-help"></i> Help</a></span>
		<span class="title_menu_item"><a href="<?php echo site_url('users/add') ?>"><i class="splashy-group_blue_add"></i> Add Users</a></span>
		<span class="title_menu_item"><a href="<?php echo site_url("users/list_excel") ?>"><i class="splashy-document_small_download"></i> Excel</a></span>
	</div>

	<div id="main_content">
		<?php if (isset($deleted_user) && $deleted_user===TRUE): ?>
			<p class="shj_ok">User deleted successfully.</p>
		<?php endif ?>
		<?php if (isset($deleted_submissions) && $deleted_submissions===TRUE): ?>
			<p class="shj_ok">Submissions of selected user deleted successfully.</p>
		<?php endif ?>
		<div style="height:15px"></div>
		<table class="sharif_table">
			<thead>
			<tr><th>#</th><th>User ID</th><th>Username</th><th>Display Name</th><th>Email</th><th>Role</th><th>First Login</th><th>Last Login</th><th>Actions</th></tr>
			</thead>
			<?php $counter = 0; ?>
			<?php foreach($users as $user): ?>
				<tr>
					<td><?php echo ++$counter; ?></td>
					<td><?php echo $user['id'] ?></td>
					<td><?php echo $user['username'] ?></td>
					<td><?php echo $user['display_name'] ?></td>
					<td><?php echo $user['email'] ?></td>
					<td><?php echo $user['role'] ?></td>
					<td><?php echo $user['first_login_time']==='0000-00-00 00:00:00'?'Never':$user['first_login_time'] ?></td>
					<td><?php echo $user['last_login_time']==='0000-00-00 00:00:00'?'Never':$user['last_login_time'] ?></td>
					<td>
						<a title="Edit" href="<?php echo site_url('profile/'.$user['id']) ?>"><i class="splashy-contact_blue_edit"></i></a>
						<a title="Delete User" href="<?php echo site_url('users/delete/'.$user['id']) ?>"><i class="splashy-contact_blue_remove"></i></a>
						<a title="Delete Submissions" href="<?php echo site_url('users/delete_submissions/'.$user['id']) ?>"><i class="splashy-documents_remove"></i></a>
					</td>
				</tr>
			<?php endforeach ?>
		</table>

	</div> <!-- main_content -->

</div> <!-- main_container -->