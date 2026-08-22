<?php
$_title = 'Staff Management';
require __DIR__ . '/../_base.php';
auth('admin');
require __DIR__ . '/../_head.php';

$stm = $_db->query('SELECT * FROM staff ORDER BY name');
$list = $stm->fetchAll();
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php">Room</a>
        <a href="/reserve/details.php">Booking reservation</a>
        <a href="/staff/details.php" class="active">Staff</a>
        <a href="/logout.php">Logout</a>

        <a href="/admin/profile.php" class="sidebar-user">
            <img src="/images/profile/<?= $_user->profile ?>">
            <div><?= htmlspecialchars($_user->name) ?></div>
        </a>
    </aside>

    <section class="admin-content">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="margin:0;">Staff List</h2>
            <input type="text" id="searchBox" placeholder="Search staff..."
                   style="flex:1; max-width:320px; margin:0 20px; box-sizing:border-box; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px;">
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
            <tbody id="searchableRows">
                <?php foreach ($list as $s): ?>
                    <tr style="border-bottom:1px solid #f0f0f0;" data-search="<?= htmlspecialchars(strtolower($s->name . ' ' . $s->email)) ?>">
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

<script>
$(document).on('input', '#searchBox', function () {
    const q = $(this).val().toLowerCase().trim();
    $('#searchableRows tr').each(function () {
        $(this).toggle($(this).data('search').includes(q));
    });
});
</script>

<?php require __DIR__ . '/../_foot.php'; ?>