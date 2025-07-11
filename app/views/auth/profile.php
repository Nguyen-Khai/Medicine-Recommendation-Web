<?php
$avatar = $userInfo['avatar'] ?? null;
$base64 = $avatar ? 'data:image/png;base64,' . base64_encode($avatar) : 'default-avatar.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>My information</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css">
    <style>
        body {
            margin: 0;
            font-family: 'Merriweather', serif;
            padding-top: 150px;
            background: linear-gradient(to bottom, #3A59D1, #1B56FD, #0D92F4, #60B5FF, #77CDFF, #3A59D1);
            background-size: 100% 1000%;
            animation: waveGradient 20s ease infinite;
        }

        @keyframes waveGradient {
            0% {
                background-position: 50% 0%;
            }

            50% {
                background-position: 50% 100%;
            }

            100% {
                background-position: 50% 0%;
            }
        }

        /*Main*/
        .profile-page {
            display: flex;
            height: calc(100vh - 150px);
        }

        aside.sidebar {
            width: 250px;
            color: white;
            padding: 20px;
        }

        aside.sidebar ul {
            list-style: none;
            padding: 0;
        }

        aside.sidebar li {
            margin: 10px 0;
        }

        aside.sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 5px 10px;
            border-radius: 4px;
        }

        aside.sidebar a:hover {
            color: #C62E2E;
        }

        main.content {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: white;
            border-radius: 30px;
            margin: 10px;
        }

        main.content section {
            display: none;
        }

        main.content section:target {
            display: block;
        }

        input[type="password"] {
            padding: 10px;
            width: 300px;
            max-width: 100%;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #2e86de;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        /*Hồ sơ*/
        #profile {
            max-width: 500px;
            margin: auto;
        }

        #profile h2 {
            text-align: center;
            font-size: 1.8rem;
            color: #F95454;
        }

        .avatar-section {
            text-align: center;
            margin-bottom: 20px;
        }

        .avatar-section img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #ccc;
            margin-bottom: 10px;
        }

        .avatar-section a {
            display: inline-block;
            color: #2e86de;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }

        button#change-avatar-btn {
            width: 404px !important;
            background: none !important;
            color: #1e90ff !important;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        #profile button {
            width: 100%;
            padding: 12px;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
        }

        #profile button:hover {
            background-color: #0d6efd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }

        .gender-options {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        .gender-options label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 15px;
            cursor: pointer;
        }

        .gender-options input[type="radio"] {
            accent-color: #007BFF;
            /* màu xanh nổi bật */
            width: 16px;
            height: 16px;
        }

        /*Đổi mật khẩu*/
        #change-password {
            max-width: 420px;
            margin: -36px auto;
        }

        #change-password h2 {
            text-align: center;
            font-size: 1.8rem;
            color: #F95454;
        }

        .password-form .form-group {
            position: relative;
        }

        .password-form input {
            height: 45px;
            width: 400px;
            border: 1px solid #aaa;
            border-radius: 5px;
            font-size: 18px;
            font-family: 'Merriweather', serif;
            outline: none;
        }

        .password-form input:focus {
            border: 2px solid #1e90ff;
        }

        .password-form label {
            position: absolute;
            left: 12px;
            top: 40%;
            transform: translateY(-50%);
            background-color: white;
            padding: 0 5px;
            font-size: 16px;
            color: #777;
            pointer-events: none;
            transition: all 0.25s ease;
        }

        .password-form input:focus+label,
        .password-form input:valid+label {
            top: 5px;
            font-size: 14px;
            color: #1e90ff;
            font-weight: 500;
        }

        .password-form button {
            width: 100%;
            padding: 14px;
            font-size: 1.1rem;
            font-weight: 600;
            background-color: #1e90ff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .password-form button:hover {
            background-color: #0d6efd;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            transform: scale(1.02);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
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

        /*Lịch sử tìm kiếm*/
        #search-history h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #F95454;
            font-weight: 600;
            font-size: 1.7rem;
            letter-spacing: 0.02em;
        }

        #search-history ul {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }

        #search-history ul li {
            position: relative;
            padding: 14px 120px 14px 175px;
            border-bottom: 1px solid #eee;
            font-size: 1rem;
            color: #444;
            line-height: 1.4;
            transition: background-color 0.2s ease;
            cursor: default;
        }

        #search-history ul li:last-child {
            border-bottom: none;
        }

        #search-history ul li:hover {
            background-color: #f5faff;
            color: #F95454;
        }

        /* Icon đồng hồ bên trái */
        #search-history ul li::before {
            content: "🕒";
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1rem;
        }

        /* Thời gian từ data-time */
        #search-history ul li::after {
            content: attr(data-time);
            position: absolute;
            left: 38px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.85rem;
            color: #888;
            font-family: monospace;
        }

        /* Link chi tiết bên phải */
        .detail-link {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.95rem;
            color: #0077cc;
            text-decoration: none;
            font-weight: 500;
        }

        .detail-link:hover {
            text-decoration: underline;
            color: #005fa3;
        }

        /*Lịch sử tư vấn*/
        #advice-history h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #F95454;
            font-weight: 600;
            font-size: 1.7rem;
            letter-spacing: 0.02em;
        }

        #advice-history ul {
            list-style-type: none;
        }

        #advice-history ul li {
            position: relative;
            padding: 14px 120px 14px 18px;
            /* Chừa khoảng bên phải cho link */
            border-bottom: 1px solid #eee;
            font-size: 1rem;
            color: #444;
            line-height: 1.4;
            transition: background-color 0.2s ease;
            cursor: default;
        }

        #advice-history ul li:last-child {
            border-bottom: none;
        }

        #advice-history ul li:hover {
            background-color: #f0f8ff;
            color: #F95454;
        }

        .detail-link {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 0.95rem;
            color: #0077cc;
            text-decoration: none;
            font-weight: 500;
        }

        .detail-link:hover {
            text-decoration: underline;
            color: #005fa3;
        }

        input {
            max-width: 400px;
            outline: none;
            font-family: 'Merriweather', serif;
        }

        input:focus {
            border-color: #1e90ff;
            box-shadow: 0 0 5px rgba(30, 144, 255, 0.5);
        }

        /*icon*/
        .nav-item {
            display: flex;
            align-items: center;
        }

        .nav-item img {
            width: 20px;
            height: 20px;
            margin-bottom: 2px;
        }
    </style>
