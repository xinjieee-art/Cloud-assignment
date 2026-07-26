<?php
require __DIR__ . '/../_base.php';
auth('admin', 'staff');

$id = req('id');

if (!is_exists($id, 'room', 'room_id')) {
    temp('info', 'Room not found');
    redirect('/room/detail.php');
}

$stm = $_db->prepare('DELETE FROM room WHERE room_id = ?');
$stm->execute([$id]);

temp('info', 'Room deleted');
redirect('/room/detail.php');