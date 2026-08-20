<?php
    include '../_base.php';
    $_title = 'User | Password';
    include '../_head.php';

 
    auth('user');

    if (is_post()) {
        $password     = req('password');
        $new_password = req('new_password');
        $confirm      = req('confirm');


        if ($password == '') {
            $_err['password'] = 'Required';
        }
        else if (strlen($password) < 5 || strlen($password) > 100) {
            $_err['password'] = 'Between 5-100 characters';
        }
        else {
            $stm = $_db->prepare('
                SELECT COUNT(*) FROM user
                WHERE password = SHA1(?) AND user_id = ?
            ');
            $stm->execute([$password, $_user->user_id]);
            
            if ($stm->fetchColumn() == 0) {
                $_err['password'] = 'Not matched';
            }
        }


        if ($new_password == '') {
            $_err['new_password'] = 'Required';
        }
        else if (strlen($new_password) < 5 || strlen($new_password) > 100) {
            $_err['new_password'] = 'Between 5-100 characters';
        }


        if (!$confirm) {
            $_err['confirm'] = 'Required';
        }
        else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
            $_err['confirm'] = 'Between 5-100 characters';
        }
        else if ($confirm != $new_password) {
            $_err['confirm'] = 'Not matched';
        }

        if (!$_err) {
            $stm = $_db->prepare('
                UPDATE user     
                SET password = SHA1(?)
                WHERE user_id = ?
            ');
            $stm->execute([$new_password, $_user->user_id]);

            temp('info', 'Password updated');
            redirect('/user/profile.php');
        }
    }
?>

<div class="auth-card">
    <p class="auth-card__eyebrow">Manage your account</p>
    <h2 class="auth-card__title">Update Password</h2>

    <form method="post" class="auth-form">
        <label for="password" class="sr-only">Current Password</label>
        <?= html_password('password', 'class="pass-input" placeholder="Current password" maxlength="100"') ?>
        <?= err('password') ?>

        <label for="new_password" class="sr-only">New Password</label>
        <?= html_password('new_password', 'class="pass-input" placeholder="New password" maxlength="100"') ?>
        <?= err('new_password') ?>

        <label for="confirm" class="sr-only">Confirm Password</label>
        <?= html_password('confirm', 'class="pass-input" placeholder="Confirm new password" maxlength="100"') ?>
        <?= err('confirm') ?>

        <div class="auth-form__row">
            <a href="javascript:void(0)"
               id="allToggleBtn"
               onclick="toggleAllPasswords()"
               class="auth-link-muted">
                Show All Passwords
            </a>
        </div>

        <button class="auth-btn-primary">Update Password</button>
        <button type="reset" class="auth-btn-secondary">Reset</button>

        <p class="auth-footer-text">
            <a href="profile.php" class="auth-link">Back to Profile</a>
        </p>
    </form>
</div>

<script>
function toggleAllPasswords() {
    const $inputs = $('.pass-input');
    const $btn = $('#allToggleBtn');

    if ($inputs.length > 0) {
        const isHidden = $inputs.first().attr('type') === 'password';

        $inputs.attr('type', isHidden ? 'text' : 'password');
        $btn.text(isHidden ? "Hide All Passwords" : "Show All Passwords");
    }
}
</script>

<?php
include '../_foot.php';
?>