<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.php?route=login");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Giới thiệu</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css">
</head>
<style>
    body {
        margin: 0;
        font-family: 'Merriweather', serif;
        padding-top: 150px;
    }

    .slogan-container {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: #f0f0f0;
        padding: 10px 0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        z-index: 1001;
        white-space: nowrap;
    }

    .slogan {
        display: inline-block;
        padding-left: 100%;
        animation: marquee 20s linear infinite;
        font-size: 18px;
        color: #d62828;
        font-weight: bold;
    }

    @keyframes marquee {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    .main-header {
        position: fixed;
        top: 42px;
        left: 0;
        width: 100%;
        background-color: #1e90ff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 60px;
        z-index: 1000;
        box-shadow: 0 8px 10px rgba(0, 0, 0, 0.1);
    }

    .main-header .logo {
        font-size: 24px;
        font-weight: bold;
        color: #ffffff;
        position: relative;
        float: left;
    }

    .main-header img.logo {
        width: 60px;
        left: -20px;
    }

    h1.logo {
        position: absolute;
        left: -5px;
        top: 2px;
    }

    nav.main-nav {
        display: flex;
        align-items: center;
        gap: 20px;
        position: absolute;
        right: 250px;
    }

    nav.main-nav ul {
        list-style: none;
        display: flex;
        gap: 30px;
        padding: 0;
        margin: 0;
    }

    nav.main-nav a {
        text-decoration: none;
        color: #ffffff;
        font-size: 22px;
        transition: color 0.3s ease;
    }

    nav.main-nav a.active {
        color: #c62e2e;
        font-weight: bold;
    }

    nav.main-nav a:hover {
        transform: scale(1.0) translateY(-5px);
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.4);
        color: #ffffff;
        font-weight: 900;
    }

    /*Tìm kiếm*/
    .search-sidebar {
        height: 100vh;
        width: 300px;
        position: fixed;
        top: 0;
        right: 0;
        background-color: #fefefe;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.3);
        padding: 60px 20px 20px 20px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        transform: translateX(100%);
        transition: transform 0.4s ease;
        z-index: 1000;
    }

    .search-sidebar.open {
        transform: translateX(0);
    }

    .close-btn {
        position: absolute;
        top: 15px;
        right: 15px;
        /* đổi từ left sang right */
        font-size: 30px;
        background: none;
        border: none;
        cursor: pointer;
    }

    .search-sidebar input {
        padding: 10px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 4px;
        position: relative;
        top: 30px;
    }

    .search-sidebar button {
        padding: 10px;
        font-size: 16px;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        top: 29px;
    }

    button#openSearchBtn {
        border: none;
        border-radius: 30px;
        position: relative;
        left: 30px;
        background: white;
        font-size: 16px;
        cursor: pointer;
        font-family: 'Merriweather', serif;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    button#closeSearchBtn {
        padding: 10px;
        font-size: 40px;
        color: red;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        height: 50px;
        top: 31px;
        right: 1px;
    }

    button#closeSearchBtn:hover {
        background: none;
        color: #F95454;
    }

    button.search {
        background-color: #c62e2e;
        border: none;
        border-radius: 30px;
        position: relative;
        bottom: 1px;
        color: white;
        font-size: 16px;
        cursor: pointer;
        font-family: 'Merriweather', serif;
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    button.search:hover {
        background-color: #F95454;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
    }

    /*Avatar*/
    .avatar-dropdown {
        position: relative;
        display: inline-block;
    }

    .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        position: relative;
        left: 45px;
        top: 28px;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        right: 0;
        background-color: #F95454;
        min-width: 140px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        z-index: 1;
        border-radius: 6px;
        padding: 8px 0;
        top: 77px;
    }

    .dropdown-menu a {
        display: block;
        padding: 10px 16px;
        text-decoration: none;
        font-size: 20px;
        width: 195px;
    }

    .dropdown-menu a:hover {
        background-color: #F95454;
    }

    h2.name {
        font-size: 18px;
        position: relative;
        right: -124px;
        top: -10px;
        background: linear-gradient(270deg, #ffffff, #EFF3EA, #FFF3AF, #FFACAC, #F95454, #C62E2E);
        background-size: 600% 600%;
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: gradientAnimation 4s ease infinite;
    }

    @keyframes gradientAnimation {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }
    /* Header */
    /*Main*/
    .section {
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .fp-overflow {
        width: 100%;
        height: 100%;
    }

    .fp-watermark {
        display: none !important;
    }

    .section_1 {
        width: 100%;
    }

    main {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 20px;
    }

    img.logo_st1 {
        width: 100px;
        position: relative;
        top: 150px;
    }

    h1.logo_st1 {
        margin-bottom: 10px;
        color: #F95454;
        position: relative;
        top: 128px;
        left: 553px;
        width: 178px;
    }

    h1 {
        margin-bottom: 10px;
        color: #2e86de;
    }

    p {
        margin-bottom: 35px;
        position: relative;
        font-size: 17px;
        width: 1000px;
        top: 136px;
        left: 140px;
    }

    .divider {
        border: none;
        height: 2px;
        background-color: #ccc;
        /* hoặc #e0e0e0 cho nhẹ hơn */
        margin: 20px 0;
        position: relative;
        top: 125px;
    }

    /*Background*/
    .circle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at top left, #F95454, #C62E2E);
        animation: float 6s ease-in-out infinite, glow 4s ease-in-out infinite alternate;
        opacity: 0.9;
        filter: brightness(1);
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-20px);
        }
    }

    @keyframes glow {
        0% {
            filter: brightness(0.7);
        }

        100% {
            filter: brightness(1.3);
        }
    }

    /* Mỗi vòng tròn có kích thước và vị trí khác nhau */
    .circle1 {
        width: 150px;
        height: 150px;
        top: 90%;
        left: -7px;
        animation-delay: 2s;
    }

    .circle2 {
        width: 100px;
        height: 100px;
        top: 70%;
        left: 90%;
        animation-delay: 4s;
    }

    .circle3 {
        width: 200px;
        height: 200px;
        top: 5%;
        left: 60%;
    }

    .circle4 {
        width: 200px;
        height: 200px;
        top: 80%;
        left: 90%;
    }

    /*Section2*/
    h3 {
        position: relative;
        top: 138px;
        font-size: 27px;
        background: linear-gradient(90deg, #0D92F4, #F95454);
        /* màu gradient */
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .multi-column-list {
        list-style-type: none;
        column-count: 4;
        column-gap: 32px;
        padding-left: 23px;
        position: relative;
        top: 405px;
    }

    /*Background*/
    .circle_st2 {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at top left, #0D92F4, #F95454);
        animation: glow 4s ease-in-out infinite alternate;
        opacity: 0.9;
        filter: brightness(1);
    }

    @keyframes glow {
        0% {
            filter: brightness(0.8);
        }

        100% {
            filter: brightness(1.4);
        }
    }

    .circle-container {
        position: relative;
        width: 150px;
        height: 150px;
        float: left;
    }

    .circle_st2.circle1 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 67px;
    }

    .cx {
        position: absolute;
        top: 133%;
        left: 118%;
        width: 191px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .circle_st2.circle2 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 155%;
    }

    .tt {
        position: absolute;
        top: 134%;
        left: 229%;
        ;
        width: 224px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .circle_st2.circle3 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 266%;
    }

    .cnh {
        position: absolute;
        top: 133%;
        left: 340%;
        width: 224px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }

    .circle_st2.circle4 {
        width: 220px;
        height: 220px;
        top: 60%;
        left: 380%;
    }

    .\32 47 {
        position: absolute;
        top: 133%;
        left: 454%;
        width: 224px;
        transform: translate(-50%, -50%);
        z-index: 2;
    }
</style>

<body>
    <div class="slogan-container">
        <div class="slogan">Thông tin được cung cấp trên trang web này chỉ mang tính chất tham khảo và không thay thế cho chẩn đoán, tư vấn hoặc điều trị y tế từ các chuyên gia có thẩm quyền.</div>
    </div>
    <header class="main-header">
        <div class="logo">
            <img src="assets/images/logo.png" alt="Logo" class="logo">
            <h1 class="logo">HealMate</h1>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php?route=home">Trang chủ</a></li>
                <li><a href="index.php?route=introduction">Giới thiệu</a></li>
                <li><a href="index.php?route=medicine_cabinet">Tủ thuốc</a></li>
                <li><a href="index.php?route=recommendation">Tư vấn</a></li>
            </ul>

            <!-- Nút mở tìm kiếm -->
            <button id="openSearchBtn">🔍</button>

            <!-- Sidebar tìm kiếm -->
            <div id="searchSidebar" class="search-sidebar">
                <button id="closeSearchBtn" class="close-btn">&times;</button>
                <input type="text" placeholder="Nhập từ khóa tìm kiếm..." />
                <button class="search">Tìm kiếm</button>
            </div>
            <div class="avatar-dropdown">
                <img src="assets/images/avatar.png" alt="Avatar" class="avatar" onclick="toggleDropdown()">
                <div class="dropdown-menu" id="dropdown-menu">
                    <a href="index.php?route=profile">Thông tin cá nhân</a>
                    <a href="#" onclick="logout()">Đăng xuất</a>
                </div>
                <h2 class="name">Chào <?= htmlspecialchars($_SESSION['user']['name']) ?>!</h2>
            </div>
        </nav>
    </header>
    <!-- Nội dung chính -->
    <div id="fullpage">
        <div class="section  section1">
            <div class="logo_st1">
                <img class="logo_st1" src="assets/images/logo.png" alt="">
                <h1 class="logo_st1">HealMate</h1>
            </div>
            <p>
                HealMate là nền tảng trực tuyến giúp bạn tiếp cận nhanh chóng các thông tin, lời khuyên y tế đáng tin cậy.
                Chúng tôi cung cấp những giải pháp và hướng dẫn chăm sóc sức khỏe phù hợp với nhu cầu cá nhân của bạn.
            </p>
            <hr class="divider" />
            <p>
                Với sự hỗ trợ từ công nghệ hiện đại, website của chúng tôi cam kết mang đến trải nghiệm tìm kiếm thông tin y tế dễ dàng, an toàn và hiệu quả.
            </p>
            <hr class="divider" />
            <p>
                Dù bạn đang tìm hiểu về các triệu chứng bệnh, chế độ dinh dưỡng, hay cách phòng ngừa bệnh tật, Gợi ý Y tế luôn sẵn sàng đồng hành cùng bạn trên con đường chăm sóc sức khỏe.
            </p>
            <div class="circle circle1"></div>
            <div class="circle circle2"></div>
            <div class="circle circle3"></div>
            <div class="circle circle4"></div>
        </div>
        <div class="section">
            <h3>Điểm nổi bật của chúng tôi</h3>
            <ul class="multi-column-list">
                <li>Thông tin cập nhật, chính xác từ các nguồn uy tín.</li>
                <li>Giao diện thân thiện, dễ sử dụng trên mọi thiết bị.</li>
                <li>Tư vấn cá nhân hóa dựa trên dữ liệu và chuyên môn y tế.</li>
                <li>Hỗ trợ 24/7.</li>
            </ul>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle1"></div>
                <img class="cx" src="assets/images/chinh_xac.png" alt="" />
            </div>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle2"></div>
                <img class="tt" src="assets/images/than_thien.png" alt="" />
            </div>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle3"></div>
                <img class="cnh" src="assets/images/ca_nhan_hoa.png" alt="" />
            </div>
            <div class="circle-container" style="position: relative;">
                <div class="circle_st2 circle4"></div>
                <img class="247" src="assets/images/247.png" alt="" />
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.js"></script>

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
                cancelButtonColor: '#3085d6',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "login.html";
                }
            });
        }

        // Đóng menu khi click bên ngoài
        window.addEventListener("click", function(e) {
            const avatar = document.querySelector(".avatar");
            const menu = document.getElementById("dropdown-menu");
            if (!avatar.contains(e.target) && !menu.contains(e.target)) {
                menu.style.display = "none";
            }
        });

        // Main
        new fullpage('#fullpage', {
            autoScrolling: true,
            navigation: true,
            scrollBar: false,
        });

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