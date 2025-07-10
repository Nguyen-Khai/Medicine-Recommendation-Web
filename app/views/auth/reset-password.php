<?php if (!empty($_SESSION['error'])): ?>
    <p style="color: #F95454; margin-bottom: 10px; font-weight: bold; position: relative; top: -13px;"><?= $_SESSION['error'];
                                                                                                        unset($_SESSION['error']); ?></p>
<?php endif; ?>
<?php if (!empty($_SESSION['success'])): ?>
    <p style="color:green; margin-bottom: 10px; font-weight: bold; position: relative; top: -13px;"><?= $_SESSION['success'];
                                                                                                    unset($_SESSION['success']); ?></p>
<?php endif; ?>
<h2>Đặt lại mật khẩu mới</h2>
<form method="POST" action="index.php?route=handle-reset-password">
    <input type="password" name="new_password" placeholder="Mật khẩu mới" required>
    <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
    <button type="submit">Cập nhật mật khẩu</button>
</form>