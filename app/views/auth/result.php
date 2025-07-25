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
    <title>Recommendation Result</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullPage.js/4.0.20/fullpage.min.css">
</head>
<style>
    /*Main*/
    body {
        background: linear-gradient(270deg, #F95454, #F5C45E, #FE7743, #328E6E, #4DA8DA, #AA60C8);
        background-size: 1000% 1000%;
        animation: waveGradient 20s ease infinite;
        font-family: 'Inter';
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
        text-decoration: none;
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

    /* In kết quả */
    @media print {
        body {
            animation: none !important;
            background: #fff !important;
        }

        .result-container,
        .result-container * {
            visibility: visible;
        }

        .result-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }

        button {
            display: none !important;
        }

        .header,
        .footer,
        #dropdown-menu {
            display: none !important;
        }
    }

    @media print {
        .result-section {
            page-break-inside: avoid;
        }
    }
</style>

<body>
    <?php
    include('header.php')
    ?>
    <!-- Nội dung chính -->
    <div id="print-area">
        <div class="result">
            <div class="result-container">
                <div class="result-section">
                    <img class="result" src="https://img.icons8.com/?size=100&id=uACqhDVXGkr6&format=png&color=000000" alt="">
                    <h2> Symptoms you entered:</h2>
                    <?php if (!empty($enteredSymptoms)): ?>
                        <p><?= implode(', ', array_map('htmlspecialchars', $enteredSymptoms)) ?></p>
                    <?php endif; ?>
                </div>

                <h1> This is the recommendation result from HEALMATE</h1>

                <div class="result-section">
                    <img class="result" src="https://img.icons8.com/?size=100&id=eStKiCvjHOv8&format=png&color=000000" alt="">
                    <h2> Disease name:</h2>
                    <p><?= htmlspecialchars($diseaseName) ?></p>
                </div>

                <div class="result-section">
                    <img class="result" src="https://img.icons8.com/?size=100&id=g84XLRTaYw1k&format=png&color=000000" alt="">
                    <h2> Disease Information:</h2>
                    <p><?= $diseaseInfo['description'] ?></p>
                </div>

                <div class="result-section">
                    <img class="result" src="https://img.icons8.com/?size=100&id=GFZarxtFIkhx&format=png&color=000000" alt="">
                    <h2> Severity:</h2>
                    <?php if (!empty($symptomWeights)): ?>
                        <table class="symptomweights">
                            <tr>
                                <th>Symptoms</th>
                                <th>Weight (on a scale of 10)</th>
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
                    <h2>Precautions:</h2>
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
                        <h2>Recommended medications:</h2>
                        <table class="medicine-table">
                            <thead>
                                <tr>
                                    <th>Name / ingredients</th>
                                    <th>Learn more</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($medications as $med): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(trim($med)) ?></td>
                                        <td>
                                            <a href="https://www.google.com/search?q=<?= urlencode(trim($med)) ?>" target="_blank">
                                                Search now
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <?php if (!empty($matchingDrugs)): ?>
                    <div class="result-section">
                        <img class="result" src="https://img.icons8.com/?size=100&id=9shlfoGKqCS7&format=png&color=000000" alt="Các loại thuốc có trong HealMate">
                        <h2>All medicine from HealMate:</h2>
                        <table class="medicine-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Ingredients</th>
                                    <th>Content</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($matchingDrugs as $drug): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($drug['ten_thuoc']) ?></td>
                                        <td><?= htmlspecialchars($drug['ten_hoat_chat']) ?></td>
                                        <td><?= htmlspecialchars($drug['ham_luong']) ?></td>
                                        <td>
                                            <a href="<?= htmlspecialchars($drug['url']) ?>" target="_blank">Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <div class="result-section">
                    <img class="result" src="https://img.icons8.com/?size=100&id=3psXjDzSpADv&format=png&color=000000" alt="Chế độ sinh hoạt">
                    <h2>Lifestyle:</h2>
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
                    <h2> Diet:</h2>
                    <p><?= htmlspecialchars($diet ?? 'No') ?></p>
                </div>
                <div style="margin-top: 20px; text-align: center;">
                    <button onclick="printResult()" style="padding: 12px 25px; font-size: 16px; background-color: #4CAF50; color: white; border: none; border-radius: 8px; cursor: pointer;">
                        🖨️ Print Result
                    </button>
                    <button onclick="downloadPDF()" style="padding: 12px 25px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 8px; cursor: pointer; margin-left: 10px;">
                        📥 Download PDF
                    </button>
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
                title: 'Do you want to logout?',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: 'Yes',
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

        if (openBtn && closeBtn && sidebar) {
            openBtn.addEventListener('click', () => {
                sidebar.classList.add('open');
            });

            closeBtn.addEventListener('click', () => {
                sidebar.classList.remove('open');
            });
        }
    </script>
    <script>
        function printResult() {
            window.print();
        }

        function downloadPDF() {
            const element = document.getElementById('print-area');

            const opt = {
                margin: 0.5,
                filename: 'health-advice-result.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    logging: true,
                    scrollY: 0 // Tránh render sai do scroll
                },
                jsPDF: {
                    unit: 'in',
                    format: 'a4',
                    orientation: 'portrait'
                },
                pagebreak: {
                    mode: ['css', 'legacy']
                }
            };

            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>

</html>