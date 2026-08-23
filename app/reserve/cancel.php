<?php
require __DIR__ . '/../_base.php';
auth('admin', 'staff');

$id = get('id');

if (ctype_digit((string)$id)) {
    // Check current status first
    $stm = $_db->prepare("SELECT status FROM reservation WHERE reservation_id = ?");
    $stm->execute([$id]);
    $current = $stm->fetchColumn();

    // Toggle: confirm -> cancel, cancel -> confirm
    $new_status = ($current == 'cancel') ? 'confirm' : 'cancel';

    $stm = $_db->prepare("UPDATE reservation SET status = ? WHERE reservation_id = ?");
    $stm->execute([$new_status, $id]);

    temp('info', $new_status == 'cancel' ? 'Reservation cancelled.' : 'Reservation restored.');
}

redirect('/reserve/details.php');