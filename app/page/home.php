<?php
$_title = 'Home';
require __DIR__ . '/../_base.php';
auth('user');
require __DIR__ . '/../_head.php';

// Available rooms (preview)
$rooms = $_db->query("SELECT * FROM room WHERE status = 'available' LIMIT 4")->fetchAll();

// This user's recent reservations
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
?>

<div class="user-home">
    <h2 class="user-home__welcome">Welcome, <?= htmlspecialchars($_user->name) ?></h2>

    <div class="user-home__actions">
        <a href="/page/booking.php" class="action-btn action-btn--primary">+ Book a Room</a>
        <a href="/page/room_history.php" class="action-btn action-btn--secondary">My Bookings</a>
    </div>

    <div class="user-home__grid">

        <!-- Recent bookings -->
        <div class="user-home__main">
            <h3 class="section-title">My Recent Bookings</h3>
            <?php if (!$myBookings): ?>
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
                                <tr>
                                    <td><?= htmlspecialchars($b->room_name ?? '—') ?></td>
                                    <td><?= $b->booking_date ?></td>
                                    <td><?= date('g:i A', strtotime($b->start_time)) ?> - <?= date('g:i A', strtotime($b->end_time)) ?></td>
                                    <td>
                                        <span class="status-pill <?= $b->status === 'confirm' ? 'status-pill--confirm' : 'status-pill--other' ?>">
                                            <?= htmlspecialchars($b->status) ?>
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
                <div class="room-card">
                    <div class="room-card__name"><?= htmlspecialchars($r->name) ?></div>
                    <div class="room-card__capacity">Capacity: <?= $r->capacity ?></div>
                </div>
            <?php endforeach ?>
            <a href="/page/room_detail.php" class="auth-link">View all rooms →</a>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>