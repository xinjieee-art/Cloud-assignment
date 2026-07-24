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
    </header>

    <nav>
        <a href="/reserve/insert.php">Booking reservation</a>
    </nav>
   

<!--  <a href="/reserve/insert.php">
     <button type="button">Booking Reservation</button>
    </a> -->
    
    <main>
        <h1>
            <?= $_title ?? 'Untitled' ?>
            <?php if (!empty($_titleExtra)): ?>
                <span class="title-extra"><?= $_titleExtra ?></span>
            <?php endif ?>
        </h1>