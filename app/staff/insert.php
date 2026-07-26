<?php
require __DIR__ . '/../_base.php';
auth('admin');

if (is_post()) {
    $name     = req('name');
    $email    = req('email');
    $gender   = req('gender');
    $password = req('password');
    $confirm  = req('confirm');
    $f = get_file('photo');

    if ($name == '') {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    if ($email == '') {
        $_err['email'] = 'Required';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_unique($email, 'staff', 'email')) {
        $_err['email'] = 'Duplicated';
    }

    if ($gender == '') {
        $_err['gender'] = 'Required';
    }

    if ($password == '') {
        $_err['password'] = 'Required';
    }
    else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    if ($confirm == '') {
        $_err['confirm'] = 'Required';
    }
    else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    if ($f) {
        if (!str_starts_with($f->type, 'image/')) {
            $_err['photo'] = 'Must be image';
        }
        else if ($f->size > 1 * 1024 * 1024) {
            $_err['photo'] = 'Maximum 1MB';
        }
    }

    if (!$_err) {
        $profile = $f ? save_photo($f, '../images/profile') : 'user.png';

        $stm = $_db->prepare('
            INSERT INTO staff (name, email, gender, password, profile)
            VALUES (?, ?, ?, SHA1(?), ?)
        ');
        $stm->execute([$name, $email, $gender, $password, $profile]);

        temp('info', 'Staff account created');
        redirect('/staff/details.php');
    }
}

$_title = 'Add Staff';
include '../_head.php';
?>

<div class="auth-card">
    <p class="auth-card__eyebrow">Staff management</p>
    <h2 class="auth-card__title">Add New Staff</h2>

    <form method="post" class="auth-form" enctype="multipart/form-data">
        <label class="auth-photo-upload" tabindex="0">
            <?= html_file('photo', 'image/*', 'hidden') ?>
            <img src="/images/user.png" id="photoPreview">
        </label>
        <?= err('photo') ?>

        <label for="name" class="sr-only">Name</label>
        <?= html_text('name', 'placeholder="Full name" maxlength="100"') ?>
        <?= err('name') ?>

        <label for="email" class="sr-only">Email</label>
        <?= html_text('email', 'placeholder="Email address" maxlength="100"') ?>
        <?= err('email') ?>

        <div class="auth-form__row">
            <label><input type="radio" name="gender" value="M"> Male</label>
            <label><input type="radio" name="gender" value="F"> Female</label>
        </div>
        <?= err('gender') ?>

        <label for="password" class="sr-only">Password</label>
        <?= html_password('password', 'class="pass-input" placeholder="Password" maxlength="100"') ?>
        <?= err('password') ?>

        <label for="confirm" class="sr-only">Confirm password</label>
        <?= html_password('confirm', 'class="pass-input" placeholder="Confirm password" maxlength="100"') ?>
        <?= err('confirm') ?>

        <div class="auth-form__row">
            <a href="javascript:void(0)"
               id="allToggleBtn"
               onclick="toggleAllPasswords()"
               class="auth-link-muted">
                Show All Passwords
            </a>
        </div>

        <button class="auth-btn-primary">Create Staff</button>

        <p class="auth-footer-text">
            <a href="/staff/details.php" class="auth-link">Back to staff list</a>
        </p>
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

function toggleAllPasswords() {
    const $inputs = $('.pass-input');
    const $btn = $('#allToggleBtn');
    const isHidden = $inputs.attr('type') === 'password';

    $inputs.attr('type', isHidden ? 'text' : 'password');
    $btn.text(isHidden ? "Hide All Passwords" : "Show All Passwords");
}
</script>

<?php include '../_foot.php'; ?>