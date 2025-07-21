<?php
$title = "Settings";
ob_start();
?>

<div class="admin-settings-container">
    <h2 class="admin-title">Settings</h2>

    <?php if (isset($_SESSION['admin'])): ?>
        <div class="settings-section">
            <h3>Personal Information</h3>
            <p><strong>Username:</strong> <?= htmlspecialchars($_SESSION['admin']['username']) ?></p>
            <p><strong>Role:</strong> <?= htmlspecialchars($_SESSION['admin']['role']) ?></p>
        </div>

        <div class="settings-section">
            <h3>Change Password</h3>
            <form method="POST" action="index.php?route=admin-update-password" class="settings-form">
                <label>Current Password</label>
                <input type="password" name="current_password" required>

                <label>New Password</label>
                <input type="password" name="new_password" required>

                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required>

                <button type="submit">Update Password</button>
            </form>
        </div>

        <?php if ($_SESSION['admin']['role'] === 'superadmin'): ?>
        <div class="settings-section">
            <h3>System Settings</h3>
            <p>(Reserved for superadmin - coming soon)</p>
        </div>
        <?php endif; ?>
    <?php else: ?>
        <p>You must be logged in as admin to view this page.</p>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include '../app/views/admin/home.php';
?>
