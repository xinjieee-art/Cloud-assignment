<?php
    include '../_base.php';
    $_title = 'Login';
    include '../_head.php';
    if ($_user){
        temp('info', "You're already logged in!");
        redirect('/');
    }

    if (is_post()) {
        $email = req('email');
        $password = req('password');

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

        // Login user
    if (is_post()) {
            $email = req('email');
            $password = req('password');

            if ($email == '') {
                $_err['email'] = 'Required';
            } else if (!is_email($email)) {
                $_err['email'] = 'Invalid email format';
            }

            if ($password == '') {
                $_err['password'] = 'Required';
            }

            if (!$_err) {
                $stm = $_db->prepare('SELECT * FROM member WHERE email = ?');
                $stm->execute([$email]);
                $u = $stm->fetch();

                if ($u) {
                    $stm = $_db->prepare('
                        SELECT expire FROM token 
                        WHERE member_id = ? AND expire > NOW()
                    ');
                    $stm->execute([$u->member_id]);
                    $lock = $stm->fetch();

                    if ($lock) {
                        $remaining = ceil((strtotime($lock->expire) - time()) / 60);
                        $_err['password'] = "Locked. Try again in $remaining min.";
                    }

                    else if ($u->password == sha1($password)) {
                        $_db->prepare('
                            UPDATE member SET login_attempts = 0 
                            WHERE email = ?')->execute([$email]);
                        $_db->prepare('
                            DELETE FROM token 
                            WHERE member_id = ?')->execute([$u->member_id]);

                        temp('info', 'Login successfully, Welcome ' . $u->name);
                        $u->role = 'member';
                        login($u);
                    } 

                    else {
                        $new_attempts = $u->login_attempts + 1;

                        if ($new_attempts >= 3) {
                            $id = sha1(uniqid());
                            $stm = $_db->prepare('
                                INSERT INTO token (id, expire, member_id) 
                                VALUES (?, ADDTIME(NOW(), "00:05:00"), ?)');
                            $stm->execute([$id, $u->member_id]);
                            $_db->prepare('
                                UPDATE member SET login_attempts = 0 
                                WHERE email = ?')->execute([$email]);
                            
                            $_err['password'] = "Locked for 5 minutes.";
                        } else {
                            $_db->prepare('
                                UPDATE member SET login_attempts = ? 
                                WHERE email = ?')->execute([$new_attempts, $email]);
                            $remaining = 3 - $new_attempts;
                            $_err['password'] = "Wrong password. $remaining left.";
                        }
                    }
                } else {
                    $_err['email'] = 'Account not found';
                }
            }
        }
    }   
?>

<form method="post" class="memberLogin">
    <label for="email" >Email</label>
    <?= html_text('email',"", 'maxlength="100"') ?>
    <?= err('email') ?>

    <label for="password">Password</label>
    <?= html_password('password', 'maxlength="100"') ?>
        <div style="text-align: center; margin-top: 10px; margin-bottom: 20px; width: 100%;">
            <a href="javascript:void(0)" 
            id="toggleBtn" 
            onclick="toggleView()" 
            style="color: #ff8da1; font-size: 13px; text-decoration: none; font-weight: bold;">
            Show Password
            </a>
        </div>
    <?= err('password') ?>

    <section method="post" class="memberLoginButton">
        <button>Login</button>

    </section>

    <section style="text-align: center;">
        Don't have an account? 
        <a href="register.php" style="display: inline; margin-left: 5px;">Register here</a>
    </section>

    <section>
        <a href="reset.php">Forgot your password?</a>
    </section> 
</form>

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
    include '../_foot.php'
?>