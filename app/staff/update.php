<?php
require __DIR__ . '/../_base.php';
auth('admin');

$id = req('id');

if (!is_exists($id, 'staff', 'staff_id')) {
    temp('info', 'Staff not found');
    redirect('/staff/details.php');
}

if (is_get()) {
    $stm = $_db->prepare('SELECT * FROM staff WHERE staff_id = ?');
    $stm->execute([$id]);
    $u = $stm->fetch();
    extract((array)$u);
    $password = ''; // don't pre-fill the stored password hash into the input
}

if (is_post()) {
    $name     = req('name');
    $email    = req('email');
    $gender   = req('gender');
    $password = req('password'); // optional: leave blank to keep current
    $f = get_file('photo');

    $stm = $_db->prepare('SELECT * FROM staff WHERE staff_id = ?');
    $stm->execute([$id]);
    $current = $stm->fetch();
    $profile = $current->profile;

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
    else {
        $stm = $_db->prepare('SELECT COUNT(*) FROM staff WHERE email = ? AND staff_id != ?');
        $stm->execute([$email, $id]);
        if ($stm->fetchColumn() > 0) {
            $_err['email'] = 'Duplicated';
        }
    }

    if ($gender == '') {
        $_err['gender'] = 'Required';
    }

    if ($password != '' && (strlen($password) < 5 || strlen($password) > 100)) {
        $_err['password'] = 'Between 5-100 characters';
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
        if ($f) {
            $profile = save_photo($f, '../images/profile');
        }

        if ($password != '') {
            $stm = $_db->prepare('
                UPDATE staff
                SET name = ?, email = ?, gender = ?, profile = ?, password = SHA1(?)
                WHERE staff_id = ?
            ');
            $stm->execute([$name, $email, $gender, $profile, $password, $id]);
        }
        else {
            $stm = $_db->prepare('
                UPDATE staff
                SET name = ?, email = ?, gender = ?, profile = ?
                WHERE staff_id = ?
            ');
            $stm->execute([$name, $email, $gender, $profile, $id]);
        }

        temp('info', 'Staff updated');
        redirect('/staff/details.php');
    }
}

$_title = 'Edit Staff';
include '../_head.php';
?>

<div class="auth-card">
    <p class="auth-card__eyebrow">Staff management</p>
    <h2 class="auth-card__title">Edit Staff</h2>

    <form method="post" class="auth-form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <label class="auth-photo-upload" tabindex="0">
            <?= html_file('photo', 'image/*', 'hidden') ?>
            <img src="/images/profile/<?= $profile ?>" id="photoPreview">
        </label>
        <?= err('photo') ?>

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

        <label for="password" class="sr-only">New password</label>
        <?= html_password('password', 'id="password" placeholder="New password (leave blank to keep current)" maxlength="100"') ?>
        <?= err('password') ?>

        <div class="auth-form__row">
            <a href="javascript:void(0)"
               id="toggleBtn"
               onclick="toggleView()"
               class="auth-link-muted">
                Show Password
            </a>
        </div>

        <button class="auth-btn-primary">Save changes</button>

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

function toggleView() {
    const $input = $('#password');
    const $btn = $('#toggleBtn');
    const isHidden = $input.attr('type') === 'password';

    $input.attr('type', isHidden ? 'text' : 'password');
    $btn.text(isHidden ? "Hide Password" : "Show Password");
}
</script>

<?php include '../_foot.php'; ?>