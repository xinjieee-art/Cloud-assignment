//hole
<?php

require '../_base.php';
if(is_post()){
global $_err;
$_err=[];


$user_id=req('user_id');
$slot_id=req('slot_id');
$booking_date=req('booking_date');
$room_id=req('room_id');


if(empty($user_id)){
	$_err['user_id'] = "Enter a ID is require";
}

elseif(strlen($user_id) > 10){
	$_err['user_id'] = "ID must not exceed 10 characters";
}

elseif(!ctype_alnum($user_id)){
	$_err['user_id'] = "ID must contain only letters and numbers";
}

elseif(!is_user($user_id)){
	$_err['user_id'] = "Invalid Id";
}


if(empty($slot_id)){
	$_err['slot_id'] = "Invalid. Please select";
}
if(empty($room_id)){
	$_err['room_id'] = "Invalid. Please select";
}
if(empty($booking_date)){
	$_err['booking_date'] = "Invalid. Please select date";
}



if(empty($_err))
{
  $stm=$_db->prepare("INSERT INTO reservation (user_id,slot_id,booking_date,status) VALUES(?,?,?,'confirm')");

	$success=$stm->execute([$user_id,$slot_id,$booking_date]);
  
	if($success){
		temp('info',"Reservation is successfully");
		redirect("/home.php");

	}else{
	temp('info',"Reservation unsuccessfully due to some error");
	}
}
}
$Room = $_db->query('SELECT room_id, name FROM room')->fetchAll(PDO::FETCH_OBJ);

$Slot = $_db->query('SELECT slot_id, start_time, end_time FROM time_slot ')->fetchAll(PDO::FETCH_OBJ);

include "/head.php";
$_title="Insert | reservation ";
?>
<form method="POST">
	
	<div>
		<label>Student Id</label>
		<input type="text" placeholder="Enter Id e.g 12PMD12345" name="user_id" maxlength="10" value="<?=req('user_id') ?> ">
		<?= err('user_id')?>
	</div>
	
	<div>
		<label>Which Room </label>
		<select id="roomSelect" name="room_id">
			<option value="">Select a room</option>
			<?php foreach ($Room as $r): ?>
			<option value="<?=$r->room_id?>" <?=req('room_id') == $r->room_id ? 'selected':'' ?>>
				<?=$r->name?>
			</option>
			<?php endforeach?>
		</select>
			<?= err('room_id')?>

	</div>

	<div>
		<label>Date</label>
		<input type="date" name="booking_date" value="<?=req('booking_date') ?>">
		<?= err('booking_date')?>
	</div>

	<div>
		<label>Time</label>
		<select name="slot_id">
			<option value="">Select Time</option>
			<?php foreach ($Slot as $s): ?>
			<option value="<?= $s->slot_id?>" <?=req('slot_id') == $s->slot_id ? 'selected' : '' ?>>
				<?= $s->start_time?> --- <?= $s->end_time ?>  
			</option>
			<?php endforeach?>
		</select>
		<?= err('slot_id')?>
	</div>
				<section>
					<button type="submit">Submit</button>
				</section>
  
</form>
<?php
include '../_foot.php';