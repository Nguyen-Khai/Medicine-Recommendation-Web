<?php
if (!isset($_SESSION['user'])) {
    header("Location: index.php?route=login");
    exit();
}

require_once '../app/models/DiseaseModel.php';
$userModel = new DiseaseModel();
$userInfo = $userModel->getUserById($_SESSION['user']['id']); // lấy avatar từ DB
$avatar = $userInfo['avatar'] ?? null;
$avatarSrc = $avatar ? 'data:image/png;base64,' . base64_encode($avatar) : 'assets/images/default-avatar.png';
?>

<style>
    body {
        margin: 0;
        font-family: 'Inter';
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
        top: 40px;
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
        position: absolute;
        right: 250px;
    }

    nav.main-nav ul {
        list-style: none;
        display: flex;
        gap: 20px;
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

    nav.main-nav a.active {
        color: #ffffff;
        font-weight: bold;
        transform: translateY(-5px);
        text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.4);
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
        padding: 8px 16px;
        transition: all 0.3s ease;
    }

    button.search:hover {
        background-color: #F95454;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
    }

    #suggestions {
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        position: relative;
        top: 21px;
        background-color: white;
        z-index: 1000;
        width: 100%;
        border-radius: 4px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    #suggestions div {
        padding: 10px;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    #suggestions div:hover {
        background-color: #f0f0f0;
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
        top: 87px;
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
        right: -110px;
        top: -20px;
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
</style>
<div class="slogan-container">
    <div class="slogan">The information provided on this website is for reference only and is not a substitute for diagnosis, medical advice or treatment from competent professionals.</div>
</div>
<header class="main-header">
    <div class="logo">
        <img src="assets/images/logo.png" alt="Logo" class="logo">
        <h1 class="logo">HealMate</h1>
    </div>
    <nav class="main-nav">
        <ul>
            <li><a href="index.php?route=home">Home</a></li>
            <li><a href="index.php?route=introduction">Introduction</a></li>
            <li><a href="index.php?route=medicine_cabinet">Medicine cabinet</a></li>
            <li><a href="index.php?route=recommendation">Recommendation</a></li>
        </ul>

        <!-- Nút mở tìm kiếm -->
        <button id="openSearchBtn">🔍</button>

        <!-- Sidebar tìm kiếm -->
        <div id="searchSidebar" class="search-sidebar">
            <button id="closeSearchBtn" class="close-btn">&times;</button>
            <input type="text" id="searchInput" placeholder="Enter search keywords..." autocomplete="off" />
            <div id="suggestions" class="autocomplete-box"></div>
            <button class="search">Search</button>
        </div>
        <div class="avatar-dropdown">
            <img src="<?= $avatarSrc ?>" alt="Avatar" class="avatar" onclick="toggleDropdown()">
            <div class="dropdown-menu" id="dropdown-menu">
                <a href="index.php?route=profile#profile">My information</a>
                <a href="#" onclick="logout()">Logout</a>
            </div>
            <h2 class="name">Hello <?= htmlspecialchars($_SESSION['user']['name']) ?>!</h2>
        </div>
    </nav>
</header>
<script>
    function toggleDropdown() {
        const menu = document.getElementById("dropdown-menu");
        menu.style.display = menu.style.display === "block" ? "none" : "block";
    }

    function logout() {
        Swal.fire({
            title: 'Do you want to exit?',
            imageUrl: 'assets/images/question_mask.png', // ảnh bạn muốn dùng
            imageWidth: 150,
            imageHeight: 150,
            showCancelButton: true,
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "index.php?route=logout";
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

    document.addEventListener("DOMContentLoaded", function() {
        const input = document.getElementById('searchInput');
        const box = document.getElementById('suggestions');

        input.addEventListener('input', function() {
            const keyword = input.value.trim();

            if (keyword.length < 2) {
                box.style.display = 'none';
                return;
            }

            // 🔄 Sửa tại đây
            fetch(`index.php?route=autocomplete&query=${encodeURIComponent(keyword)}`)
                .then(res => res.json())
                .then(data => {
                    box.innerHTML = '';
                    if (data.length === 0) {
                        box.style.display = 'none';
                        return;
                    }

                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item;
                        div.addEventListener('click', () => {
                            input.value = item;
                            box.style.display = 'none';
                        });
                        box.appendChild(div);
                    });

                    box.style.display = 'block';
                });
        });
    });

    document.querySelector('.search').addEventListener('click', function() {
        const keyword = document.getElementById('searchInput').value.trim();
        if (keyword.length > 0) {
            window.location.href = `index.php?route=search&query=${encodeURIComponent(keyword)}`;
        }
    });

    // In Đậm trang đang ở hiện tại
    const links = document.querySelectorAll('.main-nav a');
    const urlParams = new URLSearchParams(window.location.search);
    const currentRoute = urlParams.get('route');

    links.forEach(link => {
        const href = link.getAttribute('href');
        const hrefParams = new URLSearchParams(href.split('?')[1]);
        const routeInLink = hrefParams.get('route');

        if (routeInLink === currentRoute) {
            link.classList.add('active');
        }
    });
</script>