<?php 
require '../_base.php';

auth('user');

	$stm=$_db->prepare("SELECT * FROM room");
	$stm->execute();
	$shows=$stm->fetchAll();
    

$_title = "Room";
include '../_head.php';
?>

<?php if (empty($shows)): ?>
	<p>No record yet</p>
<?php else: ?>
	<div style="max-width: 1100px; margin: 30px auto; padding: 30px; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
		<table>
			<thead>
				<tr>
					<th>Room name</th>
					<th>Room Status</th>
					<th>Room Description</th>
					<th>Room Capacity</th>
					<th>Action</th>
				</tr>
			</thead>

			<tbody>
				<?php foreach($shows as $r):?>
				<tr>
					<td><?= $r->name ?></td>
					<td><?= $r->status?></td>
					<td><?= $r->description?></td>
					<td><?= $r->capacity?></td>
					<td>
						<?php if ($r->status == 'available'): ?>
							<a href="/page/booking.php?room_id=<?= $r->room_id ?>">Book</a>
						<?php else: ?>
							—
						<?php endif ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif;?>
<?php include '../_foot.php'; ?>