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
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tủ thuốc</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css">
</head>
<style>
    /*section 1*/
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

    h1.section_1 {
        font-family: 'Merriweather', serif;
        font-size: 50px;
        position: relative;
        top: -24px;
        left: 375px;
    }

    img.section_1 {
        width: 500px;
        float: left;
        position: relative;
        left: 10px;
        top: 200px;
    }

    button.section_1 {
        background-color: #c62e2e;
        border: none;
        border-radius: 30px;
        position: relative;
        bottom: 1px;
        color: white;
        font-size: 20px;
        cursor: pointer;
        font-family: 'Merriweather', serif;
        padding: 8px 16px;
        transition: all 0.3s ease;
        width: 155px;
        height: 50px;
        top: 200px;
    }

    button.section_1:hover {
        background-color: #F95454;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
    }

    /*Background*/
    .circle {
        position: absolute;
        border-radius: 50%;
        background: radial-gradient(circle at top left, #0D92F4, #77CDFF);
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
        top: 80%;
        left: 50px;
        animation-delay: 2s;
    }

    .circle2 {
        width: 100px;
        height: 100px;
        top: 70%;
        left: 80%;
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
        left: 80%;
    }

    /*section 2*/
    .container.st2 {
        position: relative;
        top: 162px;
    }

    ul.a_st2 {
        list-style-type: none;
        column-count: 2;
        column-gap: 32px;
    }

    a.a_st2 {
        text-decoration: none;
        font-size: 20px;
        color: black;
        column-count: 4;
        column-gap: 32px;
    }

    /*section 3*/
    .ai-assistant-section {
        background: #f0f9ff;
        padding: 60px 20px;
        text-align: center;
    }

    h2.st3 {
        position: relative;
        top: 121px;
    }

    p.st3 {
        position: relative;
        top: 113px;
    }
</style>

<body>
    <?php
        include('header.php')
    ?>
    <!-- Nội dung chính -->
    <div id="fullpage">
        <h1 class="section_1">Tủ thuốc của HealMate</h1>

        <div class="section section2">
            <h2>Kiến thức sức khỏe</h2>
        </div>
        <div class="section secton3">
            <h2 class="st3">Trợ lý sức khoẻ cá nhân (AI)</h2>
        </div>
        <div class="section">Footer</div>
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