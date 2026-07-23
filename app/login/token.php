<?php
    include '../_base.php';
    $_title = 'Reset Password';
    include '../_head.php';
    // ----------------------------------------------------------------------------

    $_db->query('DELETE FROM token WHERE expire < NOW()');

    $id = req('id');

    if (!is_exists($id, 'token', 'id')) { // check form data base
        temp('info', 'Invalid token. Try again');
        redirect('/');
    }

    if (is_post()) {
        $password = req('password');
        $confirm  = req('confirm');


        if ($password == '') {
            $_err['password'] = 'Required';
        }
        else if (strlen($password) < 5 || strlen($password) > 100) {
            $_err['password'] = 'Between 5-100 characters';
        }


        if ($confirm == '') {
            $_err['confirm'] = 'Required';
        }
        else if (strlen($confirm) < 5 || strlen($confirm) > 100) {
            $_err['confirm'] = 'Between 5-100 characters';
        }
        else if ($confirm != $password) {
            $_err['confirm'] = 'Not matched';
        }


        if (!$_err) {
            $stm = $_db->prepare('
                UPDATE member
                SET password = SHA1(?)
                WHERE member_ID = (SELECT member_id FROM token WHERE id = ?);

                DELETE FROM token WHERE id = ?;
            ');
            $stm->execute([$password, $id, $id]);

            temp('info', 'Record updated');
            redirect('/login/login.php');
        }
    }
?>
 

<form method="post" class="tokenForm">
    <label for="password">Password</label>
    <?= html_password('password', 'class="pass-input" maxlength="100"') ?>
    <?= err('password') ?>

    <label for="confirm">Confirm</label>
    <?= html_password('confirm', 'class="pass-input" maxlength="100"') ?>
    <?= err('confirm') ?>

    <div style="text-align: right; margin-top: 10px; margin-bottom: 20px;">
        <a href="javascript:void(0)" 
        id="allToggleBtn" 
        onclick="toggleAllPasswords()" 
        style="color: #ff8da1;
        font-size: 13px; 
        text-decoration: none;
         font-weight: bold;">
        Show All Passwords
        </a>
    </div>


    <section>
        <button>Submit</button>
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