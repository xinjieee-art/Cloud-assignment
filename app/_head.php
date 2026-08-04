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
        <h1><a href="/">ABC Library</a></h1>
        <div class="header-right">
            <?php if ($_user): ?>
                <a href="/cart/cart.php">
                    <img src="/images/latte.jpg" class="cart-pic">
                </a>
                <a href="/user/profile.php">
                    <img src="/images/profile/<?= $_user->profile ?>" 
                        style="width:36px; height:36px; object-fit:cover; border-radius:50%; display:block;">
                    <span><?= htmlspecialchars($_user->name) ?></span>
                </a>
            <?php else: ?>
                <a href="/login.php">Login / Register</a>
                <img src="/images/user.png" class="profile-pic">
            <?php endif ?>
        </div>
    </header>

    <nav>
        <a href="/page/home.php">Home</a>
        <a href="/page/room_detail.php">Room</a>
        <a href="/page/room_history.php">History</a>
        <a href="../logout.php">Logout</a>
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
        <div id="info"><?= temp('info') ?></div>