<?php 
require "../_base.php";
auth('user');
if(is_post()){
global $_err;
$_err=[];
$user_id=req('user_id');
$slot_id=req('slot_id');
$booking_date=req('booking_date');
$room_id=req('room_id');

if($user_id == ''){
    $_err['user_id'] = "Require";
}
else if(!is_user($user_id)){
	$_err['user_id'] = "Invalid Id";
}
elseif(!is_exists($user_id, 'user', 'user_id')){
	$_err['user_id'] = "Student not found";
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
		$reservation_id = $_db->lastInsertId();

		$stm2 = $_db->prepare("INSERT INTO reservation_room (room_id, reservation_room) VALUES (?, ?)");
		$stm2->execute([$room_id, $reservation_id]);

		temp('info',"Reservation is successfully");
		redirect("/page/home.php");

	}else{
	temp('info',"Reservation unsuccessfully due to some error");
	}
}
}

$Room = $_db->query("SELECT room_id, name FROM room WHERE status = 'available'")->fetchAll(PDO::FETCH_OBJ);

$Slot = $_db->query('SELECT slot_id, start_time, end_time FROM time_slot ')->fetchAll(PDO::FETCH_OBJ);
$_title="Booking Reservation";
include '../_head.php';
?>
<div class="booking-card">
    <h2>Booking Reservation</h2>

    <form method="post" class="form-reserve">
        <div class="form-grid">
            <div class="form-group">
                <label for="user_id">Student ID</label>
                <input type="text" id="user_id" placeholder="Enter Id e.g 12PMD12345" name="user_id" maxlength="10" value="<?= htmlspecialchars(trim(req('user_id'))) ?>">
                <?= err('user_id') ?>
            </div>

            <div class="form-group">
                <label for="roomSelect">Which Room</label>
                <select id="roomSelect" name="room_id">
                    <option value="">Select a room</option>
                    <?php foreach ($Room as $r): ?>
                        <option value="<?= $r->room_id ?>" <?= req('room_id') == $r->room_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r->name) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= err('room_id') ?>
            </div>

            <div class="form-group">
                <label for="booking_date">Booking Date</label>
                <input type="date" id="booking_date" name="booking_date" value="<?= htmlspecialchars(req('booking_date')) ?>">
                <?= err('booking_date') ?>
            </div>

            <div class="form-group">
                <label for="slot_id">Time</label>
                <select id="slot_id" name="slot_id">
                    <option value="">Select Time</option>
                    <?php foreach ($Slot as $s): ?>
                        <option value="<?= $s->slot_id ?>" <?= req('slot_id') == $s->slot_id ? 'selected' : '' ?>>
                            <?= date('g:i A', strtotime($s->start_time)) ?> - <?= date('g:i A', strtotime($s->end_time)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= err('slot_id') ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit">Submit</button>
                <button type="reset" class="btn-reset">Reset</button>
            </div>
        </div>
    </form>
</div>
<?php
include '../_foot.php';