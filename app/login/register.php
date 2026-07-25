<?php
include '../_base.php';
$_title = 'Register';
include '../_head.php';
 
if ($_user) {
    temp('info', "You're already logged in!");
    redirect('/');
}
 
// ----------------------------------------------------------------------------
 
if (is_post()) {
    $name     = req('username');
    $gender = req('gender');
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
    else if (!is_unique($email, 'user', 'email')) {
        $_err['email'] = 'Duplicated';
    }
 
    if (!$password) {
        $_err['password'] = 'Required';
    }
    else if (strlen($password) < 5 || strlen($password) > 20) {
        $_err['password'] = 'Between 5-20 characters';
    }
 
    if (!$confirm) {
        $_err['confirm'] = 'Required';
    }
    else if (strlen($confirm) < 5 || strlen($confirm) > 20) {
        $_err['confirm'] = 'Between 5-20 characters';
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

    if (!$gender) {
        $_err['gender'] = 'Required';
    }
    else if (!in_array($gender, ['Male', 'Female'])) {
        $_err['gender'] = 'Invalid selection';
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
        $photo = save_photo($f, '../images/profile');
        $stm = $_db->prepare('
            INSERT INTO user (name, email, password, profile, gender)
            VALUES (?, ?, ?, ?, ?)
        ');
        $stm->execute([$name, $email, $password, $photo, $gender]);
 
        temp('info', 'Registered! Please login.');
        redirect('/login/login.php');
    }
}
 
// ----------------------------------------------------------------------------
?>
 
<div class="auth-card">
    <p class="auth-card__eyebrow">Just a few details</p>
    <h2 class="auth-card__title">Create an account</h2>
 
    <form method="post" class="auth-form" enctype="multipart/form-data">
        <label class="auth-photo-upload" tabindex="0">
            <?= html_file('photo', 'image/*', 'hidden') ?>
            <img src="/images/profile/user.png" id="photoPreview">
        </label>
        <?= err('photo') ?>
 
        <label for="username" class="sr-only">Name</label>
        <?= html_text('username', 'placeholder="Full name" maxlength="100"') ?>
        <?= err('name') ?>

        <label class="sr-only">Gender</label>
        <div class="auth-form__row">
            <label><input type="radio" name="gender" value="Male"> Male</label>
            <label><input type="radio" name="gender" value="Female"> Female</label>
        </div>
        <?= err('gender') ?>
 
        <label for="email" class="sr-only">Email</label>
        <?= html_text('email', 'placeholder="Email address" maxlength="254"') ?>
        <?= err('email') ?>
 
        <label for="password" class="sr-only">Password</label>
        <?= html_password('password', 'class="pass-input" placeholder="Password" maxlength="20"') ?>
        <?= err('password') ?>
 
        <label for="confirm" class="sr-only">Confirm password</label>
        <?= html_password('confirm', 'class="pass-input" placeholder="Confirm password" maxlength="20"') ?>
        <?= err('confirm') ?>
 
        <div class="auth-form__row">
            <a href="javascript:void(0)"
               id="allToggleBtn"
               onclick="toggleAllPasswords()"
               class="auth-link-muted">
                Show All Passwords
            </a>
        </div>
 
        <button class="auth-btn-primary">Register</button>
 
        <p class="auth-footer-text">
            Already have an account? <a href="login.php" class="auth-link">Login here</a>
        </p>
    </form>
</div>
 
<script>
function toggleAllPasswords() {
    const $inputs = $('.pass-input');
    const $btn = $('#allToggleBtn');
    const isHidden = $inputs.attr('type') === 'password';
 
    $inputs.attr('type', isHidden ? 'text' : 'password');
    $btn.text(isHidden ? "Hide All Passwords" : "Show All Passwords");
}

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
?>