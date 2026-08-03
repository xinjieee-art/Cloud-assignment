<?php
require __DIR__ . '/../_base.php';
auth('admin', 'staff');

$id = req('id');

if (!is_exists($id, 'room', 'room_id')) {
    temp('info', 'Room not found');
    redirect('/room/detail.php');
}

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM room WHERE room_id = ?');
    $stm->execute([$id]);
    $r = $stm->fetch();
    extract((array)$r);
}

if (is_post()) {
    $name        = req('name');
    $description = req('description');
    $capacity    = req('capacity');
    $status      = req('status');

    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    if ($description == '') {
        $_err['description'] = 'Required';
    }

    if ($capacity == '') {
        $_err['capacity'] = 'Required';
    }
    else if (!ctype_digit($capacity) || $capacity <= 0) {
        $_err['capacity'] = 'Must be a positive number';
    }

    if ($status == '') {
        $_err['status'] = 'Required';
    }

    if (!$_err) {
        $stm = $_db->prepare('
            UPDATE room
            SET name = ?, description = ?, capacity = ?, status = ?
            WHERE room_id = ?
        ');
        $stm->execute([$name, $description, $capacity, $status, $id]);

        temp('info', 'Room updated');
        redirect('/room/detail.php');
    }
}

$_title = 'Room';
require __DIR__ . '/../admin/_head.php';
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php" class="active">Room</a>
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
        <h2 style="margin-top:0;">Update Room</h2>

        <form method="post" class="auth-form" style="max-width:420px; width:100%;">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <label for="name" class="sr-only">Room name</label>
            <?= html_text('name', 'placeholder="Room name" maxlength="100"') ?>
            <?= err('name') ?>

            <label for="description" class="sr-only">Description</label>
            <textarea name="description" placeholder="Description" rows="3" style="width:100%; padding:10px; border-radius:8px; border:1px solid #ccc;"><?= htmlspecialchars($description ?? '') ?></textarea>
            <?= err('description') ?>

            <label for="capacity" class="sr-only">Capacity</label>
            <?= html_number('capacity', 1, '', 1, 'placeholder="Capacity"') ?>
            <?= err('capacity') ?>

            <label for="status" class="sr-only">Status</label>
            <?= html_select('status', ['available' => 'Available', 'unavailable' => 'Unavailable'], null) ?>
            <?= err('status') ?>

            <button type="submit" class="auth-btn-primary">Save changes</button>
            <p class="auth-footer-text">
                <a href="/room/detail.php" class="auth-link">Back to room list</a>
            </p>
        </form>
    </section>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>