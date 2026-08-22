<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $_title ?? 'Untitled' ?></title>
    <link rel="shortcut icon" href="/images/favicon.png">
    <link rel="stylesheet" href="/css/app.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="/js/app.js"></script>
</head>
<body>
    <!-- Flash message -->
    <div id="info"><?= temp('info') ?></div>

    <header>
        <h1><a href="/">ABC Library</a></h1>
        <?php $_hideAuthLinks = str_contains($_SERVER['REQUEST_URI'], '/login.php')
                            || str_contains($_SERVER['REQUEST_URI'], '/register.php')
                            || str_contains($_SERVER['REQUEST_URI'], '/reset.php'); ?>
        <?php if (!$_hideAuthLinks): ?>
        <div class="header-right">
            <?php if ($_user): ?>
                <a href="/user/profile.php">
                    <span><?= htmlspecialchars($_user->name) ?></span>
                    <img src="/images/profile/<?= $_user->profile ?>" class="profile-pic">
                </a>
            <?php else: ?>
                <a href="/login/login.php">Login / Register</a>
                <img src="/images/user.png" class="profile-pic">
            <?php endif ?>
        </div>
        <?php endif ?>
    </header>

    <nav>
        <?php if ($_user && ($_user->role ?? '') == 'user'): ?>
            <a href="/page/home.php">Home</a>
            <a href="/page/room_detail.php">Room</a>
            <a href="/page/room_history.php">History</a>
            <a href="/logout.php">Logout</a>
        <?php endif ?>
    </nav>

    <main>
        <h1>
            <?= $_title ?? 'Untitled' ?>
            <?php if (!empty($_titleExtra)): ?>
                <span class="title-extra"><?= $_titleExtra ?></span>
            <?php endif ?>
        </h1>