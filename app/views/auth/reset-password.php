<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reset Password</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Merriweather', serif;
            background: linear-gradient(to right, #e0f7fa, #ffffff);
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
            width: 420px;
            max-width: 90%;
            position: relative;
        }

        .login h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #F95454;
        }

        .form-group {
            position: relative;
            margin-bottom: 28px;
        }

        .form-group input {
            height: 60px;
            width: 100%;
            outline: none;
            border: 1px solid #aaa;
            padding: 10px;
            border-radius: 5px;
            font-size: 15px;
            padding-top: 20px;
        }

        .form-group input:focus {
            border: 2px solid #1e90ff;
        }

        .form-group label {
            position: absolute;
            padding: 0px 5px;
            left: 10px;
            top: 50%;
            pointer-events: none;
            transform: translateY(-50%);
            font-size: 16px;
            color: #777;
            background-color: white;
            transition: all 0.3s ease-in-out;
        }

        .form-group input:focus+label,
        .form-group input:valid+label {
            top: 13px;
            font-size: 15px;
            font-weight: 500;
            color: #1e90ff;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 18px;
            color: #888;
        }

        .password-strength {
            font-size: 14px;
            margin-top: -18px;
            margin-bottom: 16px;
            color: #555;
            font-weight: bold;
        }

        .password-strength.weak {
            color: #e74c3c;
            position: relative;
            top: 22px;
        }

        .password-strength.medium {
            color: #f39c12;
            position: relative;
            top: 22px;
        }

        .password-strength.strong {
            color: #2ecc71;
            position: relative;
            top: 22px;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0d6efd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }

        .message {
            margin-bottom: 15px;
            font-weight: bold;
            text-align: center;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .message.error {
            background-color: #ffe5e5;
            color: #d8000c;
        }

        .message.success {
            background-color: #e0f9e0;
            color: #2e7d32;
        }
    </style>
</head>

<body>

    <div class="login">
        <?php if (!empty($_SESSION['error'])): ?>
            <p class="message error"><?= $_SESSION['error'];
                                        unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <p class="message success"><?= $_SESSION['success'];
                                        unset($_SESSION['success']); ?></p>
        <?php endif; ?>

        <h2>Input New Password</h2>

        <form method="POST" action="index.php?route=handle-reset-password">
            <div class="form-group">
                <input type="password" name="new_password" id="new_password" required />
                <label for="new_password">New Password</label>
                <span class="toggle-password" onclick="togglePassword('new_password', this)">
                    <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                </span>
                <div id="strength" class="password-strength"></div>
            </div>

            <div class="form-group">
                <input type="password" name="confirm_password" id="confirm_password" required />
                <label for="confirm_password">Confirm Password</label>
                <span class="toggle-password" onclick="togglePassword('confirm_password', this)">
                    <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                </span>
            </div>

            <button type="submit">Update New Password</button>
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

        document.getElementById('new_password').addEventListener('input', function() {
            const value = this.value;
            const strengthText = document.getElementById('strength');

            let strength = 0;
            if (value.length >= 6) strength++;
            if (/[A-Z]/.test(value)) strength++;
            if (/[0-9]/.test(value)) strength++;
            if (/[^A-Za-z0-9]/.test(value)) strength++;

            switch (strength) {
                case 0:
                case 1:
                    strengthText.textContent = "Strength: Weak";
                    strengthText.className = "password-strength weak";
                    break;
                case 2:
                case 3:
                    strengthText.textContent = "Strength: Medium";
                    strengthText.className = "password-strength medium";
                    break;
                case 4:
                    strengthText.textContent = "Strength: Strong";
                    strengthText.className = "password-strength strong";
                    break;
            }
        });
    </script>

</body>

</html>