<?php 
require '../_base.php';

auth('user');

	$stm=$_db->prepare("SELECT * FROM room");
	$stm->execute();
	$shows=$stm->fetchAll();
    

$_title = "Room";
include '../_head.php';
?>

<?php if($shows == ''): 
	temp('info','No record yet');
	redirect('home.php');
	?>
	<?php else:?>
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
<?php endif;?>
<?php include '../_foot.php'; ?>