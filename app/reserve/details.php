<?php
$_title = 'All Reservations';
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
            <input type="text" id="searchBox" placeholder="Search bookings..."
                   style="flex:1; max-width:320px; margin:0 20px; box-sizing:border-box; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px;">
        </div>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #ddd;">
                    <th style="padding:8px;">Reservation ID</th>
                    <th style="padding:8px;">Student</th>
                    <th style="padding:8px;">Room</th>
                    <th style="padding:8px;">Date</th>
                    <th style="padding:8px;">Time</th>
                    <th style="padding:8px;">Status</th>
                    <th style="padding:8px;">Actions</th>
                </tr>
            </thead>
            <tbody id="searchableRows">
                <?php foreach ($list as $r): ?>
                    <tr style="border-bottom:1px solid #f0f0f0;" data-search="<?= htmlspecialchars(strtolower($r->reservation_id . ' ' . $r->student_name . ' ' . $r->room_name . ' ' . $r->booking_date . ' ' . $r->status)) ?>">
                        <td style="padding:8px;"><?= $r->reservation_id ?></td>
                        <td style="padding:8px;">
                            <?= htmlspecialchars($r->student_name) ?> (<?= $r->user_id ?>)
                        </td>
                        <td style="padding:8px;"><?= htmlspecialchars($r->room_name ?? '—') ?></td>
                        <td style="padding:8px;"><?= $r->booking_date ?></td>
                        <td style="padding:8px;"><?= $r->start_time ?> - <?= $r->end_time ?></td>
                        <td style="padding:8px;"><?= htmlspecialchars($r->status) ?></td>
                        <td style="padding:8px;">
                            <?php if ($r->status != 'cancel'): ?>
                                <a href="/reserve/cancel.php?id=<?= $r->reservation_id ?>"
                                   onclick="return confirm('Cancel this reservation?')"
                                   style="color:#c0392b;">Cancel</a>
                            <?php else: ?>
                                —
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>
</div>

<script>
$(document).on('input', '#searchBox', function () {
    const q = $(this).val().toLowerCase().trim();
    $('#searchableRows tr').each(function () {
        $(this).toggle($(this).data('search').includes(q));
    });
});
</script>

<?php require __DIR__ . '/../_foot.php'; ?>