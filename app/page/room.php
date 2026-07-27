<?php 
require '../_base.php';

auth('user');


	$stm=$_db->prepare("SELECT res.reservation_id,res.user_id ,
	res.booking_date, ts.start_time, ts.end_time, u.user_id, u.name AS user_name,rm.name AS room_name,
	rm.description, rm.status AS room_status,
	rm.capacity 
	FROM reservation res 
	JOIN reservation_room r ON res.reservation_id = r.reservation_room
	JOIN room rm ON r.room_id = rm.room_id 
	JOIN time_slot ts ON res.slot_id = ts.slot_id 
	LEFT JOIN user u ON res.user_id = u.user_id
	WHERE r.user_id=?
	ORDER BY res.booking_date DESC 
");
	$shows=$stm->fetchAll(PDO::FETCH_ASSOC);


$_title = "Room History";
include '../_head.php';
?>

<?php if(empty($shows)): 
	temp('info','No record yet');
	?>
	<?php else:?>
		<?php foreach($shows as $show):?>
	<table>
		<thead>
			<tr>
				<th>Reservation ID:</th>
				<th>Student ID:</th>
				<th>Room name:</th>
				<th>Room Status:</th>
				<th>Room Description:</th>
				<th>Room Capacity:</th>
				<th>Time:</th>
				<th>Booking Date:</th>
			</tr>
		</thead>
			<tbody>
				<tr>
					<td><?=htmlspecialchars($show['reservation_id'])?></td>
					<td><?= htmlspecialchars($show['user_id']) ?></td>
					<td><?= htmlspecialchars($show['room_name']) ?></td>
					<td style="<?=$show['room_status'] === 'confirm' ? 'green' : 'red' ?>">
					<?= htmlspecialchars($show['room_status'])?></td>
					<td> <?=htmlspecialchars($show['description'])?></td>
					<td><?= htmlspecialchars($show['capacity'])?></td>
					<td><?= htmlspecialchars($show['start_time'] )?> - <?=htmlspecialchars($show['end_time'])?></td>
					<td><?= htmlspecialchars($show['booking_date'])?></td>
				</tr>
				<?php endforeach ?>
			</tbody>
	
</table>

<?php endif ?>
<?php include '../_foot.php'; ?>