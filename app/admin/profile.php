<?php
include '../_base.php';
 
// ----------------------------------------------------------------------------
// Authenticated admin only
auth('admin');

 
if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM admin WHERE admin_id=?');
    $stm->execute([$_user->admin_id]);
    $u = $stm->fetch();

    if (!$u) {
        redirect('/');
    }

    extract((array)$u);
    $_SESSION['profile'] = $u->profile;
}
 
// update profile
if (is_post()) {
    $email   = req('email');
    $name    = req('name');
    $gender  = req('gender');
    $profile = $_SESSION['profile'];
    $f       = get_file('photo'); 
 
    // Validate: email
    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else {
        $stm = $_db->prepare('
            SELECT COUNT(*) FROM admin
            WHERE email=? AND admin_id != ?
        ');
        $stm->execute([$email, $_user->admin_id]);
 
        if ($stm->fetchColumn() > 0) {
            $_err['email'] = 'Duplicated';
        }
    }
 
    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }
 
    if ($gender == '') {
        $_err['gender'] = 'Required';
    }
 
    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['profile'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['profile'] = 'Maximum 1MB';
        }
    }
 
    // DB operation
    if (!$_err) {
        if ($f) {
            if ($profile) {
                unlink("../photos/$profile");
            }
            $profile = save_photo($f, '../photos');
        }

        // (2) Update admin (email, name, gender, profile)
        $stm = $_db->prepare('
            UPDATE admin
            SET email = ?, name = ?, gender = ?, profile = ?
            WHERE admin_id = ?
        ');
        $stm->execute([$email, $name, $gender, $profile, $_user->admin_id]);

        // (3) Update global user object + session profile
        $_user->email   = $email;
        $_user->name    = $name;
        $_user->gender  = $gender;
        $_user->profile = $profile;
        $_SESSION['profile'] = $profile;

        temp('info', 'Record updated');
        redirect('/');
    }
}
 
// ----------------------------------------------------------------------------
 
$_title = 'Admin | Profile';
include '../_head.php';
?>
 
<form method="post" class="form" enctype="multipart/form-data">
    <label for="email">Email</label>
    <?= html_text('email', 'maxlength="100"') ?>
    <?= err('email') ?>
 
    <label for="name">Name</label>
    <?= html_text('name', 'maxlength="100"') ?>
    <?= err('name') ?>
 
    <label>Gender</label>
    <?= html_radios('gender', ['M' => 'Male', 'F' => 'Female']) ?>
    <?= err('gender') ?>
 
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/photos/<?= $profile ?>">
    </label>
 
    <section>
        <button>Submit</button>
        <button type="reset">Reset</button>
    </section>
</form>
 
<?php
include '../_foot.php';