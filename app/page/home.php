<?php
require '../_base.php';
// ----------------------------------------------------------------------------

if (is_post()) {
    temp('info', 'This action is redirected from Demo 1');
    redirect('/'); // index.php, () stay at same page
}

// ----------------------------------------------------------------------------
$_title = 'Page | Demo 1';
include '../_head.php';
?>

<form method="post">
    <button>Submit</button>
</form>

<?php
include '../_foot.php';