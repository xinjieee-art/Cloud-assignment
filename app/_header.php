<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" 
          content="width=device-width, initial-scale=1.0">
    <title>
        <?= $title ?? 'Untitled' ?>
    </title>

    <link rel="shortcut icon" 
    href="/images/logo.jpg">

    <link rel="stylesheet" 
    href="/css/app.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>

<body>
    <header>
        <h1><a href="/">CottonTie</a></h1>
        <div class="header-right">
            <?php if (isset($_SESSION['member'])): ?>
                <a href="/logout.php">Logout</a>
                <a href="/cart/cart.php">
                    <img src="/images/cart.jpg" class="cart-pic">
                </a>
                <a href="/user/profile.php">
                <img src="/images/profile/<?=  $_SESSION['member']->profile_picture ?>" 
                    style="width:36px; height:36px; object-fit:cover; border-radius:50%; display:block;">
                </a>
                <span><?= $_SESSION['member']->name ?></span>
                
            <?php else: ?>
                <a href="/login.php">Login / Register</a>
                <img src="/images/user.jpg" class="profile-pic">
            <?php endif ?>
        </div>
    </header>

    <nav>
        <a href="/">Home</a>
        <a href="/page/product.php">Product</a>
        <a href="/page/history.php">History</a>
        <a href="/page/contactUs.php">Contact Us</a>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
        <div id="info"><?= temp('info') ?></div>