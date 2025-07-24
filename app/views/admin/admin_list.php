<form method="GET" action="index.php" class="admin-filter-form">
    <input type="hidden" name="route" value="admins">

    <input type="text" name="search" placeholder="Search by username..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

    <select name="role">
        <option value="">All roles</option>
        <option value="superadmin" <?= ($_GET['role'] ?? '') === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
        <option value="manager" <?= ($_GET['role'] ?? '') === 'manager' ? 'selected' : '' ?>>Manager</option>
        <option value="staff" <?= ($_GET['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff</option>
    </select>

    <button type="submit">Filter</button>
</form>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?= $admin['id'] ?></td>
                <td><?= htmlspecialchars($admin['username']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td>
                    <?php
                    $role = strtolower($admin['role']);
                    $badgeClass = $role === 'superadmin' ? 'superadmin' : ($role === 'manager' ? 'manager' : 'staff');
                    ?>
                    <span class="role-badge <?= $badgeClass ?>">
                        <i class="fas fa-user-shield"></i> <?= ucfirst($role) ?>
                    </span>
                </td>
                <td class="action-buttons">
                    <a href="index.php?route=edit-admin&id=<?= $admin['id'] ?>">
                        <button class="btn-edit"><i class="fas fa-edit"></i> Edit</button>
                    </a>

                    <?php if ($_SESSION['admin']['role'] === 'superadmin' && $admin['role'] !== 'superadmin'): ?>
                        <button class="btn-delete" onclick="deleteAdmin(<?= $admin['id'] ?>, '<?= $admin['username'] ?>')">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<style>
    .admin-filter-form {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        align-items: center;
        flex-wrap: wrap;
    }

    .admin-filter-form input,
    .admin-filter-form select {
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 8px;
        border: 1px solid #ccc;
        outline: none;
    }

    .admin-filter-form input:focus,
    .admin-filter-form select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
    }

    .admin-filter-form button {
        background-color: #007bff;
        color: white;
        border: none;
        font-weight: 500;
        transition: background-color 0.2s ease;
        cursor: pointer;
        padding: 10px 14px;
        font-size: 14px;
        border-radius: 8px;
    }

    .admin-filter-form button:hover {
        background-color: #0056b3;
    }
</style>
<script>
    function deleteAdmin(adminId, adminName) {
        Swal.fire({
            title: 'Are you sure?',
            html: `You are about to delete <b style="color:#d33;">${adminName}</b>. This action cannot be undone.`,
            imageUrl: 'assets/images/question_mask.png',
            imageWidth: 100,
            imageHeight: 100,
            imageAlt: 'Warning Image',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `index.php?route=delete-admin&id=${adminId}`;
            }
        });
    }
</script>