<?php
require __DIR__ . '/../_base.php';
auth('admin', 'staff');

$id = get('id');

if (ctype_digit((string)$id)) {
    $stm = $_db->prepare("UPDATE reservation SET status = 'cancel' WHERE reservation_id = ?");
    $stm->execute([$id]);
}

temp('info', 'Reservation cancelled.');
redirect('/reserve/details.php');