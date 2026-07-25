<?php
$_title = 'Admin Dashboard';
require __DIR__ . '/../_base.php';
auth('admin', 'staff');
require __DIR__ . '/../_head.php';
?>
 
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/demo2.php" class="active">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/reserve/insert.php">Booking reservation</a>
        <a href="/logout.php">Logout</a>
 
        <?php $profileUrl = ($_user->role ?? '') == 'admin' ? '/admin/profile.php' : '/staff/profile.php'; ?>
        <a href="<?= $profileUrl ?>" class="sidebar-user">
            <img src="/images/profile/<?= $_user->profile ?>">
            <div>
                <?= htmlspecialchars($_user->name) ?>
            </div>
        </a>
        <?php ?>
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