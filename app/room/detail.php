<?php 
require '../_base.php';

?>

<?php 
$user_id = req('user_id');

if(empty($user_id)){
	temp("Sorry, you don't have the any record yet");
	$shows = [];
}else{
	$stm=$_db->prepare("SELECT r.reservation_id,r.user_id r.start_time,
	r.end_time, r.booking_date, rm.name AS room_name,
	rm.description, rm.status AS room_status,
	rm.capacity FROM reservation r 
	JOIN room rm ON r.room_id = rm.room_id 
	WHERE r.user_id=?
");

	$stm->execute([$user_id]);

	$shows=$stm->fetchAll(PDO::FETCH_ASSOC);
}

$_title = "Room Detail";
include '../_head.php';

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
		<label> Room Status:</label>
		<span style="color:<?= $show['room_status'] === 'confirm' ? 'green' : 'red' ?>;">
		<?= $show['room_status']?>
		</span>
		</div>

		<div>
		<label> Room Description:</label>
		<span>
			<?= $show['description']?>
		</span>
		</div>

		<div>
		<label> Room Capacity:</label>
		<span>
			<?= $show['capacity']?>
		</span>
		</div>

		<div>
		<label>Time:</label>
		<?= $show['start_time'] ?> - <?=$show['end_time']?>
		</div>

		<div>
		<label>Booking Date:</label>
		<?= $show['booking_date']?>
		</div>

		
	</div>
<?php endforeach ?>
<?php include '../_foot.php'; ?>
