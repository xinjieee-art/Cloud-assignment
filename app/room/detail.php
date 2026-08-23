<?php
$_title = 'Room Detail';
require __DIR__ . '/../_base.php';
auth('admin', 'staff');
require __DIR__ . '/../_head.php';

$stm = $_db->query('SELECT * FROM room ORDER BY name');
$list = $stm->fetchAll();
?>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="/admin/home.php">Home</a>
        <a href="/room/detail.php" class="active">Room</a>
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
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1rem;">
            <h2 style="margin:0;">Room List</h2>
            <input type="text" id="searchBox" placeholder="Search rooms..."
                   style="flex:1; max-width:320px; margin:0 20px; box-sizing:border-box; padding:8px 12px; border:1px solid #e5e7eb; border-radius:8px; font-size:14px;">
            <a href="/room/insert.php" class="auth-btn-primary" style="display:inline-block; width:auto; padding:8px 16px; text-decoration:none;">
                + Add Room
            </a>
        </div>

        <table style="width:100%; border-collapse:collapse;">
            <thead>
                <tr style="text-align:left; border-bottom:1px solid #ddd;">
                    <th style="padding:8px;">Name</th>
                    <th style="padding:8px;">Description</th>
                    <th style="padding:8px;">Capacity</th>
                    <th style="padding:8px;">Status</th>
                    <th style="padding:8px;">Actions</th>
                </tr>
            </thead>
            <tbody id="searchableRows">
                <?php foreach ($list as $r): ?>
                    <tr style="border-bottom:1px solid #f0f0f0;" data-search="<?= htmlspecialchars(strtolower($r->name . ' ' . $r->description . ' ' . $r->status)) ?>">
                        <td style="padding:8px;"><?= htmlspecialchars($r->name) ?></td>
                        <td style="padding:8px;"><?= htmlspecialchars($r->description) ?></td>
                        <td style="padding:8px;"><?= $r->capacity ?></td>
                        <td style="padding:8px;">
                            <span style="color:<?= $r->status === 'available' ? 'green' : 'red' ?>;">
                                <?= htmlspecialchars($r->status) ?>
                            </span>
                        </td>
                        <td style="padding:8px;">
                            <a href="/room/update.php?id=<?= $r->room_id ?>">Edit</a>
                            &nbsp;|&nbsp;
                            <a href="/room/delete.php?id=<?= $r->room_id ?>"
                               onclick="return confirm('Delete this room?')"
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