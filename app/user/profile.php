<?php
    include '../_base.php';
    $_title = 'Profile';
    include '../_head.php';
 
    auth('member');

    if (is_get()) {
        $stm = $_db->prepare('SELECT * FROM member WHERE member_id = ?');
        $stm->execute([$_user->member_id]);
        $u = $stm->fetch();

        if (!$u) {
            redirect('/');
        }

        extract((array)$u);
        $_SESSION['image_url'] = $u->image_url;
    }

    if (is_post()) {
        $email = req('email');
        $name  = req('name');
        $phone = req('phone');
        $photo = $_SESSION['image_url'];
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
                SELECT COUNT(*) FROM member
                WHERE email = ? AND member_id != ?
            ');
            $stm->execute([$email, $_user->member_id]);

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
        if ($phone == '') {
            $_err['phone'] = 'Required';
        }
        else if (!preg_match ('/^\d{10,11}$/', $phone)) {
            $_err['phone'] = 'Invalid phone number';
        }

        if ($f) {
            if (!str_starts_with($f->type, 'image/')) {
                $_err['photo'] = 'Must be image';
            }
            else if ($f->size > 1 * 720 * 720) {
                $_err['photo'] = 'Maximum 1MB';
            }
        }


        if (!$_err) {

            if ($f) {
                unlink("../images/$photo");
                $photo = save_photo($f, '../images/user','/images/user');
            }
            $stm = $_db->prepare('
                UPDATE member
                SET email = ?, name = ?, phone_number = ?, image_url = ?
                WHERE member_id = ?
            ');
            $stm->execute([$email, $name, $phone, $photo, $_user->member_id]);

            $_user->email = $email;
            $_user->name  = $name;
            $_user->phone_number = $phone;
            $_user->image_url= $photo;

            temp('info', 'Profile updated!');
            redirect('/');
        }
    }

    $stm = $_db->prepare('SELECT * FROM invoice WHERE member_id = ?');
    $stm->execute([$_user->member_id]);
    $orders = $stm->fetchAll();

    if (is_post() && isset($_POST['cancel_order_id'])) {
        $cancel_id = post('cancel_order_id');

        
        $stmt = $_db->prepare("SELECT product_id, quantity FROM order_details WHERE order_id = ?");
        $stmt->execute([$cancel_id]);
        $items = $stmt->fetchAll();

        foreach ($items as $item) {
            $upd = $_db->prepare("UPDATE product SET stock_quantity = stock_quantity + ? WHERE product_id = ?");
            $upd->execute([$item->quantity, $item->product_id]);
        }

        $delDetails = $_db->prepare("DELETE FROM order_details WHERE order_id = ?");
        $delDetails->execute([$cancel_id]);

        
        $delReceipt = $_db->prepare("DELETE FROM receipt WHERE order_id = ?");
        $delReceipt->execute([$cancel_id]);

       
        $delInvoice = $_db->prepare("DELETE FROM invoice WHERE order_id = ?");
        $delInvoice->execute([$cancel_id]);

        temp('info', 'Order cancelled and records cleared.');
        redirect($_SERVER['PHP_SELF']);
        exit();
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

    <label for="email">Email</label>
    <?= html_text('email', $_user->email, 'maxlength="100"') ?>
    <?= err('email') ?>

    <label for="phone">Phone</label>
    <?= html_text('phone', $_user->phone_number, 'maxlength="11"') ?>
    <?= err('phone') ?>

    <section>
            <a href="password.php">Update your password?</a>
    </section>

    <section>
        <button>Update Profile</button>
        <button type="reset">Reset</button>
    </section>
</form>

<div class = "profileOrderHistory">
    <h2>Order History</h2>
    <?php if($orders): ?>
        <table>
            <?php foreach($orders as $o): ?>       
            <tr class = profileOrderRowTop>
                <td class = profileOrderDetails><b>Order #<?= $o->order_id." ". $o->order_date ?></b></td>
                <td></td>
                <td class = "profileOrderViewDetailBtn">
                    <button data-post="order_detail.php?order_id=<?= $o->order_id ?>">View Details</button>
                    <?php if ($o->status == 'Preparing'): ?>
                        <form method="post" onsubmit="return confirm('Are you sure you want to cancel this order? This will restore stock and delete the record.')">
                            <input type="hidden" name="cancel_order_id" value="<?= $o->order_id ?>">
                            <button type="submit" class="cancel-btn" style="color: red; font-size: 20px; text-decoration: underline;">Cancel Order</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
            <tr class = profileOrderRowMiddle>
                <td class = "profileOrderAddress"><span>Shipping Address:</span><br><?= $o->street_address . ",<br>" . $o->city . ', ' . $o->state . ' ' . $o->post_code;?></td>
                <td class = profileOrderPrice>RM <?= number_format($o->total_amount,2) ?></td>
                <td class = "profileOrderProductQuantiy">Status: <?= $o->status ?></td>
            </tr>
            <tr class = profileOrderRowBottom></tr>
            <?php endforeach ?>
        </table>
    <?php else: ?>
        <p class = "profileOrderEmpty">You have no previous orders!</p>
    <?php endif ?>
</div>

<?php
include '../_foot.php';
?>