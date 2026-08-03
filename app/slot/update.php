<?php
require __DIR__ . '/../_base.php';
auth('admin', 'staff');

$id = req('id');

if (!is_exists($id, 'time_slot', 'slot_id')) {
    temp('info', 'Time slot not found');
    redirect('/slot/details.php');
}

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM time_slot WHERE slot_id = ?');
    $stm->execute([$id]);
    $s = $stm->fetch();
    extract((array)$s);
}

if (is_post()) {
    $start_time = req('start_time');
    $end_time   = req('end_time');

    if ($start_time == '') {
        $_err['start_time'] = 'Required';
    }

    if ($end_time == '') {
        $_err['end_time'] = 'Required';
    }
    else if ($start_time != '' && $end_time <= $start_time) {
        $_err['end_time'] = 'Must be after start time';
    }

    if (!$_err) {
        $stm = $_db->prepare('
            UPDATE time_slot
            SET start_time = ?, end_time = ?
            WHERE slot_id = ?
        ');
        $stm->execute([$start_time, $end_time, $id]);

        temp('info', 'Time slot updated');
        redirect('/slot/details.php');
    }
}

$_title = 'Edit Time Slot';
require __DIR__ . '/../admin/_head.php';
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/slot/details.php" class="active">Time Slot</a>
        <a href="/reserve/details.php">Booking reservation</a>
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

    <section class="admin-content" style="display:flex; flex-direction:column; align-items:center; justify-content:center; min-height:calc(100vh - 220px);">
        <h2 style="margin-top:0;">Edit Time Slot</h2>

        <form method="post" class="auth-form" style="max-width:420px; width:100%;">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <label for="start_time" class="sr-only">Start time</label>
            <input type="time" name="start_time" value="<?= $start_time ?? '' ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
            <?= err('start_time') ?>

            <label for="end_time" class="sr-only">End time</label>
            <input type="time" name="end_time" value="<?= $end_time ?? '' ?>" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;">
            <?= err('end_time') ?>

            <button type="submit" class="auth-btn-primary">Save changes</button>
            <p class="auth-footer-text">
                <a href="/slot/details.php" class="auth-link">Back to time slot list</a>
            </p>
        </form>
    </section>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>