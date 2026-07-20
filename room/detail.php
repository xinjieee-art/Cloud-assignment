<?php 
require '../_base.php';
include '../_header.php';

$_title="Room Detail";
?>



<?php 
$user_id = req('user_id');

if(empty($user_id)){
	temp("Sorry, you don't have the any record yet");
	$shows = [];
}else{
	$stm=$_db->prepare("SELECT r.reservation_id,r.user_id,r.booking_date,r.status,t.start_time,t.end_time,rm.name AS room_name FROM reservation r 
INNER JOIN time_slot t ON r.slot_id = t.slot_id
INNER JOIN reservation_room rr ON rr.reservation_room = r.reservation_id
INNER JOIN room rm ON rr.room_id = rm.room_id
WHERE user_id=? 
");

	$stm->execute([$user_id]);

	$shows=$stm->fetchAll(PDO::FETCH_ASSOC);
}


?>

<?php foreach($shows as $show): ?>
	<div>
		<div>
		<label>Reservation ID:</label>
		<?=$show['reservation_id'] ?>
		</div>

		<div>
		<label>Student ID:</label>
		<?= $show['user_id']; ?>
		</div>
		
		<div>
		<label>Room name:</label>
		<?= $show['room_name'] ?>
		</div>


		<div>
		<label>Time:</label>
		<?= $show['start_time'] ?> - <?=$show['end_time']?>
		</div>

		<div>
		<label>Booking Date:</label>
		<?= $show['booking_date']?>
		</div>

		<div>
		<label>Status:</label>
		<span style="color:<?= $show['status'] === 'confirm' ? 'green' : 'red' ?>;">
		<?= $show['status']?>
		</span>
		</div>
	</div>
<?php endforeach ?>
<?php include '../_foot.php'; ?>
