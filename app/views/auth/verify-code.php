<?php if (!empty($_SESSION['error'])): ?>
    <p style="color: #F95454; margin-bottom: 10px; font-weight: bold; position: relative; top: -13px;"><?= $_SESSION['error'];
                                                                                                        unset($_SESSION['error']); ?></p>
<?php endif; ?>
<?php if (!empty($_SESSION['success'])): ?>
    <p style="color:green; margin-bottom: 10px; font-weight: bold; position: relative; top: -13px;"><?= $_SESSION['success'];
                                                                                                    unset($_SESSION['success']); ?></p>
<?php endif; ?>
<h2>Nhập mã xác nhận</h2>
<form method="POST" action="index.php?route=verifyResetCode">
    <input type="text" name="code" placeholder="Nhập mã xác nhận" required>
    <button type="submit">Xác nhận</button>
</form>

<p style="margin-top: 12px;">
    <a href="index.php?route=resend-verification-code" style="color: #1e90ff; text-decoration: underline;">
        Chưa nhận được mã? Gửi lại mã xác nhận
    </a>
</p>
