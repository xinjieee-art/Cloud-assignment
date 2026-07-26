<?php
$_title = '';
require __DIR__ . '/../_base.php';
auth('admin', 'staff');
require __DIR__ . '/../_head.php';

$stm = $_db->query('
    SELECT
        res.reservation_id,
        res.booking_date,
        res.status,
        u.user_id,
        u.name AS student_name,
        rm.name AS room_name,
        ts.start_time,
        ts.end_time
    FROM reservation res
    LEFT JOIN user u ON res.user_id = u.user_id
    JOIN time_slot ts ON res.slot_id = ts.slot_id
    LEFT JOIN reservation_room rr ON rr.reservation_room = res.reservation_id
    LEFT JOIN room rm ON rm.room_id = rr.room_id
    ORDER BY res.booking_date DESC, ts.start_time
');
$list = $stm->fetchAll();
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/reserve/details.php" class="active">Booking reservation</a>
        <?php if (($_user->role ?? '') == 'admin'): ?>
            <a href="/staff/details.php">Staff</a>
        <?php endif ?>
        <a href="/slot/details.php">Time Slot</a>
        <a href="/logout.php">Logout</a>

        <?php $profileUrl = ($_user->role ?? '') == 'admin' ? '/admin/profile.php' : '/staff/profile.php'; ?>
        <a href="<?= $profileUrl ?>" class="sidebar-user">
            <img src="/images/profile/<?= $_user->profile ?>">
            <div><?= htmlspecialchars($_user->name) ?></div>
        </a>
    </aside>

    <section class="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="margin:0;">All Bookings</h2>
            <a href="/reserve/insert.php" class="auth-btn-primary" style="display:inline-block; width:auto; padding:8px 16px; text-decoration:none;">
                + New Reservation
            </a>
        </div>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #ddd;">
                    <th style="padding:8px;">Reservation ID</th>
                    <th style="padding:8px;">Student ID</th>
                    <th style="padding:8px;">Room</th>
                    <th style="padding:8px;">Date</th>
                    <th style="padding:8px;">Time</th>
                    <th style="padding:8px;">Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $r): ?>
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:8px;"><?= $r->reservation_id ?></td>
                        <td style="padding:8px;">
                            <?= htmlspecialchars($r->student_name) ?> (<?= $r->user_id ?>)
                        </td>
                        <td style="padding:8px;"><?= htmlspecialchars($r->room_name ?? '—') ?></td>
                        <td style="padding:8px;"><?= $r->booking_date ?></td>
                        <td style="padding:8px;"><?= $r->start_time ?> - <?= $r->end_time ?></td>
                        <td style="padding:8px;"><?= htmlspecialchars($r->status) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>