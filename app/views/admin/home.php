<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= $title ?? 'Admin Panel' ?> - HEALMATE</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

        aside {
            width: 240px;
            background-color: #1e3a8a;
            color: white;
            padding: 20px;
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
            text-decoration: underline;
        }

        main {
            flex: 1;
            padding: 30px;
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
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

        /* Add Admin */
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

        /* User */
        .admin-users-container {
            padding: 30px;
            background: #f9f9f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .admin-heading {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 20px;
            color: #333;
        }

        .admin-users-controls {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .admin-search-input {
            padding: 10px;
            font-size: 16px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .admin-add-btn {
            padding: 10px 16px;
            font-size: 16px;
            background-color: #1976d2;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .admin-add-btn:hover {
            background-color: #1565c0;
        }

        .admin-users-table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.08);
        }

        .admin-users-table thead {
            background-color: #1976d2;
            color: white;
        }

        .admin-users-table th,
        .admin-users-table td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .admin-users-table tbody tr:hover {
            background-color: #f0f8ff;
        }

        .action-btn {
            padding: 6px 12px;
            font-size: 14px;
            margin-right: 6px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #4caf50;
            color: white;
        }

        .delete-btn {
            background-color: #f44336;
            color: white;
        }

        @media (max-width: 768px) {
            .admin-users-controls {
                flex-direction: column;
                gap: 10px;
            }

            .admin-search-input {
                width: 100%;
            }

            .admin-add-btn {
                width: 100%;
            }
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
    </style>
</head>

<body>
    <aside>
        <h2>HEALMATE Admin</h2>
        <ul>
            <li><a href="#" onclick="loadContent('admin-dashboard')">Dashboard</a></li>
            <li><a href="#" onclick="loadContent('add-admin')">Add Admin</a></li>
            <li><a href="#" onclick="loadContent('admin-users')">Users</a></li>
            <li><a href="index.php?route=admin-diagnosis">Diagnosis</a></li>
            <li><a href="index.php?route=admin-drugs">Drugs</a></li>
            <li><a href="index.php?route=admin-diseases">Diseases</a></li>
            <li><a href="index.php?route=admin-guides">Guides</a></li>
            <li><a href="index.php?route=admin-settings">Settings</a></li>
        </ul>
    </aside>

    <main>
        <div class="header">
            <h1><?= $title ?? 'Admin' ?></h1>
            <button onclick="logout()" style="background:#dc2626;color:white;padding:8px 16px;border:none;border-radius:6px;cursor:pointer">Logout</button>
        </div>

        <!-- Nội dung động được đưa vào -->
        <div class="admin-content">
            <?= isset($content) ? $content : '<p>No content loaded.</p>' ?>
        </div>
    </main>

    <script>
        function logout() {
            if (confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        }

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
    </script>

</body>

</html>