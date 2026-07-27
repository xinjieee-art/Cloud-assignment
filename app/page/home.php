<?php
$_title = 'Home';
require __DIR__ . '/../_base.php';
auth('user');
require __DIR__ . '/../_head.php';
?>

<div class="user-home">
    <h2>Welcome, <?= htmlspecialchars($_user->name) ?></h2>

    <!-- TODO: 普通 user 首页的实际内容放这里,例如: -->
    <!-- 图书列表 / 借阅记录 / 房间预约状态 等 -->

    <button>
        <a href="booking.php">Booking reservation</a>
    </button>

     <button>
        <a href="room_history.php">History</a>
    </button>
    <button>
        <a href="room_detail.php">Details</a>
    </button>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>