</head>

<body>
    <?php
    include('header.php')
    ?>
    <div class="profile-page">
        <aside class="sidebar">
            <h2>My account</h2>
            <ul>
                <li><strong>My information</strong></li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=RH2knxpdDpjm&format=png&color=000000" alt="icon">
                    <a href="#profile">Profile</a>
                </li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=8OdwzXFjBVH2&format=png&color=000000" alt="icon">
                    <a href="#change-password">Change password</a>
                </li>
                <li><strong>History</strong></li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=ObuWtTlsoTj6&format=png&color=000000" alt="icon">
                    <a href="#search-history">Search history</a>
                </li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=PyrI7GCv8LCv&format=png&color=000000" alt="icon">
                    <a href="#advice-history">Recommendation history</a>
                </li>
            </ul>
        </aside>
        <main class="content">
            <section id="profile">
                <h2>Profile</h2>
                <form id="profile-form" method="POST" action="index.php?route=update-profile" enctype="multipart/form-data">
                    <div class="avatar-section">
                        <img id="avatar-preview" src="<?= $base64 ?>" alt="Ảnh đại diện">
                        <button type="button" id="change-avatar-btn">Change Avatar</button>
                        <input type="file" name="avatar" id="avatar-input" accept="image/*" hidden>
                    </div>
                    <div class="form-group">
                        <label for="fullname">Full name:</label>
                        <input type="text" id="fullname" name="name" value="<?= htmlspecialchars($userInfo['name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= htmlspecialchars($userInfo['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="dob">Birthday:</label>
                        <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($userInfo['dob'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Gender:</label>
                        <div class="gender-options">
                            <label>
                                <input type="radio" name="gender" value="male" <?= (isset($userInfo['gender']) && $userInfo['gender'] == 'male') ? 'checked' : '' ?>>
                                Male
                            </label>
                            <label>
                                <input type="radio" name="gender" value="female" <?= (isset($userInfo['gender']) && $userInfo['gender'] == 'female') ? 'checked' : '' ?>>
                                Female
                            </label>
                        </div>
                    </div>
                    <button type="submit">Update</button>
                </form>
            </section>
            <section id="change-password">
                <h2>Change password</h2>
                <?php if (!empty($_SESSION['error'])): ?>
                    <p style="color: #F95454; margin-bottom: 10px; font-weight: bold; position: relative; left: 94px; bottom: 11px;"><?= $_SESSION['error'];
                                            unset($_SESSION['error']); ?></p>
                <?php endif; ?>
                <?php if (!empty($_SESSION['success'])): ?>
                    <p style="color:green; margin-bottom: 10px; font-weight: bold; position: relative; left: 94px; bottom: 11px;"><?= $_SESSION['success'];
                                            unset($_SESSION['success']); ?></p>
                <?php endif; ?>
                <form class="password-form" method="POST" action="index.php?route=change-password">
                    <div class="form-group">
                        <input type="password" id="current-password" name="current-password" required />
                        <label for="current-password">Current password</label>
                        <span type="button" class="toggle-password" onclick="togglePassword('current-password', this)">
                            <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                        </span>
                    </div>
                    <div class="form-group">
                        <input type="password" id="new-password" name="new-password" required />
                        <label for="new-password">New password</label>
                        <span type="button" class="toggle-password" onclick="togglePassword('new-password', this)">
                            <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                        </span>
                    </div>
                    <div class="form-group">
                        <input type="password" id="confirm-password" name="confirm-password" required />
                        <label for="confirm-password">Confirm password</label>
                        <span type="button" class="toggle-password" onclick="togglePassword('confirm-password', this)">
                            <img src="assets/images/close eye.png" alt="Hiện mật khẩu" class="eye-icon" />
                        </span>
                    </div>
                    <button type="submit">Update password</button>
                </form>
            </section>
            <section id="search-history">
                <h2>Search history</h2>

                <div class="input-with-icon">
                    <input type="text" id="search-filter" placeholder="Tìm kiếm trong lịch sử tìm kiếm...">
                    <svg class="icon-search" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                    </svg>
                </div>

                <ul id="search-history-list">
                    <?php if (!empty($searchHistories)): ?>
                        <?php foreach ($searchHistories as $item): ?>
                            <li data-time="<?= date(' d/m/Y H:i', strtotime($item['created_at'])) ?>">
                                <?= htmlspecialchars($item['keyword']) ?>
                                <a href="index.php?route=search&query=<?= urlencode($item['keyword']) ?>" class="detail-link">Search now</a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li>There's no search history.</li>
                    <?php endif; ?>
                </ul>
            </section>
            <section id="advice-history">
                <h2>Recommendation history</h2>
                <div class="input-with-icon">
                    <input type="text" id="search-filter" placeholder="Tìm kiếm trong lịch sử tư vấn...">
                    <svg class="icon-search" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                    </svg>
                </div>
                <?php if (!empty($userHistories)): ?>
                    <ul>
                        <?php foreach ($userHistories as $record): ?>
                            <li>
                                <?= htmlspecialchars($record['predicted_disease']) ?> –
                                recommend at <?= date('H:i \n\g\à\y d/m/Y', strtotime($record['created_at'])) ?>
                                <a href="index.php?route=history-detail&id=<?= $record['id'] ?>" class="detail-link">Details</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>There's no history</p>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script>
        function toggleDropdown() {
            const menu = document.getElementById("dropdown-menu");
            menu.style.display = menu.style.display === "block" ? "none" : "block";
        }

        function logout() {
            Swal.fire({
                title: 'Bạn có chắc muốn đăng xuất?',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Không',
                confirmButtonText: 'Có',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "login.html";
                }
            });
        }
        window.addEventListener("click", function(e) {
            const avatar = document.querySelector(".avatar");
            const menu = document.getElementById("dropdown-menu");
            if (!avatar.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = "none";
            }
        });

        function toggleDropdown() {
            const menu = document.getElementById("dropdown-menu");
            if (menu.style.display === "block") {
                menu.style.display = "none";
            } else {
                menu.style.display = "block";
            }
        }

        // Tìm kiếm
        const openBtn = document.getElementById('openSearchBtn');
        const closeBtn = document.getElementById('closeSearchBtn');
        const sidebar = document.getElementById('searchSidebar');

        openBtn.addEventListener('click', () => {
            sidebar.classList.add('open');
        });

        closeBtn.addEventListener('click', () => {
            sidebar.classList.remove('open');
        });

        //Lịch sử tìm kiếm
        document.getElementById('search-filter').addEventListener('input', function() {
            const keyword = this.value.toLowerCase();
            const items = document.querySelectorAll('#search-history-list li');

            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(keyword) ? 'list-item' : 'none';
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const changeAvatarBtn = document.getElementById('change-avatar-btn');
            const avatarInput = document.getElementById('avatar-input');
            const avatarPreview = document.getElementById('avatar-preview');

            if (changeAvatarBtn && avatarInput && avatarPreview) {
                changeAvatarBtn.addEventListener('click', function() {
                    avatarInput.click();
                });

                avatarInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            } else {
                console.warn("❌ Một trong các phần tử avatar không được tìm thấy!");
            }
        });

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