<?php
class UserController
{
    public static function login()
    {
        include '../app/views/auth/login.php';
    }

    public static function register()
    {
        include '../app/views/auth/register.php';
    }

    public static function handleLogin()
    {
        require_once '../config/database.php'; // Đảm bảo có kết nối PDO
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Tìm người dùng theo user_name
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Đăng nhập thành công
                session_start();
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'username' => $user['username']
                ];

                include '../app/views/auth/home.php';
                exit();
            } else {
                $error = "Sai tài khoản hoặc mật khẩu!";
                include '../app/views/auth/login.php';
            }
        }
    }

    // Đăng kí
    public static function handleRegister()
    {
        require_once '../config/database.php';
        global $pdo;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $name = $_POST['nameuser'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirm = $_POST['confirm-password'] ?? '';
            $dob = $_POST['dob'] ?? '';
            $gender = $_POST['gender'] ?? '';

            // Kiểm tra mật khẩu khớp
            if ($password !== $confirm) {
                $error = "Mật khẩu không khớp!";
                include '../app/views/auth/login.php';
                return;
            }

            // Mã hóa mật khẩu
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Thêm vào CSDL
            $stmt = $pdo->prepare("INSERT INTO users (username, name, email, password, birthday, gender) VALUES (?, ?, ?, ?, ?, ?)");

            try {
                $stmt->execute([$username, $name, $email, $hashedPassword, $birthday, $gender]);
                $success = "Đăng ký thành công! Bạn có thể đăng nhập.";
                include '../app/views/auth/login.php';
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                    $error = "Username hoặc Email đã tồn tại.";
                } else {
                    $error = "Lỗi hệ thống: " . $e->getMessage();
                }
                $stayOnRegister = true;
                include '../app/views/auth/login.php';
            }
        }
    }

    //Đổi mật khẩu
    public function changePassword()
    {
        if (!isset($_SESSION['user'])) {
            echo "Bạn chưa đăng nhập.";
            return;
        }

        $current = $_POST['current-password'] ?? '';
        $new = $_POST['new-password'] ?? '';
        $confirm = $_POST['confirm-password'] ?? '';

        if ($new !== $confirm) {
            $_SESSION['error'] = "Xác nhận mật khẩu không khớp.";
            header("Location: index.php?route=profile#change-password");
            exit();
        }

        require_once '../app/models/DiseaseModel.php';
        $model = new DiseaseModel();
        $user = $model->getUserById($_SESSION['user']['id']);

        if (!$user || !password_verify($current, $user['password'])) {
           $_SESSION['error'] = "Mật khẩu hiện tại không đúng.";
            header("Location: index.php?route=profile#change-password");
            exit();
        }

       $hashedPassword = password_hash($new, PASSWORD_DEFAULT);
        $updated = $model->updatePassword($_SESSION['user']['id'], $hashedPassword);

        if ($updated) {
            $_SESSION['success'] = "Thay đổi mật khẩu thành công.";
        } else {
            $_SESSION['error'] = "Đã có lỗi xảy ra khi cập nhật.";
        }

        header("Location: index.php?route=profile#change-password");
        exit();
    }
}
