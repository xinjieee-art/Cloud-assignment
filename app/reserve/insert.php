<?php
require '../_base.php';
auth('admin', 'staff');

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
		redirect("/reserve/details.php");

	}else{
	temp('info',"Reservation unsuccessfully due to some error");
	}
}
}
$Room = $_db->query("SELECT room_id, name FROM room WHERE status = 'available'")->fetchAll(PDO::FETCH_OBJ);

$Slot = $_db->query('SELECT slot_id, start_time, end_time FROM time_slot ')->fetchAll(PDO::FETCH_OBJ);

$_title = "";
require __DIR__ . '/../admin/_head.php';
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/reserve/details.php" class="active">Booking reservation</a>
        <?php if (($_user->role ?? '') == 'admin'): ?>
            <a href="/staff/details.php">Staff</a>
        <?php endif ?>
        <a href="/logout.php">Logout</a>

        <?php $profileUrl = ($_user->role ?? '') == 'admin' ? '/admin/profile.php' : '/staff/profile.php'; ?>
        <a href="<?= $profileUrl ?>" class="sidebar-user">
            <img src="/images/profile/<?= $_user->profile ?>">
            <div><?= htmlspecialchars($_user->name) ?></div>
        </a>
    </aside>

    <section class="admin-content" style="display:flex; flex-direction:column; align-items:center;">
        <h2 style="margin-top:0;">New Booking Reservation</h2>

        <form method="post" class="auth-form" style="max-width:420px; width:100%;">

            <label for="user_id" class="sr-only">Student Id</label>
            <input type="text" id="user_id" name="user_id"
                   placeholder="Enter Id e.g 1212345" maxlength="10"
                   value="<?= req('user_id') ?>">
            <?= err('user_id') ?>

            <label for="roomSelect" class="sr-only">Which Room</label>
            <select id="roomSelect" name="room_id">
                <option value="">Select a room</option>
                <?php foreach ($Room as $r): ?>
                <option value="<?=$r->room_id?>" <?=req('room_id') == $r->room_id ? 'selected':'' ?>>
                    <?=$r->name?>
                </option>
                <?php endforeach?>
            </select>
            <?= err('room_id') ?>

            <label for="booking_date" class="sr-only">Booking Date</label>
            <input type="date" id="booking_date" name="booking_date" value="<?=req('booking_date') ?>">
            <?= err('booking_date') ?>

            <label for="slot_id" class="sr-only">Time</label>
            <select id="slot_id" name="slot_id">
                <option value="">Select Time</option>
                <?php foreach ($Slot as $s): ?>
                <option value="<?= $s->slot_id?>" <?=req('slot_id') == $s->slot_id ? 'selected' : '' ?>>
                    <?= $s->start_time?> --- <?= $s->end_time ?>
                </option>
                <?php endforeach?>
            </select>
            <?= err('slot_id') ?>

            <button type="submit" class="auth-btn-primary">Submit</button>
            <button type="reset" class="auth-link-muted" style="text-align:center; margin-top:8px;">Reset</button>
        </form>
    </section>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>