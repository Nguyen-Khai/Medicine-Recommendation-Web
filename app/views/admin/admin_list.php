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
                    <button class="btn-delete" onclick="deleteAdmin(<?= $admin['id'] ?>)">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    function deleteAdmin(adminId) {
        Swal.fire({
            title: 'Are you sure you want to delete this admin?',
            text: "This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "index.php?route=delete-admin&id=" + adminId;
            }
        });
    }
</script>
