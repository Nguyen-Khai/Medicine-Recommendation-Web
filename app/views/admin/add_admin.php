<?php
$title = "Add Admin";
ob_start();
?>
<div class="admin-add-container">
    <h2 class="admin-add-title">Tạo tài khoản Admin mới</h2>
    <form action="create_admin.php" method="POST" class="admin-add-form">
        <div class="form-group">
            <label for="username">Tên đăng nhập</label>
            <input type="text" name="username" id="username" required>
        </div>

        <div class="form-group">
            <label for="email">Địa chỉ Email</label>
            <input type="email" name="email" id="email" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" name="password" id="password" required>
        </div>

        <button type="submit" class="admin-submit-btn">Tạo Admin</button>
    </form>
</div>
