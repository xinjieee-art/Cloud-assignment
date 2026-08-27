<?php
$_title = 'Home';
require __DIR__ . '/../_base.php';
require __DIR__ . '/../_head.php';

if ($_user && in_array($_user->role, ['admin', 'staff'])) {
    redirect('/admin/home.php');
}

$isGuest = !$_user;

// Available rooms (preview)
$rooms = $_db->query("SELECT * FROM room WHERE status = 'available' LIMIT 4")->fetchAll();

$myBookings = [];
if (!$isGuest) {
    $stm = $_db->prepare('
        SELECT
            res.reservation_id,
            res.booking_date,
            res.status,
            rm.name AS room_name,
            ts.start_time,
            ts.end_time
        FROM reservation res
        LEFT JOIN time_slot ts ON res.slot_id = ts.slot_id
        LEFT JOIN reservation_room rr ON rr.reservation_room = res.reservation_id
        LEFT JOIN room rm ON rm.room_id = rr.room_id
        WHERE res.user_id = ?
        ORDER BY res.booking_date DESC
        LIMIT 5
    ');
    $stm->execute([$_user->user_id]);
    $myBookings = $stm->fetchAll();
}

$bookUrl     = $isGuest ? '/login/login.php' : '/page/booking.php';
$bookingsUrl = $isGuest ? '/login/login.php' : '/page/room_history.php';
$roomsUrl    = $isGuest ? '/login/login.php' : '/page/room_detail.php';
?>

<div class="user-home">
    <h2 class="user-home__welcome"><?= $isGuest ? 'Welcome to ABC Library' : 'Welcome, ' . htmlspecialchars($_user->name) ?></h2>
    <?php if ($isGuest): ?>
        <p style="color:#6b7280; margin-top:-10px; margin-bottom:24px;">
            Log in to book a study room and manage your reservations.
        </p>
    <?php endif ?>

    <div class="user-home__actions">
        <a href="<?= $bookUrl ?>" class="action-btn action-btn--primary">+ Book a Room</a>
        <a href="<?= $bookingsUrl ?>" class="action-btn action-btn--secondary">My Bookings</a>
    </div>

    <div class="user-home__grid">

        <!-- Recent bookings -->
        <div class="user-home__main">
            <h3 class="section-title">My Recent Bookings</h3>
            <?php if ($isGuest): ?>
                <p class="empty-state">
                    <a href="/login/login.php" class="auth-link">Log in</a> to see your bookings.
                </p>
            <?php elseif (!$myBookings): ?>
                <p class="empty-state">You haven't booked anything yet.</p>
            <?php else: ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Room</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myBookings as $b): ?>
                                <?php
                                    $display_status = $b->status;
                                    if ($display_status === 'confirm' && $b->booking_date < date('Y-m-d')) {
                                        $display_status = 'completed';
                                    }

                                    $pill_class = match ($display_status) {
                                        'confirm'   => 'status-pill--confirm',
                                        'completed' => 'status-pill--completed',
                                        'cancel'    => 'status-pill--cancel',
                                        default     => 'status-pill--other',
                                    };
                                ?>
                                <tr>
                                    <td><?= htmlspecialchars($b->room_name ?? '—') ?></td>
                                    <td><?= $b->booking_date ?></td>
                                    <td><?= date('g:i A', strtotime($b->start_time)) ?> - <?= date('g:i A', strtotime($b->end_time)) ?></td>
                                    <td>
                                        <span class="status-pill <?= $pill_class ?>">
                                            <?= htmlspecialchars($display_status) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach ?>
                        </tbody>
                    </table>
                </div>
            <?php endif ?>
        </div>

        <!-- Available rooms preview -->
        <div class="user-home__side">
            <h3 class="section-title">Available Rooms</h3>
            <?php foreach ($rooms as $r): ?>
                <?php if ($isGuest): ?>
                    <a href="/login/login.php" class="room-card" style="display:block; text-decoration:none; color:inherit;">
                        <div class="room-card__name"><?= htmlspecialchars($r->name) ?></div>
                        <div class="room-card__capacity">Capacity: <?= $r->capacity ?></div>
                    </a>
                <?php else: ?>
                    <div class="room-card">
                        <div class="room-card__name"><?= htmlspecialchars($r->name) ?></div>
                        <div class="room-card__capacity">Capacity: <?= $r->capacity ?></div>
                    </div>
                <?php endif ?>
            <?php endforeach ?>
            <a href="<?= $roomsUrl ?>" class="auth-link">View all rooms →</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>