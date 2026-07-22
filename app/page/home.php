<?php
$_title = 'Admin Dashboard';
require __DIR__ . '/../_base.php';
require __DIR__ . '/../_head.php';
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/demo2.php" class="active">Home</a>
        <?php if ($_user): ?>
            <a href="/demo1.php">Demo 1</a>
        <?php endif ?>
        <?php if (($_user->role ?? null) == "Admin"): ?>
            <a href="/demo2.php">Demo 2 (Admin)</a>
        <?php endif ?>
        <?php if (($_user->role ?? null) == "Member"): ?>
            <a href="/demo3.php">Demo 3</a>
        <?php endif ?>
        <a href="/admin/profile.php">Profile</a>
        <a href="/room/detail.php">Room</a>
        <a href="/logout.php">Logout</a>

        <?php if ($_user): ?>
            <a href="/user/profile.php" class="sidebar-user">
                <img src="/photos/<?= $_user->profile ?>">
                <div>
                    <?= htmlspecialchars($_user->name) ?>
                </div>
            </a>
        <?php endif ?>
    </aside>
 
    <section class="admin-content">
        <div class="card-grid">
            <div class="stat-card">
                <div class="label">Total users</div>
                <div class="value">--</div>
            </div>
            <div class="stat-card">
                <div class="label">Pending requests</div>
                <div class="value">--</div>
            </div>
            <div class="stat-card">
                <div class="label">Active sessions</div>
                <div class="value">--</div>
            </div>
        </div>
    </section>
</div>
 
<?php require __DIR__ . '/../_foot.php'; ?>