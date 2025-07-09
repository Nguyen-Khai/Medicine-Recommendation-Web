<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tư vấn</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css">
</head>
<style>
    /*Main*/
    h1.recommendation {
        font-family: 'Merriweather', serif;
        font-size: 50px;
        position: relative;
        top: -24px;
        left: 310px;
        width: 672px;
        color: #F95454;
    }

    p.recommendation {
        position: absolute;
        top: 40px;
        left: 430px;
    }

    .container-forms {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        position: relative;
        width: 100%;
        left: 430px;
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

    form {
        position: absolute;
        top: 10px;
    }

    .recommendation {
        position: relative;
        width: 421px;
    }

    .recommendation input {
        height: 60px;
        width: 400px;
        outline: none;
        border: 1px solid black;
        padding: 10px;
        border-radius: 5px;
        font-size: 18px;
        padding-top: 20px;
        font-family: 'Merriweather', serif;
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

    .recommendation input:focus {
        border: 2px solid #1e90ff;
    }

    .recommendation input:focus+label,
    .recommendation input:valid+label {
        top: 13px;
        font-size: 15px;
        font-weight: 500;
        color: #1e90ff;
    }

    button.recommendation {
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
        margin-top: 10px;
    }

    button.recommendation:hover {
        background-color: #F95454;
        text-decoration: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        transform: scale(1.02);
    }

    img.recommendation {
        width: 450px;
        height: 450px;
        left: -552px;
        bottom: -95px;
        position: absolute;
        object-fit: cover;
    }

    /*Background*/
    .light-effect {
        position: relative;
        width: 100px;
        height: 100px;
    }

    .glow-circle {
        position: absolute;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, #FFE8B6, #F95454, transparent 80%);
        border-radius: 50%;
        filter: blur(80px);
        animation: pulse 5s infinite ease-in-out;
        z-index: -1;
        bottom: -55px;
        right: 300px;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
            opacity: 0.8;
        }

        50% {
            transform: scale(1.3);
            opacity: 0.4;
        }
    }
</style>

<body>
    <?php
        include('header.php')
    ?>
    <!-- Nội dung chính -->
    <div class="recommendation">
        <h1 class="recommendation">Bạn đang cảm thấy thế nào?</h1>
        <p class="recommendation">Hãy cho chúng tôi biết sức khỏe bạn đang thế nào nhé!</p>
        <div class="container-forms">
            <div id="login-form" class="form-section active-form">
                <form action="index.php?route=diagnose" method="POST">
                    <div class="recommendation">
                        <input type="text" name="symptoms" id="symptom" required />
                        <label>Triệu chứng</label>
                    </div>
                    <button class="recommendation">Gửi</button>
                </form>
                <div class="light-effect">
                    <div class="glow-circle"></div>
                    <img class="recommendation" src="assets/images/recommendation.png" alt="">
                </div>
            </div>
        </div>
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