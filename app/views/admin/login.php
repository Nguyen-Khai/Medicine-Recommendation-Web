<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>HEALMATE - Admin Login</title>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" />
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter';
      background: linear-gradient(to right, #3A59D1, #60B5FF);
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }

    .login-container {
      background: white;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      width: 400px;
      max-width: 90%;
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 25px;
      color: #F95454;
      font-size: 24px;
    }

    .form-group {
      position: relative;
      margin-bottom: 25px;
    }

    .form-group input {
      width: 100%;
      height: 60px;
      border: 1px solid #ccc;
      border-radius: 6px;
      padding: 12px 10px 0 10px;
      font-size: 16px;
    }

    .form-group label {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      font-size: 15px;
      background: white;
      padding: 0 4px;
      color: #888;
      transition: 0.3s;
      pointer-events: none;
    }

    .form-group input:focus {
      border: 2px solid #1e90ff;
    }

    .form-group input:focus+label,
    .form-group input:valid+label {
      top: 10px;
      font-size: 13px;
      font-weight: 600;
      color: #1e90ff;

    }

    .login-btn {
      width: 100%;
      padding: 14px;
      background: #1e90ff;
      color: white;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .login-btn:hover {
      background: #0d6efd;
      transform: scale(1.02);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .error-message {
      color: #F95454;
      text-align: center;
      margin-bottom: 10px;
      font-weight: bold;
      position: relative;
      top: -12px;
    }

    /* Ẩn hiện mật khẩu */
    .password-wrapper {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      top: 55%;
      right: -55px;
      transform: translateY(-50%);
      background: none;
      border: none;
      cursor: pointer;
      font-size: 20px;
    }

    span.toggle-password {
      position: absolute;
      right: 10px;
    }

    .eye-icon {
      pointer-events: none;
      /* Để không chặn sự kiện click */
    }
  </style>
</head>

<body>
  <div class="login-container">
    <h2>Admin Login</h2>

    <?php if (!empty($_SESSION['admin_error'])): ?>
      <p class="error-message"><?= $_SESSION['admin_error'];
                                unset($_SESSION['admin_error']); ?></p>
    <?php endif; ?>

    <form action="index.php?route=admin-login-handler" method="POST">
      <div class="form-group">
        <input type="text" name="username" id="username" required />
        <label for="username">Username</label>
      </div>
      <div class="form-group">
        <input type="password" name="password" id="password" required />
        <label for="password">Password</label>
        <span type="button" class="toggle-password" onclick="togglePassword('password', this)">
          <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
        </span>
      </div>
      <button type="submit" class="login-btn">Login</button>
    </form>
  </div>

  <script>
    function togglePassword(inputId, button) {
      const passwordInput = document.getElementById(inputId);
      const img = button.querySelector("img");

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        img.src = "assets/images/open eye.png"; // Đường dẫn tới icon "ẩn mật khẩu"
        img.alt = "Ẩn mật khẩu";
      } else {
        passwordInput.type = "password";
        img.src = "assets/images/close eye.png"; // Đường dẫn tới icon "hiện mật khẩu"
        img.alt = "Hiện mật khẩu";
      }
    }
  </script>
</body>

</html>