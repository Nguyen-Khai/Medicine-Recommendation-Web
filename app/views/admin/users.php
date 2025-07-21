<div class="admin-users-container">
    <form method="get" action="index.php" class="admin-users-controls" style="display: flex; gap: 10px;">
        <input type="hidden" name="route" value="admin-users">

        <input
            type="text"
            class="admin-search-input"
            name="keyword"
            placeholder="Search by username or email..."
            value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">

        <select name="status">
            <option value="">All Status</option>
            <option value="active" <?= (($_GET['status'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($_GET['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
        </select>

        <button type="submit">Search</button>
    </form>

    <table class="admin-users-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Username</th>
                <th>Email</th>
                <th>Registered Date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $index => $user): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= date('d/m/Y', strtotime($user['created_at'])) ?></td>
                        <td><?= $user['is_active'] ? 'Active' : 'Inactive' ?></td>
                        <td>
                            <?php if ($user['is_active']): ?>
                                <a href="index.php?route=deactivate-user&id=<?= $user['id'] ?>" class="action-btn delete-btn">Deactivate</a>
                            <?php else: ?>
                                <a href="index.php?route=activate-user&id=<?= $user['id'] ?>" class="action-btn edit-btn">Activate</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No users found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>