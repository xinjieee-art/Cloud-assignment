<?php
require '_base.php';

if ($_user) {
    $url = in_array($_user->role, ['admin', 'staff']) ? '/admin/home.php' : '/page/home.php';
    redirect($url);
}
else {
    redirect('/page/home.php');
}