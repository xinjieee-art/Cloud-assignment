<?php
require __DIR__ . '/../_base.php';
auth('admin', 'staff');

$id = req('id');

if (!is_exists($id, 'time_slot', 'slot_id')) {
    temp('info', 'Time slot not found');
    redirect('/slot/details.php');
}

$stm = $_db->prepare('DELETE FROM time_slot WHERE slot_id = ?');
$stm->execute([$id]);

temp('info', 'Time slot deleted');
redirect('/slot/details.php');