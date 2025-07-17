<?php
require_once '../config/database.php'; // Đảm bảo có kết nối PDO
global $pdo;

class AdminController
{
    public function addAdmin()
    {
        require_once '../app/views/admin/add_admin.php';
    }

    public function showLogin()
    {
        include '../app/views/admin/login.php';
    }

    public function handleLogin()
    {
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Truy vấn admin từ CSDL
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($admin && password_verify($password, $admin['password'])) {
                // Đăng nhập thành công
                $_SESSION['admin'] = [
                    'id' => $admin['id'],
                    'username' => $admin['username'],
                    'role' => $admin['role'],
                    'email' => $admin['email'],
                    'avatar' => $admin['avatar'] ?? null
                ];

                header("Location: index.php?route=admin-dashboard");
                exit();
            } else {
                $_SESSION['admin_error'] = "Sai tài khoản hoặc mật khẩu.";
                header("Location: index.php?route=admin-login");
                exit();
            }
        }
    }

    public function logout()
    {
        unset($_SESSION['admin']);
        header("Location: index.php?route=admin-login");
        exit();
    }

    public function dashboard($isPartial = false)
    {
        $title = "Dashboard";
        ob_start();
        require '../app/views/admin/dashboard.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }

    //Hiển thị ds người dùng
    public function users($isPartial = false)
    {
        require_once '../app/models/AdminModel.php';
        $adminModel = new AdminModel();
        $users = $adminModel->getAllUsers();

        $title = "Users";

        ob_start();
        require '../app/views/admin/users.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }

    //Diagnosis
    public function diagnosis($isPartial = false)
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        $userId = $_GET['user_id'] ?? null;
        $keyword = $_GET['keyword'] ?? null;
        $fromDate = $_GET['from_date'] ?? null;
        $toDate = $_GET['to_date'] ?? null;

        $currentPage = max(1, intval($_GET['page'] ?? 1));
        $perPage = 10;
        $offset = ($currentPage - 1) * $perPage;

        // Dữ liệu
        $diagnoses = $model->getFilteredDiagnosisHistory($userId, $keyword, $fromDate, $toDate, $perPage, $offset);
        $totalRows = $model->countFilteredDiagnosisHistory($userId, $keyword, $fromDate, $toDate);
        $totalPages = ceil($totalRows / $perPage);

        $allUsers = $model->getAllUsersSimple();

        $title = "Diagnosis History";
        ob_start();
        require '../app/views/admin/diagnosis.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }
}
