<?php
    include '../_base.php';
    $_title = 'Profile';
    include '../_head.php';
 
    auth('user');

    if (is_get()) {
        $stm = $_db->prepare('SELECT * FROM user WHERE user_id = ?');
        $stm->execute([$_user->user_id]);
        $u = $stm->fetch();

        if (!$u) {
            redirect('/');
        }

        extract((array)$u);
        $_SESSION['profile'] = $u->profile;
    }

    if (is_post()) {
        $email = req('email');
        $name  = req('name');
        $photo = $_SESSION['profile'] ?? '';
        $f = get_file('photo');

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
                SELECT COUNT(*) FROM user
                WHERE email = ? AND user_id != ?
            ');
            $stm->execute([$email, $_user->user_id]);

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

        if ($f) {
            if (!str_starts_with($f->type, 'image/')) {
                $_err['profile'] = 'Must be image';
            }
            else if ($f->size > 1 * 720 * 720) {
                $_err['profile'] = 'Maximum 1MB';
            }
        }


        if (!$_err) {

            if ($f) {
                if (!empty($_user->profile)) {
                    $old_file = '../images/profile/' . $_user->profile;
                    if (file_exists($old_file) && is_file($old_file)) {
                        unlink($old_file);
                    }
                }
                $photo = save_photo($f, '../images/profile', 200);
            }
            $stm = $_db->prepare('
                UPDATE user
                SET email = ?, name = ?, profile = ?
                WHERE user_id = ?
            ');
            $stm->execute([$email, $name, $photo, $_user->user_id]);

            $_user->email = $email;
            $_user->name  = $name;
            $_user->profile= $photo;

            temp('info', 'Profile updated!');
            redirect('/');
        }
    }

    $photoSrc = !empty($profile) ? "/images/profile/$profile" : '/images/user.png';
?>

<div class="auth-card">
    <p class="auth-card__eyebrow">Manage your account</p>
    <h2 class="auth-card__title">Profile</h2>

    <form method="post" class="auth-form" enctype="multipart/form-data">
        <label class="auth-photo-upload" tabindex="0">
            <?= html_file('photo', 'image/*', 'hidden') ?>
            <img src="<?= $photoSrc ?>" id="photoPreview">
        </label>
        <?= err('photo') ?>

        <label for="name" class="sr-only">Name</label>
        <?= html_text('name', 'placeholder="Full name" maxlength="100"') ?>
        <?= err('name') ?>

        <label for="email" class="sr-only">Email</label>
        <?= html_text('email', 'placeholder="Email address" maxlength="100"') ?>
        <?= err('email') ?>

        <button class="auth-btn-primary">Update Profile</button>
        <button type="reset" class="auth-btn-secondary">Reset</button>

        <p class="auth-footer-text">
            <a href="password.php" class="auth-link">Update your password?</a>
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
</script>

<?php
include '../_foot.php';
?>