<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login / Register</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter';
            background: linear-gradient(270deg,
                    rgba(119, 205, 255, 1) 0%,
                    rgba(174, 225, 254, 1) 33%,
                    rgba(191, 231, 254, 1) 63%,
                    rgba(226, 243, 253, 1) 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .main-header .logo {
            font-size: 24px;
            font-weight: bold;
            color: #ffffff;
            position: relative;
            float: left;
        }

        img.logo {
            width: 60px;
            position: absolute;
            top: 16px;
            left: 20px;
        }

        h1.logo {
            position: absolute;
            left: 84px;
            top: 26px;
            color: #F95454;
        }

        h1 {
            position: absolute;
            top: 30px;
            left: 120px;
        }

        .ng-nhp {
            text-align: center;
            position: relative;
            width: 100%;
        }

        .tab-container {
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            font-size: 30px;
            font-weight: 600;
            gap: 60px;
            margin-bottom: 40px;
        }

        .tab {
            cursor: pointer;
            transition: color 0.3s ease;
            color: #1e90ff;
        }

        .tab.active {
            color: #d62828;
        }

        .pill-icon {
            position: absolute;
            left: 55%;
            transform: translateX(-110px);
            transition: transform 0.5s ease;
            width: 36px;
        }

        .tab-container.register-active .pill-icon {
            transform: translateX(58px);
        }

        .container-forms {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            position: relative;
            width: calc(100% - 20px);
            left: 10px;
            bottom: 10px;
        }

        .form-section {
            display: none;
            justify-content: center;
            align-items: center;
            min-height: 300px;
            width: 100%;
        }

        .active-form {
            display: flex;
        }

        .form-section form {
            border: 1px solid black;
            border-radius: 5px;
            padding: 50px;
            background-color: rgba(255, 255, 255, 0.9);
            max-height: 75vh;
        }

        .login {
            margin-bottom: 30px;
            position: relative;
        }

        input {
            height: 60px;
            width: 400px;
            outline: none;
            border: 1px solid black;
            padding: 10px;
            border-radius: 5px;
            font-size: 15px;
            padding-top: 20px;
        }

        label {
            position: absolute;
            padding: 0px 5px;
            left: 10px;
            top: 50%;
            pointer-events: none;
            transform: translateY(-50%);
            font-size: 16px;
            background-color: transparent;
            transition: all 0.3s ease-in-out;
        }

        .login input:focus {
            border: 2px solid #1e90ff;
        }

        .login input:focus+label,
        .login input:valid+label {
            top: 13px;
            font-size: 15px;
            font-weight: 500;
            color: #1e90ff;
        }

        button {
            width: 180px;
            height: 50px;
            background-color: #c62e2e;
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            position: relative;
            top: 10px;
        }

        button.button_register {
            width: 180px;
            height: 50px;
            background-color: #c62e2e;
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 18px;
            cursor: pointer;
            position: relative;
            top: -20px;
        }

        button:hover {
            background-color: #F95454;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
            transition: all 0.3s ease;
        }

        /* Giới tính */
        .gender-group {
            margin-bottom: 30px;
            position: relative;
        }

        .gender-group input[type="radio"] {
            margin-right: 8px;
        }

        input.gender {
            height: 40px;
            width: 110px;
            position: relative;
            left: 41px;
        }

        label.gender_Male {
            position: absolute;
            padding: 0px 5px;
            left: 105px;
            pointer-events: none;
        }

        label.gender_Female {
            position: absolute;
            padding: 0px 5px;
            left: 210px;
            pointer-events: none;
        }

        label.date {
            position: absolute;
            padding: 0px 5px;
            left: 10px;
            top: 20%;
            pointer-events: none;
            transform: translateY(-50%);
            font-size: 16px;
            background-color: transparent;
            transition: all 0.3s ease-in-out;
        }

        /* Chia 2 cột bên đăng kí */
        .register-columns {
            display: flex;
            gap: 40px;
            flex-wrap: wrap;
        }

        .register-left,
        .register-right {
            display: flex;
            flex-direction: column;
        }

        /* Đảm bảo mỗi cột chiếm 50% */
        .register-left,
        .register-right {
            flex: 1;
            min-width: 250px;
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

        /* Quên mật khẩu */
        a.forgot_password {
            color: red;
            position: relative;
            left: 130px;
            bottom: 10px;
            text-decoration: none;
        }

        /* Nhớ mật khẩu */
        .remember-me {
            margin-top: 10px;
            font-size: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }

        input#remember {
            height: 30px;
            width: 30px;
            outline: none;
            position: absolute;
            top: 65%;
            pointer-events: auto;
            z-index: 1;
        }

        label.remember {
            position: absolute;
            padding: 0px 5px;
            left: 470px;
            top: 69%;
        }

        /* Ảnh */
        .ng-nhp .img {
            width: 450px;
            height: 450px;
            left: 30px;
            bottom: -18px;
            position: absolute;
            object-fit: cover;
        }

        img.img_box {
            width: 200px;
            height: 200px;
            left: 900px;
            bottom: -25px;
            position: absolute;
            object-fit: cover;
        }

        img.img_re {
            width: 450px;
            height: 450px;
            left: -117px;
            bottom: 0px;
            position: absolute;
            object-fit: cover;
        }

        img.img_box_re {
            width: 180px;
            height: 180px;
            right: 10px;
            bottom: -6px;
            position: absolute;
            object-fit: cover;
        }

        @media (max-width: 768px) {
            .ng-nhp .img,
            img.img_box,
            img.img_re,
            img.img_box_re {
                display: block;
                margin: 20px auto;
            }
        }
    </style>
</head>

<body>
    <div class="logo">
        <img class="logo" src="assets/images/logo.png" alt="">
        <h1 class="logo">HealMate</h1>
    </div>
    <div class="ng-nhp">
        <div class="tab-container" id="tabContainer">
            <div class="tab login-tab active" onclick="switchTab('login')">Login</div>
            <div class="tab register-tab" onclick="switchTab('register')">Register</div>
            <img class="pill-icon" src="assets/images/image-1.png" />
        </div>

        <div class="container-forms">
            <div id="login-form" class="form-section active-form">
                <?php if (isset($error)): ?>
                    <div style="color: #F95454; margin-bottom: 10px; font-weight: bold; position: absolute; top: 10px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($success)): ?>
                    <div style="color: green; font-weight: bold; margin-bottom: 10px; position: absolute; top: 12px;">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['success'])): ?>
                    <p style="color:green;  margin-bottom: 10px; font-weight: bold; position: absolute; top: 10px;"><?= $_SESSION['success'];
                                                                                                                    unset($_SESSION['success']); ?></p>
                <?php endif; ?>
                <form action="index.php?route=handle-login" method="POST">
                    <div class="login">
                        <input type="text" name="username" id="login-username" required />
                        <label>Username</label>
                    </div>
                    <div class="forgot_password">
                        <a class="forgot_password" href="index.php?route=forgot_password">Forgot Password?</a>
                    </div>
                    <div class="login">
                        <input type="password" id="login-password" name="password" required />
                        <label>Password</label>
                        <span type="button" class="toggle-password" onclick="togglePassword('login-password', this)">
                            <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                        </span>
                    </div>
                    <button>Login</button>
                </form>
                <img class="img" src="assets/images/doctor.png" />
                <img class="img_box" src="assets/images/medical box.png" />
            </div>

            <div id="register-form" class="form-section">
                <?php if (isset($error)): ?>
                    <div style="color: red; font-weight: bold; margin-bottom: 10px; position: absolute; top: 12px;">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                <form action="index.php?route=handle-register" method="POST">
                    <div class="register-columns">
                        <!-- Cột trái: 4 trường -->
                        <div class="register-left">
                            <div class="login">
                                <input type="text" name="username" required />
                                <label>Username</label>
                            </div>
                            <div class="login">
                                <input type="text" name="nameuser" required />
                                <label>Full Name</label>
                            </div>
                            <div class="login">
                                <input type="date" name="dob" required />
                                <label class="date">Birthday</label>
                            </div>
                            <div class="gender-group">
                                <label>Gender</label>
                                <input class="gender" type="radio" name="gender" value="Male" required />
                                <label class="gender_Male">Male</label>
                                <input class="gender" type="radio" name="gender" value="FeMale" required />
                                <label class="gender_Female">Female</label>
                            </div>
                        </div>

                        <!-- Cột phải: 3 trường -->
                        <div class="register-right">
                            <div class="login">
                                <input type="email" name="email" required />
                                <label>Email</label>
                            </div>
                            <div class="login password-wrapper">
                                <input type="password" id="register-password" name="password" required />
                                <label>Password</label>
                                <span type="button" class="toggle-password" onclick="togglePassword('register-password', this)">
                                    <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                                </span>
                            </div>
                            <div class="login password-wrapper">
                                <input type="password" id="confirm-password" name="confirm-password" required />
                                <label>Confirm Password</label>
                                <span type="button" class="toggle-password" onclick="togglePassword('confirm-password', this)">
                                    <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                                </span>
                            </div>
                        </div>
                    </div>
                    <button class="button_register">Register</button>
                </form>
                <img class="img_re" src="assets/images/nurses.png" />
                <img class="img_box_re" src="assets/images/patient records.png" />
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const loginTab = document.querySelector('.login-tab');
            const registerTab = document.querySelector('.register-tab');
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');
            const container = document.getElementById('tabContainer');

            if (tab === 'register') {
                loginTab.classList.remove('active');
                registerTab.classList.add('active');
                container.classList.add('register-active');
                loginForm.classList.remove('active-form');
                registerForm.classList.add('active-form');
            } else {
                registerTab.classList.remove('active');
                loginTab.classList.add('active');
                container.classList.remove('register-active');
                registerForm.classList.remove('active-form');
                loginForm.classList.add('active-form');
            }
        }

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
    <?php if (isset($stayOnRegister) && $stayOnRegister): ?>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                switchTab('register');
            });
        </script>
    <?php endif; ?>
</body>

</html>