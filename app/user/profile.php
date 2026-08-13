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
        $_SESSION['image_url'] = $u->profile;
    }

    if (is_post()) {
        $email = req('email');
        $name  = req('name');
        $photo = $_SESSION['image_url'] ?? 'images/profile/default.png';
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
       
        if (!empty($_user->profile) && $_user->profile !== 'images/profile/default.png') {
            $old_file = '../' . $_user->profile;
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

  
?>

<form method="post" class="profileForm" enctype="multipart/form-data">
    <label class="upload" tabindex="0">
        <?= html_file('photo', 'image/*', 'hidden') ?>
        <img src="../<?= $_user->image_url ?> ">
    </label>
    <?= err('photo') ?>

    <label for="name">Name</label>
    <?= html_text('name', $_user->name, 'maxlength="100"') ?>
    <?= err('name') ?>
    <br>
    <label for="email">Email</label>
    <?= html_text('email', $_user->email, 'maxlength="100"') ?>
    <?= err('email') ?>


    <section style="margin-top: 10px; margin-bottom: 20px;">
            <a href="password.php">Update your password?</a>
    </section>

    <section>
        <button style="background-color: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer;">Update Profile</button>
        <button type="reset" style="background-color: #007bff; color: white; border: none; padding: 10px 20px; cursor: pointer;">Reset</button>
    </section>
</form>


</div>

<?php
include '../_foot.php';
?>