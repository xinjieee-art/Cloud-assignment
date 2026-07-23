<?php
include '../_base.php';
$_title = 'Register';
include '../_head.php';

// ----------------------------------------------------------------------------

if (is_post()) {
    $name     = req('username');
    $phone    = req('phone');
    $email    = req('email');
    $password = req('password');
    $confirm  = req('confirm');
    $f = get_file('photo');

    if (!$email) {
        $_err['email'] = 'Required';
    }
    else if (strlen($email) > 100) {
        $_err['email'] = 'Maximum 100 characters';
    }
    else if (!is_email($email)) {
        $_err['email'] = 'Invalid email';
    }
    else if (!is_unique($email, 'member', 'email')) {
        $_err['email'] = 'Duplicated';
    }

    if (!$password) {
        $_err['password'] = 'Required';
    }
    else if (strlen($password) < 5 || strlen($password) > 100) {
        $_err['password'] = 'Between 5-100 characters';
    }

    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
        $_err['confirm'] = 'Between 5-100 characters';
    }
    else if ($confirm != $password) {
        $_err['confirm'] = 'Not matched';
    }

    if (!$name) {
        $_err['name'] = 'Required';
    }
    else if (strlen($name) > 100) {
        $_err['name'] = 'Maximum 100 characters';
    }

    if (!$phone) {
        $_err['phone'] = 'Required';
    }
    else if (!preg_match ('/^01\d{8,9}$/', $phone)) {
        $_err['phone'] = 'Invalid phone number';
    }

    if (!$f) {
        $_err['photo'] = 'Required';
    }
    else if (!str_starts_with($f->type, 'image/')) {
        $_err['photo'] = 'Must be image';
    }
    else if ($f->size > 1 * 1024 * 1024) {
        $_err['photo'] = 'Maximum 1MB';
    }

    if (!$_err) {

        $photo = save_photo($f, '../images/user','/images/user');
        
        $stm = $_db->prepare('
            INSERT INTO member (name, email, password, phone_number, image_url)
            VALUES (?, ?, SHA1(?), ?, ?)
        ');
        $stm->execute([$name, $email, $password, $phone, $photo]);

        temp('info', 'Registered! Please login.');
        redirect('/login/login.php');
    }
}

// ----------------------------------------------------------------------------
?>

<form method="post" class="registerForm" enctype="multipart/form-data">
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="/images/photo.jpg">
    </label>
    <?= err('photo') ?>
    <label for="username">Name</label>
    <?= html_text('username', '', 'maxlength="100"') ?>
    <?= err('name') ?>

    <label for="phone">Phone</label>
    <?= html_text('phone', '', 'maxlength="11"') ?>
    <?= err('phone') ?>

    <label for="email">Email</label>
    <?= html_text('email',"", 'maxlength="254"') ?>
    <?= err('email') ?>

    <label for="password">Password</label>
    <?= html_password('password', 'class="pass-input" maxlength="100"') ?>
    <?= err('password') ?>

    <label for="confirm">Confirm</label>
    <?= html_password('confirm', 'class="pass-input" maxlength="100"') ?>
    <?= err('confirm') ?>

    <div style="text-align: center; margin-top: 10px; margin-bottom: 20px; width: 100%;">
        <a href="javascript:void(0)" 
        id="allToggleBtn" 
        onclick="toggleAllPasswords()" 
        style="color: #ff8da1; font-size: 13px; text-decoration: none; font-weight: bold;">
        Show All Passwords
        </a>
    </div>

    <section>
        <button>Register</button>
        <button type="reset">Reset</button>
    </section>
</form>

<script>
function toggleAllPasswords() {
    const $inputs = $('.pass-input');
    const $btn = $('#allToggleBtn');
    const isHidden = $inputs.attr('type') === 'password';

    $inputs.attr('type', isHidden ? 'text' : 'password');
    $btn.text(isHidden ? "Hide All Passwords" : "Show All Passwords"); 
}
</script>

<?php 
    include '../_foot.php';
?>
