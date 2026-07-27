<?php 
require '../_base.php';
$shows=[];
auth('user');
	$user_id = req('user_id') ?? $_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? null;

	$stm=$_db->prepare('SELECT 
	res.reservation_id,res.booking_date,
	res.status, u.user_id, u.name AS user_name,
	rm.room_id, rm.name AS room_name, 
	ts.start_time, ts.end_time
	FROM reservation res
	LEFT JOIN user u ON u.user_id = res.user_id
	LEFT JOIN time_slot ts ON ts.slot_id = res.slot_id
	LEFT JOIN reservation_room rr ON rr.reservation_room = res.reservation_id
	LEFT JOIN room rm ON rm.room_id = rr.room_id
	WHERE res.user_id = ?
	ORDER BY res.booking_date DESC
');
	$stm->execute([$user_id]);
	$shows=$stm->fetchAll(PDO::FETCH_ASSOC);
	

$_title = "Room History";
include '../_head.php';
?>

<?php if(empty($shows)): 
	temp('info','No record yet');
	redirect('home.php');
	?>
	<?php else:?>
	<table>
		<thead>
			<tr>
				<th>Reservation ID</th>
				<th>Student ID</th>
				<th>Student Name</th>
				<th>Room Name</th>
				<th>Status</th>
				<th>Time</th>
				<th>Booking Date</th>
			</tr>
		</thead>
			<tbody>
				<?php foreach($shows as $show):?>
				<tr>
					<td><?=htmlspecialchars($show['reservation_id'])?></td>
					<td><?= htmlspecialchars($show['user_id']) ?></td>
					<td><?= htmlspecialchars($show['user_name']) ?></td>
					<td><?= htmlspecialchars($show['room_name']) ?></td>
					<td style="color:<?=$show['status'] === 'confirm' ? 'green' : 'red' ?>; font-weight:bold;">
					<?= htmlspecialchars($show['status'])?></td>
					<td><?= htmlspecialchars(date('g:i A', strtotime
					($show['start_time'] )))?> - <?=htmlspecialchars(date('g:i A',strtotime($show['end_time'])))?></td>
					<td><?= htmlspecialchars($show['booking_date'])?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
	
</table>

<?php endif; ?>
<?php include '../_foot.php'; ?>