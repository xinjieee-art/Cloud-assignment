<?php
$_title = 'Admin Dashboard';
require __DIR__ . '/../_base.php';
auth('admin', 'staff');
require __DIR__ . '/_head.php';

$totalUsers = $_db->query('SELECT COUNT(*) FROM user')->fetchColumn();
$todayBookings = $_db->prepare('SELECT COUNT(*) FROM reservation WHERE booking_date = CURDATE()');
$todayBookings->execute();
$todayBookings = $todayBookings->fetchColumn();
$totalReservations = $_db->query('SELECT COUNT(*) FROM reservation')->fetchColumn();

// Recent 5 reservations
$recent = $_db->query('
    SELECT
        res.reservation_id,
        res.booking_date,
        res.status,
        u.name AS student_name,
        rm.name AS room_name,
        ts.start_time,
        ts.end_time
    FROM reservation res
    LEFT JOIN user u ON res.user_id = u.user_id
    LEFT JOIN time_slot ts ON res.slot_id = ts.slot_id
    LEFT JOIN reservation_room rr ON rr.reservation_room = res.reservation_id
    LEFT JOIN room rm ON rm.room_id = rr.room_id
    ORDER BY res.reservation_id DESC
    LIMIT 5
')->fetchAll();

// Room availability breakdown
$availableCount = $_db->query("SELECT COUNT(*) FROM room WHERE status = 'available'")->fetchColumn();
$unavailableCount = $_db->query("SELECT COUNT(*) FROM room WHERE status != 'available'")->fetchColumn();
$totalRooms = $availableCount + $unavailableCount;
$availablePct = $totalRooms > 0 ? round($availableCount / $totalRooms * 100) : 0;
?>
 
<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php" class="active">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/slot/details.php">Time Slot</a>
        <a href="/reserve/details.php">Booking reservation</a>
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
        <div class="card-grid">
            <div class="stat-card">
                <div class="label">Total users</div>
                <div class="value"><?= $totalUsers ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Today's Bookings</div>
                <div class="value"><?= $todayBookings ?></div>
            </div>
            <div class="stat-card">
                <div class="label">Total Reservations</div>
                <div class="value"><?= $totalReservations ?></div>
            </div>
        </div>

        <div style="display:flex; gap:24px; margin-top:2rem; flex-wrap:wrap; align-items:flex-start;">

            <!-- Recent reservations table -->
            <div style="flex:2; min-width:320px;">
                <h3 style="margin-top:0;">Recent Reservations</h3>
                <table style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="text-align:left; border-bottom:1px solid #ddd;">
                            <th style="padding:8px;">Student</th>
                            <th style="padding:8px;">Room</th>
                            <th style="padding:8px;">Date</th>
                            <th style="padding:8px;">Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!$recent): ?>
                            <tr><td colspan="4" style="padding:8px; color:#888;">No reservations yet</td></tr>
                        <?php endif ?>
                        <?php foreach ($recent as $r): ?>
                            <tr style="border-bottom:1px solid #f0f0f0;">
                                <td style="padding:8px;"><?= htmlspecialchars($r->student_name ?? '—') ?></td>
                                <td style="padding:8px;"><?= htmlspecialchars($r->room_name ?? '—') ?></td>
                                <td style="padding:8px;"><?= $r->booking_date ?></td>
                                <td style="padding:8px;"><?= $r->start_time ?> - <?= $r->end_time ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
                <p style="margin-top:10px;"><a href="/reserve/details.php" class="auth-link">View all reservations →</a></p>
            </div>

            <div style="flex:1; min-width:220px; text-align:center;">
                <h3 style="margin-top:0;">Room Availability</h3>
                <div style="
                    width:160px; height:160px; border-radius:50%; margin:0 auto;
                    background: conic-gradient(#4caf50 0% <?= $availablePct ?>%, #e0e0e0 <?= $availablePct ?>% 100%);
                    display:flex; align-items:center; justify-content:center;
                ">
                    <div style="width:110px; height:110px; border-radius:50%; background:#fff; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <div style="font-size:22px; font-weight:600;"><?= $availablePct ?>%</div>
                        <div style="font-size:12px; color:#888;">Available</div>
                    </div>
                </div>
                <div style="margin-top:14px; font-size:14px;">
                    <span style="color:#4caf50;">●</span> Available: <?= $availableCount ?>
                    &nbsp;&nbsp;
                    <span style="color:#e0e0e0;">●</span> Unavailable: <?= $unavailableCount ?>
                </div>
            </div>
        </div>
    </section>
</div>
 
<?php require __DIR__ . '/../_foot.php'; ?>