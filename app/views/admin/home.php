<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?? 'Admin Panel' ?> - HEALMATE</title>
    <link rel="icon" href="assets/images/favicon.ico" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #f5f8ff;
            color: #333;
            display: flex;
            min-height: 100vh;
        }

        aside h2 {
            margin-bottom: 20px;
            font-size: 20px;
        }

        aside ul {
            list-style: none;
        }

        aside li {
            margin: 15px 0;
        }

        aside a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
        }

        aside a:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            font-weight: 500;
            color: #007BFF;
            /* hoặc màu bạn chọn */
            transition: all 0.2s ease;
        }

        main {
            margin-left: 240px;
            padding: 30px;
            flex: 1;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 26px;
            font-weight: 600;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(0px, 1fr));
            gap: 20px;
        }

        .card {
            background-color: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .card h3 {
            font-size: 16px;
            margin-bottom: 10px;
            color: #1e3a8a;
        }

        .card .value {
            font-size: 24px;
            font-weight: bold;
        }

        canvas {
            margin-top: 20px;
        }

        section {
            display: none;
        }

        section.active {
            display: block;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 240px;
            height: 100vh;
            background-color: #1e3a8a;
            color: white;
            padding: 20px;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin-bottom: 10px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo img {
            height: 40px;
            width: auto;
        }

        .logo h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        aside li a i {
            margin-right: 8px;
            width: 18px;
            text-align: center;
        }

        /* permissio */
        .permissions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .permissions-table th,
        .permissions-table td {
            padding: 8px 12px;
            border: 1px solid #ccc;
            text-align: center;
        }

        .permissions-table th {
            background-color: #f0f0f0;
        }

        .alert.success {
            background: #dff0d8;
            color: #3c763d;
            padding: 10px;
        }

        .alert.error {
            background: #f2dede;
            color: #a94442;
            padding: 10px;
        }

        /* Add Admin */
        .has-submenu {
            position: relative;
        }

        .has-submenu .submenu {
            display: none;
            list-style: none;
            margin: 0;
            padding: 0;
            background: #fff;
            border: 1px solid #ccc;
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 100;
            min-width: 180px;
        }

        .has-submenu .submenu li a {
            display: block;
            padding: 8px 12px;
            color: #f0f0f0;
            text-decoration: none;
        }

        .has-submenu .submenu li a:hover {
            background-color: #2563eb;
            /* hoặc thử #1d4ed8 nếu muốn dịu hơn */
            color: white;
            padding-left: 8px;
            /* hiệu ứng nổi nhẹ */
            transform: translateX(2px);
            /* nổi nhẹ sang phải */
            border-radius: 4px;
        }

        .has-submenu.open .submenu {
            display: block;
            background: none;
            position: relative;
            border: 1px;
        }

        .admin-add-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            font-family: 'Segoe UI', sans-serif;
        }

        .admin-add-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 24px;
            text-align: center;
            color: #1e3a8a;
        }

        .admin-add-form .form-group {
            margin-bottom: 18px;
        }

        .admin-add-form label {
            display: block;
            font-weight: 500;
            margin-bottom: 6px;
            color: #333;
        }

        .admin-add-form input {
            width: 100%;
            padding: 12px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            transition: border-color 0.3s ease;
        }

        .admin-add-form input:focus {
            border-color: #1e3a8a;
            outline: none;
        }

        .admin-submit-btn {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            background-color: #1e3a8a;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .admin-submit-btn:hover {
            background-color: #0f265c;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
            text-align: center;
            transition: all 0.3s ease;
        }

        .alert.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Thông tin các admin */

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 15px;
        }

        .admin-table th,
        .admin-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .admin-table th {
            background-color: #f1f5f9;
        }

        .role-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            color: white;
            display: inline-block;
        }

        .superadmin {
            background-color: #1e3a8a;
        }

        .manager {
            background-color: #2563eb;
        }

        .staff {
            background-color: #38bdf8;
        }

        .action-buttons button {
            padding: 6px 10px;
            margin: 0 3px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        .btn-edit {
            background-color: #4ade80;
            color: white;
        }

        .btn-delete {
            background-color: #f87171;
            color: white;
        }

        /* User */
        .admin-users-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
            align-items: center;
        }

        .admin-users-controls input,
        .admin-users-controls select,
        .admin-users-controls button {
            padding: 10px 14px;
            font-size: 14px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
        }

        .admin-users-controls input:focus,
        .admin-users-controls select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.15);
        }

        .admin-users-controls button {
            background-color: #007bff;
            color: white;
            border: none;
            font-weight: 500;
            transition: background-color 0.2s ease;
            cursor: pointer;
        }

        .admin-users-controls button:hover {
            background-color: #0056b3;
        }

        .admin-users-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .admin-users-table th,
        .admin-users-table td {
            padding: 14px 16px;
            text-align: left;
            font-size: 14px;
        }

        .admin-users-table thead {
            background-color: #007bff;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-users-table tbody tr {
            border-bottom: 1px solid #eee;
        }

        .admin-users-table tbody tr:hover {
            background-color: #f1f7ff;
        }

        .action-btn {
            display: inline-block;
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            transition: 0.2s ease-in-out;
        }

        .edit-btn {
            background-color: #28a745;
            color: white;
            text-decoration: none;
        }

        .edit-btn:hover {
            background-color: #218838;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
            text-decoration: none;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* Diagnosis */
        .admin-diagnosis-container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 24px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-radius: 12px;
            font-family: "Segoe UI", sans-serif;
        }

        .admin-diagnosis-title {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 24px;
            color: #1e3a8a;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 24px;
        }

        .filter-form input[type="text"],
        .filter-form select,
        .filter-form input[type="date"] {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            flex: 1/ 1 200px;
        }

        .filter-form button {
            background-color: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 20px;
            font-size: 14px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .filter-form button:hover {
            background-color: #1e40af;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 15px;
        }

        .admin-table thead {
            background-color: #f1f5f9;
        }

        .admin-table th,
        .admin-table td {
            padding: 12px 14px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .admin-table tr:hover {
            background-color: #f9fafb;
        }

        .view-link {
            color: #1d4ed8;
            text-decoration: none;
            font-weight: 500;
        }

        .view-link:hover {
            text-decoration: underline;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 6px;
            background-color: #e2e8f0;
            color: #1e293b;
            text-decoration: none;
            transition: background 0.3s;
        }

        .pagination a:hover {
            background-color: #cbd5e1;
        }

        .pagination .active {
            background-color: #1e3a8a;
            color: #fff;
            font-weight: bold;
        }

        .date-range {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .date-range label {
            font-size: 14px;
            font-weight: 500;
            color: #1e293b;
        }

        @media (max-width: 768px) {
            .filter-form {
                flex-direction: column;
            }
        }

        /* Thuốc */
        .admin-drugs-container {
            padding: 20px;
        }

        .admin-title {
            font-size: 24px;
            margin-bottom: 20px;
        }

        .filter-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            align-items: center;
        }

        .filter-form input[type="text"],
        .filter-form select {
            padding: 6px 10px;
            font-size: 14px;
        }

        .filter-form button {
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 4px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
        }

        .admin-table th,
        .admin-table td {
            border: 1px solid #ddd;
            padding: 8px;
            vertical-align: top;
        }

        .admin-table th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .view-link {
            color: #007bff;
            text-decoration: none;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            margin: 0 4px;
            padding: 6px 10px;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            display: inline-block;
            margin: 0 4px;
            padding: 6px 10px;
            border: 1px solid #ccc;
            text-decoration: none;
            border-radius: 4px;
            color: #333;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .pagination .dots {
            display: inline-block;
            margin: 0 6px;
            color: #777;
        }

        .add-drug-btn {
            display: inline-block;
            background-color: #28a745;
            /* Màu xanh lá */
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            margin: 15px 0;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .add-drug-btn:hover {
            background-color: #218838;
        }

        /* Diseases*/
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            padding: 5px 10px;
            margin: 0 3px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        .pagination .dots {
            margin: 0 5px;
            color: #999;
        }

        /* Settings */
        .admin-settings-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            font-family: 'Segoe UI', sans-serif;
        }

        .settings-section {
            margin-bottom: 40px;
        }

        .settings-section h3 {
            font-size: 20px;
            color: #3a3a3a;
            margin-bottom: 10px;
            border-bottom: 2px solid #d9d9d9;
            padding-bottom: 5px;
        }

        .settings-form label {
            display: block;
            margin-top: 15px;
            font-weight: 500;
            color: #555;
        }

        .settings-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .settings-form button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .settings-form button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="logo">
            <img src="assets/images/logo.png" alt="Logo" class="logo">
            <h2>HEALMATE</h2>
        </div>
        <ul>
            <li><a href="index.php?route=admin-dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="index.php?route=manage-permissions"><i class="fas fa-user-shield"></i> Permission</a></li>

            <li class="has-submenu">
                <a href="javascript:void(0)" onclick="toggleSubmenu(this)">
                    <i class="fas fa-users-cog"></i> Admin Management
                </a>
                <ul class="submenu">
                    <li><a href="index.php?route=add-admin"><i class="fas fa-user-plus"></i> Add Admin</a></li>
                    <li><a href="index.php?route=admins"><i class="fas fa-user-edit"></i> Admin List</a></li>
                </ul>
            </li>

            <li><a href="index.php?route=admin-users"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="index.php?route=admin-diagnosis"><i class="fas fa-stethoscope"></i> Diagnosis</a></li>
            <li><a href="index.php?route=admin-drugs"><i class="fas fa-pills"></i> Drugs</a></li>
            <li><a href="index.php?route=admin-diseases"><i class="fas fa-virus"></i> Diseases</a></li>
            <li><a href="index.php?route=feedbacks"><i class="fas fa-comment-dots"></i> Feedbacks</a></li>
            <li><a href="index.php?route=admin-settings"><i class="fas fa-cog"></i> Settings</a></li>

        </ul>
    </aside>

    <main>
        <div class="header" style="display: flex; justify-content: space-between; align-items: center; padding: 16px;">
            <h1><?= $title ?? 'Admin' ?></h1>
            <button onclick="logout()" style="background:#dc2626;color:white;padding:8px 16px;border:none;border-radius:6px;cursor:pointer">
                Logout
            </button>
        </div>

        <!-- Nội dung -->
        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error_message'];
                unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="admin-content">
            <?= isset($content) ? $content : '<p>No content loaded.</p>' ?>
        </div>
    </main>

    <script>
        function loadContent(route) {
            fetch('index.php?route=' + route + '&layout=partial')
                .then(response => response.text())
                .then(data => {
                    document.querySelector('.admin-content').innerHTML = data;
                })
                .catch(error => {
                    console.error('Error loading content:', error);
                    document.querySelector('.admin-content').innerHTML = '<p>Failed to load content.</p>';
                });
        }

        function toggleSubmenu(element) {
            const li = element.parentElement;
            li.classList.toggle('open');
        }

        function logout() {
            Swal.fire({
                title: 'Are you sure you want to log out?',
                text: "Your session will be ended.",
                imageUrl: 'assets/images/question_mask.png', // replace with your image
                imageWidth: 120,
                imageHeight: 120,
                imageAlt: 'Question Image',
                showCancelButton: true,
                confirmButtonText: 'Log Out',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "index.php?route=admin-logout";
                }
            });
        }
    </script>
</body>

</html>