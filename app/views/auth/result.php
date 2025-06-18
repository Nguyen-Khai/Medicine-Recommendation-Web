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
    <title>Kết quả tư vấn</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css">
</head>
<style>
    /*Main*/
    body {
        background: linear-gradient(270deg, #F95454, #F5C45E, #FE7743, #328E6E, #4DA8DA, #AA60C8);
        background-size: 1000% 1000%;
        animation: waveGradient 20s ease infinite;
    }

    @keyframes waveGradient {
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

    .result-container {
        max-width: 900px;
        margin-left: 150px;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
    }

    .result-container h1 {
        text-align: center;
        font-size: 26px;
        ;
        margin-bottom: 22px;
        position: relative;
        top: -6px;
        background: linear-gradient(270deg, #F95454, #F5C45E, #FE7743, #328E6E, #4DA8DA, #AA60C8);
        background-clip: text;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    img.result {
        width: 30px;
        float: left;
        margin-right: 10px;
        margin-top: -2px;
    }

    .result-section {
        margin-bottom: 25px;
    }

    .result-section h2 {
        font-size: 20px;
        margin-bottom: 8px;
        color: #4a5568;
    }

    .result-section p {
        font-size: 16px;
        color: #2d3748;
        line-height: 1.6;
        background: #f7fafc;
        padding: 12px 16px;
        border-radius: 10px;
    }

    .result-section ul {
        padding-left: 20px;
        margin-top: 10px;
        list-style-type: none;
        background-color: #f7fafc;
    }

    .result-section li {
        margin-bottom: 6px;
        line-height: 1.6;
    }

    table.symptomweights {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .symptomweights th,
    .symptomweights td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }

    .symptomweights th {
        background-color: #f2f2f2;
        color: #333;
    }


    .medicine-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .medicine-table th,
    .medicine-table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }

    .medicine-table th {
        background-color: #f2f2f2;
        color: #333;
    }

    .medicine-table a {
        color: #0D92F4;
        text-decoration: underline;
    }

    .medicine-table a:hover {
        color: #0056b3;
    }

    @media screen and (max-width: 768px) {
        .result-container {
            margin: 20px;
            padding: 20px;
        }

        .result-container h1 {
            font-size: 22px;
        }

        .result-section h2 {
            font-size: 18px;
        }
    }
</style>

<body>
    <?php
        include('header.php')
    ?>
    <!-- Nội dung chính -->
    <div class="result">
        <div class="result-container">
            <div class="result-section">
                <img class="result" src="https://img.icons8.com/?size=100&id=uACqhDVXGkr6&format=png&color=000000" alt="">
                <h2> Những triệu chứng bạn đã nhập:</h2>
                <?php if (!empty($enteredSymptoms)): ?>
                    <p><?= implode(', ', array_map('htmlspecialchars', $enteredSymptoms)) ?></p>
                <?php endif; ?>
            </div>

            <h1> Đây là lời tư vấn của chúng tôi</h1>

            <div class="result-section">
                <img class="result" src="https://img.icons8.com/?size=100&id=eStKiCvjHOv8&format=png&color=000000" alt="">
                <h2> Tên bệnh:</h2>
                <p><?= htmlspecialchars($diseaseName) ?></p>
            </div>

            <div class="result-section">
                <img class="result" src="https://img.icons8.com/?size=100&id=g84XLRTaYw1k&format=png&color=000000" alt="">
                <h2> Mô tả bệnh:</h2>
                <p><?= $diseaseInfo['description'] ?></p>
            </div>

            <div class="result-section">
                <img class="result" src="https://img.icons8.com/?size=100&id=GFZarxtFIkhx&format=png&color=000000" alt="">
                <h2> Mức độ nghiêm trọng:</h2>
                <?php if (!empty($symptomWeights)): ?>
                    <table class="symptomweights">
                        <tr>
                            <th>Triệu chứng</th>
                            <th>Trọng số (Trên thang 10)</th>
                        </tr>
                        <?php foreach ($symptomWeights as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['symptom']) ?></td>
                                <td><?= htmlspecialchars($row['weight']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>

            <div class="result-section">
                <img class="result" src="https://img.icons8.com/?size=100&id=TdgoFB7uKPlI&format=png&color=000000" alt="Phòng ngừa">
                <h2>Các biện pháp phòng ngừa:</h2>
                <?php if (!empty($diseaseInfo['precautions'])): ?>
                    <ul>
                        <?php foreach ($diseaseInfo['precautions'] as $p): ?>
                            <li><?= htmlspecialchars($p) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php if (!empty($diseaseInfo['medication'])): ?>
                <?php
                //Làm sạch
                $raw = $diseaseInfo['medication'];
                // Xoá các ký tự không mong muốn
                $clean = str_replace(["[", "]", "'", '"', "_"], '', $raw);
                // Tách thành mảng
                $medications = explode(',', $clean);
                ?>

                <div class="result-section">
                    <img class="result" src="https://img.icons8.com/?size=100&id=byMfPQ4nQ4lQ&format=png&color=000000" alt="Thuốc đề xuất">
                    <h2>Các loại thuốc được gợi ý:</h2>
                    <table class="medicine-table">
                        <thead>
                            <tr>
                                <th>Tên/ Thành phần thuốc</th>
                                <th>Tìm kiếm sản phẩm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($medications as $med): ?>
                                <tr>
                                    <td><?= htmlspecialchars(trim($med)) ?></td>
                                    <td>
                                        <a href="https://www.google.com/search?q=<?= urlencode(trim($med)) ?>" target="_blank">
                                            Tìm kiếm ngay
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
            <div class="result-section">
                <img class="result" src="https://img.icons8.com/?size=100&id=3psXjDzSpADv&format=png&color=000000" alt="Chế độ sinh hoạt">
                <h2>Chế độ sinh hoạt:</h2>
                <?php if (!empty($diseaseInfo['workouts'])): ?>
                    <ul>
                        <?php foreach ($diseaseInfo['workouts'] as $w): ?>
                            <li><?= htmlspecialchars($w) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <?php
            //Làm sạch
            $raw_diet = $diseaseInfo['diet'];
            // Xoá các ký tự không mong muốn
            $diet = str_replace(["[", "]", "'", '"', "_"], '', $raw_diet);
            ?>
            <div class="result-section">
                <img class="result" src="https://img.icons8.com/?size=100&id=35G9RMkkBBXc&format=png&color=000000" alt="">
                <h2> Chế độ ăn uống:</h2>
                <p><?= htmlspecialchars($diet ?? 'Không có') ?></p>
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