<?php
    include '../_base.php';
    $_title = 'Login';
    include '../_head.php';

    if ($_user) {
        temp('info', "You're already logged in!");
        redirect('/');
    }

    if (is_post()) {
        $email    = req('email');
        $password = req('password');

        $_err = []; 

        // Validate: email
        if ($email == '') {
            $_err['email'] = 'Required';
        }
        else if (!is_email($email)) {
            $_err['email'] = 'Invalid email';
        }

        // Validate: password
        if ($password == '') {
            $_err['password'] = 'Required';
        }

        if (!$_err) {
            $stm = $_db->prepare('
                SELECT * FROM user
                WHERE email = ? AND password = SHA1(?)
            ');
            $stm->execute([$email, $password]);
            $u = $stm->fetch();

            if ($u) {
                $u->role = 'user'; 
            }
            else {
                $stm = $_db->prepare('
                    SELECT * FROM staff
                    WHERE email = ? AND password = SHA1(?)
                ');
                $stm->execute([$email, $password]);
                $u = $stm->fetch();

                if ($u) {
                    $u->role = 'staff';
                }
                else {
                    $stm = $_db->prepare('
                        SELECT * FROM admin
                        WHERE email = ? AND password = SHA1(?)
                    ');
                    $stm->execute([$email, $password]);
                    $u = $stm->fetch();

                    if ($u) {
                        $u->role = 'admin';
                    }
                }
            }

            if ($u) {
                temp('info', 'Login successfully, Welcome ' . $u->name);

                $url = in_array($u->role, ['admin', 'staff']) ? '/admin/home.php' : '/';
                login($u, $url);
                exit();
            }
            else {
                $_err['password'] = 'Not matched';
            }
        }
    }
?>

<div class="auth-card">
    <p class="auth-card__eyebrow">Please enter your details</p>
    <h2 class="auth-card__title">Welcome back</h2>

    <form method="post" class="auth-form">
        <label for="email" class="sr-only">Email</label>
        <?= html_text('email', 'placeholder="Email address" maxlength="100"') ?>
        <?= err('email') ?>

        <label for="password" class="sr-only">Password</label>
        <?= html_password('password', 'placeholder="Password" maxlength="100"') ?>
        <?= err('password') ?>

        <div class="auth-form__row">
            <a href="javascript:void(0)"
               id="toggleBtn"
               onclick="toggleView()"
               class="auth-link-muted">
                Show Password
            </a>
            <a href="reset.php" class="auth-link">Forgot password?</a>
        </div>

        <button class="auth-btn-primary">Login</button>

        <p class="auth-footer-text">
            Don't have an account? <a href="register.php" class="auth-link">Register here</a>
        </p>
    </form>
</div>

<script>
function toggleView() {
    const $input = $('#password');
    const $btn = $('#toggleBtn');
    const isHidden = $input.attr('type') === 'password';

    $input.attr('type', isHidden ? 'text' : 'password');
    $btn.text(isHidden ? "Hide Password" : "Show Password");
}
</script>

<?php
    include '../_foot.php';
?>