<?php 
require '../_base.php';
auth('user');
	$user_id = $_user->user_id;

	$stm=$_db->prepare('SELECT 
	res.reservation_id,res.booking_date,
	res.user_id,
	res.status,
	rm.name AS room_name, 
	ts.start_time, ts.end_time
	FROM reservation res
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

<?php if (empty($shows)): ?>
	<p>No record yet</p>
<?php else: ?>
		<div class="table-container">
			<table>
				<thead>
					<tr>
						<th>Reservation ID</th>
						<th>Student ID</th>
						<th>Room Name</th>
						<th>Time</th>
						<th>Status</th>
						<th>Booking Date</th>
					</tr>
				</thead>
					<tbody>
						<?php foreach($shows as $show):?>
						<tr>
							<td><?=htmlspecialchars($show['reservation_id'])?></td>
							<td><?= htmlspecialchars($show['user_id']) ?></td>
							<td><?= htmlspecialchars($show['room_name']) ?></td>
							<td><?= htmlspecialchars(date('g:i A', strtotime
							($show['start_time'] )))?> - <?=htmlspecialchars(date('g:i A',strtotime($show['end_time'])))?></td>
							<?php
								$display_status = $show['status'];
								if ($display_status === 'confirm' && $show['booking_date'] < date('Y-m-d')) {
									$display_status = 'completed';
								}

								$status_color = match ($display_status) {
									'confirm'   => 'green',
									'completed' => '#6b7280',
									'cancel'    => 'red',
									default     => 'black',
								};
							?>
							<td style="color:<?= $status_color ?>; font-weight:bold;">
								<?= htmlspecialchars($display_status) ?>
							</td>
							<td><?= htmlspecialchars(($show['booking_date']))?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
			
			</table>
		</div>
<?php endif; ?>

<?php include '../_foot.php'; ?>