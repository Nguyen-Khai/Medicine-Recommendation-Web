<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kết quả tìm kiếm</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Merriweather&display=swap">
    <style>
        body {
            font-family: 'Merriweather', serif;
            margin: 0;
            padding: 20px;
        }

        .section {
            max-width: 1224px;
            margin: auto;
            position: relative;
            z-index: 2;
        }

        .st2-title {
            text-align: center;
            font-size: 28px;
            color: #d9534f;
            margin-bottom: 30px;
        }

        #article-list {
            list-style-type: none;
            padding: 0;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }

        .article-item {
            flex: 0 0 calc(25% - 20px);
        }

        .health-article {
            width: 237px;
            background-color: #fff;
            border-top: 5px solid #d9534f;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            padding: 20px;
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .health-article:hover {
            transform: translateY(-4px);
        }

        .article-title {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        .article-meta {
            font-size: 16px;
            color: #555;
        }

        .info-line {
            margin-bottom: 8px;
        }

        #pagination {
            margin-top: 20px;
            text-align: center;
        }

        #pagination button {
            margin: 0 5px;
            padding: 6px 12px;
            font-size: 16px;
            border: none;
            background-color: #0D92F4;
            color: white;
            border-radius: 4px;
            cursor: pointer;
        }

        #pagination button.active {
            background-color: #F95454;
        }

        #pagination span {
            margin: 0 5px;
            font-weight: bold;
        }

        a {
            color: #0D92F4;
            text-decoration: none;
        }

        a:hover {
            color: #0056b3;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle at top left, #0D92F4, #F95454);
            animation: float 6s ease-in-out infinite, glow 4s ease-in-out infinite alternate;
            opacity: 0.9;
            filter: brightness(1);
            z-index: 0;
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
                filter: brightness(1.0);
            }

            100% {
                filter: brightness(1.5);
            }
        }

        .circle1 {
            width: 150px;
            height: 150px;
            top: 25%;
            left: 50px;
            animation-delay: 2s;
        }

        .circle2 {
            width: 100px;
            height: 100px;
            top: 81%;
            left: 91%;
            animation-delay: 4s;
        }

        .circle3 {
            width: 200px;
            height: 200px;
            top: 5%;
            left: 65%;
        }

        .circle4 {
            width: 200px;
            height: 200px;
            top: 87%;
            left: 80%;
        }
    </style>
</head>

<body>
    <?php
    include('header.php');
    ?>
    <div class="circle circle1"></div>
    <div class="circle circle2"></div>
    <div class="circle circle3"></div>
    <div class="circle circle4"></div>
    <div class="section">
        <h2 class="st2-title">Kết quả tìm kiếm cho: "<?= htmlspecialchars($keyword) ?>"</h2>
        <?php if (!empty($results)): ?>
            <ul id="article-list">
                <?php foreach ($results as $med): ?>
                    <li class="article-item">
                        <div class="health-article">
                            <h3 class="article-title"><?= htmlspecialchars($med['ten_thuoc']) ?></h3>
                            <div class="article-meta">
                                <div class="info-line"><strong>Thành phần:</strong> <?= htmlspecialchars($med['ten_hoat_chat']) ?></div>
                                <div class="info-line"><strong>Hàm lượng:</strong> <?= htmlspecialchars($med['ham_luong']) ?></div>
                            </div>
                            <a href="<?= htmlspecialchars($med['url']) ?>" target="_blank">Tìm hiểu thêm</a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Không tìm thấy thuốc hoặc hoạt chất nào phù hợp.</p>
        <?php endif; ?>
        <div id="pagination"></div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const itemsPerPage = 8;
            const articleList = Array.from(document.querySelectorAll('#article-list .article-item'));
            const paginationContainer = document.getElementById('pagination');
            let currentPage = 1;

            function showPage(page) {
                const start = (page - 1) * itemsPerPage;
                const end = start + itemsPerPage;

                articleList.forEach((item, index) => {
                    item.style.display = (index >= start && index < end) ? 'block' : 'none';
                });

                currentPage = page;
                updatePagination();
            }

            function updatePagination() {
                const totalPages = Math.ceil(articleList.length / itemsPerPage);
                paginationContainer.innerHTML = '';

                if (currentPage > 1) {
                    paginationContainer.appendChild(createNavButton('<<', 1));
                    paginationContainer.appendChild(createNavButton('<', currentPage - 1));
                }

                const start = Math.max(1, currentPage - 1);
                const end = Math.min(totalPages, currentPage + 1);

                for (let i = start; i <= end; i++) {
                    const btn = createNavButton(i, i);
                    if (i === currentPage) btn.classList.add('active');
                    paginationContainer.appendChild(btn);
                }

                if (end < totalPages - 1) {
                    const dots = document.createElement('span');
                    dots.textContent = '...';
                    paginationContainer.appendChild(dots);
                }

                if (end < totalPages) {
                    paginationContainer.appendChild(createNavButton(totalPages, totalPages));
                }

                if (currentPage < totalPages) {
                    paginationContainer.appendChild(createNavButton('>', currentPage + 1));
                    paginationContainer.appendChild(createNavButton('>>', totalPages));
                }
            }

            function createNavButton(label, page) {
                const btn = document.createElement('button');
                btn.textContent = label;
                btn.addEventListener('click', () => showPage(page));
                return btn;
            }

            showPage(1);
        });
    </script>
</body>

</html>