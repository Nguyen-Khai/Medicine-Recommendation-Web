<?php $title = "Manage Permissions"; ob_start(); ?>

<?php if (!empty($_SESSION['admin_message'])): ?>
    <div class="alert <?= $_SESSION['admin_message_type'] ?>">
        <?= $_SESSION['admin_message']; unset($_SESSION['admin_message'], $_SESSION['admin_message_type']); ?>
    </div>
<?php endif; ?>

<form action="index.php?route=update-permissions" method="POST">
    <table class="permissions-table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Module</th>
                <th>Read</th>
                <th>Write</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($permissions as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['role']) ?></td>
                    <td><?= htmlspecialchars($p['module']) ?></td>
                    <td><input type="checkbox" name="read[<?= $p['id'] ?>]" <?= $p['can_read'] ? 'checked' : '' ?>></td>
                    <td><input type="checkbox" name="write[<?= $p['id'] ?>]" <?= $p['can_write'] ? 'checked' : '' ?>></td>
                    <td><input type="checkbox" name="delete[<?= $p['id'] ?>]" <?= $p['can_delete'] ? 'checked' : '' ?>></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button type="submit" class="admin-submit-btn">Save Changes</button>
</form>
