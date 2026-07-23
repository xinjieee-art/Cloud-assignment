<?php
    include '../_base.php';
    auth('member');

    if(is_post()){
        $order_id = req('order_id');
        $stm = $_db->prepare("SELECT * FROM invoice WHERE order_id = ? AND member_id = ?");
        $stm->execute([$order_id, $_user->member_id]);
        $order = $stm->fetch();

        $stm = $_db->prepare('SELECT p.*, od.* FROM order_details od JOIN product p ON od.product_id = p.product_id WHERE order_id = ?');
        $stm->execute([$order_id]);
        $products = $stm->fetchAll();

        foreach($products as $p){
            $stm = $_db->prepare('SELECT * FROM product_image WHERE product_id = ?');
            $stm->execute([$p->product_id]);
            $image = $stm->fetchAll();
            $product_image[$p->product_id] = $image;
        } 
    }

    if(!$products){
        temp('info', 'No order was found!');
        redirect('profile.php');
    }

    $_title = "Order #".$order_id." ".$order->order_date;
    include '../_head.php';
    
?>

<div class = memberOrderDetailPage>
    <table>
        <tr class = orderDetails>
            <td class = "memberOrderDetailShippingAddressHeader"><h2>Shipping Address:</h2></td>
            <td><h2>Status:</h2></td>
            <td><h2><?= $order->status ?></h2></td>
        </tr>
        <tr>
            <td class = "memberOrderDetailShippingAddress"><?= $order->street_address . ', ' . $order->city . ', ' . $order->state . ' ' . $order->post_code?></td>
        </tr>
    </table>
    <?php if($products): ?>
        <table>
            <?php foreach($products as $p): ?>        
            <tr class = memberOrderDetailProductRowTop>
                <td rowspan = "3"><a><img class = memberOrderDetailProductImage src = <?= $product_image[$p->product_id][0]->image_url ?> alt = <?= $product_image[$p->product_id][0]->alt_text ?>></a></td>
                <td class = memberOrderDetailProductDetails><b><?= $p->name ?></b><br><span class="memberOrderDetailProductCategory"><?= $p->brand." ".$p->scale." ".$p->series ?></span></td>
                <td></td>
                <td class = memberOrderDetailRemoveBtn></td>
            </tr>
            <tr class = memberOrderDetailProductRowMiddle>
                <td class = memberOrderDetailProductPrice>RM <?= number_format($p->price,2) ?></td>
                <td class = "memberOrderDetailProductQuantity">Quantity:</td>
                <td class = "memberOrderDetailQuantityValue"><?= $p->quantity ?></td>
            </tr>
            <tr class = memberOrderDetailProductRowBottom>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <?php endforeach ?>
        </table>
    <?php endif?>
    <div class = memberOrderDetailTotalPriceArea>
        <p class = memberOrderDetailSubTotal>Total: RM<?= number_format($order->total_amount,2) ?></p>
    </div>
</div>
<?php include '../_foot.php' ?>