<?php
$_title = '';
require __DIR__ . '/../_base.php';
auth('admin');
require __DIR__ . '/../admin/_head.php';

$stm = $_db->query('SELECT * FROM staff ORDER BY name');
$list = $stm->fetchAll();
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/reserve/insert.php">Booking reservation</a>
        <a href="/staff/details.php" class="active">Staff</a>
        <a href="/slot/details.php">Time Slot</a>
        <a href="/logout.php">Logout</a>

        <a href="/admin/profile.php" class="sidebar-user">
            <img src="/images/profile/<?= $_user->profile ?>">
            <div><?= htmlspecialchars($_user->name) ?></div>
        </a>
    </aside>

    <section class="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="margin:0;">Staff List</h2>
            <a href="/staff/insert.php" class="auth-btn-primary" style="display:inline-block; width:auto; padding:8px 16px; text-decoration:none;">
                + Add Staff
            </a>
        </div>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #ddd;">
                    <th style="padding:8px;">Photo</th>
                    <th style="padding:8px;">Name</th>
                    <th style="padding:8px;">Email</th>
                    <th style="padding:8px;">Gender</th>
                    <th style="padding:8px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $s): ?>
                    <tr style="border-bottom:1px solid #f0f0f0;">
                        <td style="padding:8px;">
                            <img src="/images/profile/<?= $s->profile ?>" style="width:36px; height:36px; border-radius:50%; object-fit:cover;">
                        </td>
                        <td style="padding:8px;"><?= htmlspecialchars($s->name) ?></td>
                        <td style="padding:8px;"><?= htmlspecialchars($s->email) ?></td>
                        <td style="padding:8px;"><?= htmlspecialchars($s->gender) ?></td>
                        <td style="padding:8px;">
                            <a href="/staff/update.php?id=<?= $s->staff_id ?>">Edit</a>
                            &nbsp;|&nbsp;
                            <a href="/staff/delete.php?id=<?= $s->staff_id ?>"
                               onclick="return confirm('Delete this staff account?')"
                               style="color:#c0392b;">Delete</a>
                        </td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </section>
</div>

<?php require __DIR__ . '/../_foot.php'; ?>