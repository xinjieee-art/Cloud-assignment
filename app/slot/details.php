<?php
$_title = '';
require __DIR__ . '/../_base.php';
auth('admin', 'staff');
require __DIR__ . '/../_head.php';

$stm = $_db->query('SELECT * FROM time_slot ORDER BY start_time');
$list = $stm->fetchAll();
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/reserve/details.php">Booking reservation</a>
        <?php if (($_user->role ?? '') == 'admin'): ?>
            <a href="/staff/details.php">Staff</a>
        <?php endif ?>
        <a href="/slot/details.php" class="active">Time Slot</a>
        <a href="/logout.php">Logout</a>

        <?php $profileUrl = ($_user->role ?? '') == 'admin' ? '/admin/profile.php' : '/staff/profile.php'; ?>
        <a href="<?= $profileUrl ?>" class="sidebar-user">
            <img src="/images/profile/<?= $_user->profile ?>">
            <div><?= htmlspecialchars($_user->name) ?></div>
        </a>
    </aside>

    <section class="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="margin:0;">Time Slot List</h2>
            <a href="/slot/insert.php" class="auth-btn-primary" style="display:inline-block; width:auto; padding:8px 16px; text-decoration:none;">
                + Add Time Slot
            </a>
        </div>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #ddd;">
                    <th style="padding:8px;">Start Time</th>
                    <th style="padding:8px;">End Time</th>
                    <th style="padding:8px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $s): ?>
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:8px;"><?= $s->start_time ?></td>
                        <td style="padding:8px;"><?= $s->end_time ?></td>
                        <td style="padding:8px;">
                            <a href="/slot/update.php?id=<?= $s->slot_id ?>">Edit</a>
                            &nbsp;|&nbsp;
                            <a href="/slot/delete.php?id=<?= $s->slot_id ?>"
                               onclick="return confirm('Delete this time slot?')"
                               style="color:#c0392b;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>