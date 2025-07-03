<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php?route=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Thông Tin Cá Nhân</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            margin: 10px 0;
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

        input#fullname {
            width: 100%;
            padding: 10px 12px;
            margin: 0px;
            max-width: 100%;
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
            height: 40px;
            width: 100%;
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
            top: 12px;
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

        /*Cài đặt*/
        #settings {
            max-width: 400px;
            margin: 40px auto;
            padding: 25px 30px;
            background: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        #settings h2 {
            margin-bottom: 20px;
            color: #F95454;
            font-weight: 600;
            font-size: 1.6rem;
            text-align: center;
        }

        #settings label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 1rem;
            color: #555;
            cursor: pointer;
            margin-bottom: 15px;
            user-select: none;
            position: relative;
            padding-left: 0;
        }

        /* Ẩn checkbox mặc định */
        #settings input[type="checkbox"] {
            opacity: 0;
            width: 0;
            height: 0;
            position: absolute;
        }

        /* Tạo thanh trượt nền */
        #settings input[type="checkbox"]+span {
            position: relative;
            display: inline-block;
            width: 48px;
            height: 26px;
            background-color: #ccc;
            border-radius: 26px;
            transition: background-color 0.3s ease;
        }

        /* Tạo nút trượt */
        #settings input[type="checkbox"]+span::before {
            content: "";
            position: absolute;
            left: 3px;
            top: 3px;
            width: 20px;
            height: 20px;
            background-color: white;
            border-radius: 50%;
            transition: transform 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        /* Khi checkbox được check, đổi màu nền và dịch nút trượt sang phải */
        #settings input[type="checkbox"]:checked+span {
            background-color: #0d6efd;
        }

        #settings input[type="checkbox"]:checked+span::before {
            transform: translateX(22px);
        }

        /*Lịch sử tìm kiếm*/
        #search-history {
            max-width: 480px;
            margin: 40px auto;
            padding: 25px 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

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
            padding: 14px 120px 14px 90px;
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
            color: #222;
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
            color: #F95454;
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
            color: #222;
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

        input[type="text"] {
            width: 100%;
            max-width: 400px;
            padding: 8px 12px;
            margin: 12px 0 20px 0;
            font-size: 16px;
            border-radius: 5px;
            border: 1.5px solid #ccc;
            outline: none;
            font-family: 'Merriweather', serif;
        }

        input[type="text"]:focus {
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
            <h2>Tài khoản của tôi</h2>
            <ul>
                <li><strong>Thông tin cá nhân</strong></li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=RH2knxpdDpjm&format=png&color=000000" alt="icon">
                    <a href="#profile">Hồ sơ</a>
                </li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=8OdwzXFjBVH2&format=png&color=000000" alt="icon">
                    <a href="#change-password">Đổi mật khẩu</a>
                </li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=N5JNmydXBuCl&format=png&color=000000" alt="icon">
                    <a href="#settings">Cài đặt</a>
                </li>
                <li><strong>Lịch sử</strong></li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=ObuWtTlsoTj6&format=png&color=000000" alt="icon">
                    <a href="#search-history">Lịch sử tìm kiếm</a>
                </li>
                <li class="nav-item">
                    <img src="https://img.icons8.com/?size=100&id=PyrI7GCv8LCv&format=png&color=000000" alt="icon">
                    <a href="#advice-history">Lịch sử tư vấn</a>
                </li>
            </ul>
        </aside>
        <main class="content">
            <section id="profile">
                <h2>Hồ sơ cá nhân</h2>

                <div class="avatar-section">
                    <img id="avatar-preview" src="default-avatar.png" alt="Ảnh đại diện">
                    <a href="#" id="change-avatar-link">Thay đổi ảnh đại diện</a>
                    <input type="file" id="avatar-input" accept="image/*" hidden>
                </div>

                <form id="profile-form">
                    <div class="form-group">
                        <label for="fullname">Họ tên:</label>
                        <input type="text" id="fullname" value="Nguyễn Văn A">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" value="example@email.com">
                    </div>

                    <div class="form-group">
                        <label for="dob">Ngày sinh:</label>
                        <input type="date" id="dob" value="1990-01-01">
                    </div>

                    <div class="form-group">
                        <label>Giới tính:</label>
                        <div class="gender-options">
                            <label><input type="radio" name="gender" value="male" checked> Nam</label>
                            <label><input type="radio" name="gender" value="female"> Nữ</label>
                        </div>
                    </div>

                    <button type="submit">Cập nhật</button>
                </form>
            </section>
            <section id="change-password">
                <h2>Đổi mật khẩu</h2>
                <form class="password-form">
                    <div class="form-group">
                        <input type="password" id="current-password" required>
                        <label for="current-password">Mật khẩu hiện tại</label>
                    </div>
                    <div class="form-group">
                        <input type="password" id="new-password" required>
                        <label for="new-password">Mật khẩu mới</label>
                    </div>
                    <div class="form-group">
                        <input type="password" id="confirm-password" required>
                        <label for="confirm-password">Xác nhận mật khẩu</label>
                    </div>
                    <button type="submit">Cập nhật mật khẩu</button>
                </form>
            </section>
            <section id="settings">
                <h2>Cài đặt</h2>
                <label>
                    Nhận thông báo email
                    <input type="checkbox">
                    <span></span>
                </label>
                <label>
                    Chế độ tối
                    <input type="checkbox">
                    <span></span>
                </label>
            </section>
            <section id="search-history">
                <h2>Lịch sử tìm kiếm</h2>
                <div class="input-with-icon">
                    <input type="text" id="search-filter" placeholder="Tìm kiếm trong lịch sử tìm kiếm...">
                    <svg class="icon-search" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" width="20" height="20">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1110.5 3a7.5 7.5 0 016.15 13.65z" />
                    </svg>
                </div>
                <ul>
                    <li data-time="10:15">
                        Abitol_Tablet
                        <a href="#" class="detail-link">Tìm kiếm ngay</a>
                    </li>
                    <li data-time="09:20">
                        A_Ret_HC_Cream
                        <a href="#" class="detail-link">Tìm kiếm ngay</a>
                    </li>
                </ul>
            </section>
            <section id="advice-history">
                <h2>Lịch sử tư vấn</h2>
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
                                Tư vấn lúc <?= date('H:i \n\g\à\y d/m/Y', strtotime($record['created_at'])) ?>
                                <a href="index.php?route=history-detail&id=<?= $record['id'] ?>" class="detail-link">Xem chi tiết</a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>Không có lịch sử nào.</p>
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
    </script>
</body>

</html>