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
    href="/images/">

    <link rel="stylesheet" 
    href="/css/app.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>

<body>
    <header>
        <h1><a href="/">Study ABC</a></h1>
        <div class="header-right">
            <?php if (isset($_SESSION['user'])): ?>
                <a href="/logout.php">Logout</a>
                    <img src="/images/user.png" class="cart-pic" >
                </a>
                <a href="/user/profile.php">
                </a>
                <span><?= $_SESSION['user']->name ?></span>
                
            <?php else: ?>
                <a href="/login.php">Login / Register</a>
                <img src="/images/user.png," class="profile-pic">
            <?php endif ?>
        </div>
    </header>

    <nav>
        <a href="/">Home</a>
        <a href="/reserve/insert.php">Book reservation</a>
        <a href="/room/detail.php">Room Detail</a>
       <!-- <a href="/page/contactUs.php">Contact Us</a>
        <a href="/page/review.php">Reviews</a> -->
    </nav>

    <main>
        <h1><?= $_title ?? 'Untitled' ?></h1>
        <div id="info"><?= temp('info') ?></div>

