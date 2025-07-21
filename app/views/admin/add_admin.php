<?php
$title = "Add Admin";
ob_start();
?>

<?php if (isset($_SESSION['admin_message'])): ?>
    <div class="alert <?= $_SESSION['admin_message_type'] ?>">
        <?= $_SESSION['admin_message'] ?>
    </div>
    <script>
        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) alert.style.display = 'none';
        }, 3000);
    </script>
    <?php unset($_SESSION['admin_message'], $_SESSION['admin_message_type']); ?>
<?php endif; ?>

<div class="admin-add-container">
    <form action="index.php?route=create-admin" method="POST" enctype="multipart/form-data" class="admin-add-form">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>

        <div class="form-group">
            <label for="role">Role</label>
            <select name="role" id="role" required>
                <option value="staff" selected>Staff</option>
                <option value="manager">Manager</option>
                <option value="superadmin">Super Admin</option>
            </select>
        </div>
        <button type="submit" class="admin-submit-btn">Create Admin</button>
    </form>
</div>