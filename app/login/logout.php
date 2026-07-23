<?php
    include '../_base.php';
    include '../_head.php';

    auth('member');
    // ----------------------------------------------------------------------------

    temp('info', 'Logout successfully');
    logout('/login/login.php'); 

    // ----------------------------------------------------------------------------
?>