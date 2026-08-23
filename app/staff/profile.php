<?php
include '../_base.php';
 
// ----------------------------------------------------------------------------
// Authenticated staff only
auth('staff');

 
if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM staff WHERE staff_id=?');
    $stm->execute([$_user->staff_id]);
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
            SELECT COUNT(*) FROM staff
            WHERE email=? AND staff_id != ?
        ');
        $stm->execute([$email, $_user->staff_id]);
 
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
            $profile = save_photo($f, '../images/profile');
        }

        $stm = $_db->prepare('
            UPDATE staff
            SET email = ?, name = ?, gender = ?, profile = ?
            WHERE staff_id = ?
        ');
        $stm->execute([$email, $name, $gender, $profile, $_user->staff_id]);

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
 
$_title = 'Staff | Profile';
include '../_head.php';
?>
 
<div class="auth-card">
    <p class="auth-card__eyebrow">Manage your account</p>
    <h2 class="auth-card__title">Staff Profile</h2>

    <form method="post" class="auth-form" enctype="multipart/form-data">
        <label class="auth-photo-upload" tabindex="0">
            <?= html_file('photo', 'image/*', 'hidden') ?>
            <?php $photoSrc = $profile ? "/images/profile/$profile" : '/images/user.png'; ?>
            <img src="<?= $photoSrc ?>" id="photoPreview">
        </label>

        <label for="name" class="sr-only">Name</label>
        <?= html_text('name', 'placeholder="Full name" maxlength="100"') ?>
        <?= err('name') ?>

        <label for="email" class="sr-only">Email</label>
        <?= html_text('email', 'placeholder="Email address" maxlength="100"') ?>
        <?= err('email') ?>

        <div class="auth-form__row">
            <label><input type="radio" name="gender" value="M" <?= ($gender ?? '') == 'M' ? 'checked' : '' ?>> Male</label>
            <label><input type="radio" name="gender" value="F" <?= ($gender ?? '') == 'F' ? 'checked' : '' ?>> Female</label>
        </div>
        <?= err('gender') ?>

        <button class="auth-btn-primary">Save changes</button>
        <button type="reset" class="auth-btn-secondary">Reset</button>
    </form>
</div>

<script>
$(document).on('change', 'input[name="photo"]', function (e) {
    const file = e.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (event) {
        $('#photoPreview').attr('src', event.target.result);
    };
    reader.readAsDataURL(file);
});
</script>
 
<?php
include '../_foot.php';