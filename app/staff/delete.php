<?php
require __DIR__ . '/../_base.php';
auth('admin');

$id = req('id');

if (!is_exists($id, 'staff', 'staff_id')) {
    temp('info', 'Staff not found');
    redirect('/staff/details.php');
}

$stm = $_db->prepare('SELECT * FROM staff WHERE staff_id = ?');
$stm->execute([$id]);
$s = $stm->fetch();

if ($s->profile && $s->profile != 'user.png') {
    @unlink("../images/profile/{$s->profile}");
}

$stm = $_db->prepare('DELETE FROM staff WHERE staff_id = ?');
$stm->execute([$id]);

temp('info', 'Staff deleted');
redirect('/staff/details.php');