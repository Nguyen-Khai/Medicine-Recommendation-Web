<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once '../vendor/autoload.php'; // hoặc đường dẫn đến PHPMailer/autoload.php nếu bạn tải tay

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

    public static function fogrot_password()
    {
        include '../app/views/auth/forgot_password.php';
    }

    public static function verify_reset_code()
    {
        include '../app/views/auth/verify-code.php';
    }

    public static function reset_password()
    {
        include '../app/views/auth/reset-password.php';
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

    //Mã xác nhận
    public function handleForgotPassword()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';

        if (empty($username) || empty($email)) {
            $_SESSION['error'] = "Vui lòng điền đầy đủ thông tin.";
            header("Location: index.php?route=forgot_password");
            exit;
        }

        $userModel = new DiseaseModel();
        $user = $userModel->findByUsernameAndEmail($username, $email);

        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy người dùng với thông tin đã nhập.";
            header("Location: index.php?route=forgot_password");
            exit;
        }

        // Tạo mã xác nhận
        $code = rand(100000, 999999);

        // Lưu mã vào DB hoặc session
        $_SESSION['reset_code'] = $code;
        $_SESSION['reset_user_id'] = $user['id'];
        $_SESSION['reset_email'] = $email;

        $mail = new PHPMailer(true);
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nkhai.contact@gmail.com';
            $mail->Password = 'ophsfnsjvbfrtsrp'; // ← xóa dấu cách!
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('nkhai.contact@gmail.com', 'HealMate');
            $mail->addAddress($email); // Địa chỉ người dùng nhập

            $mail->isHTML(true);
            $mail->Subject = 'Mã xác nhận khôi phục mật khẩu';
            $mail->Body = "Xin chào <b>$username</b>,<br><br>
                   Đây là mã xác nhận để đặt lại mật khẩu:<br>
                   <h2>$code</h2>
                   Nếu bạn không yêu cầu, vui lòng bỏ qua email này.<br><br>
                   Trân trọng,<br>HealMate Team";

            $mail->send();
            $_SESSION['success'] = "Đã gửi mã xác nhận đến email của bạn.";
            header("Location: index.php?route=verify-reset-code");
            exit;
        } catch (Exception $e) {
            $_SESSION['error'] = "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
            header("Location: index.php?route=forgot_password");
            exit;
        }
    }
    public function verifyResetCode()
    {
        $inputCode = $_POST['code'] ?? '';

        if ($inputCode == $_SESSION['reset_code']) {
            header("Location: index.php?route=reset_password");
        } else {
            $_SESSION['error'] = "Mã xác nhận không đúng.";
            header("Location: index.php?route=verify-reset-code");
        }
    }

    //Tạo lại mật khẩu
    public function handleResetPassword()
    {
        $newPassword = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        $userId = $_SESSION['reset_user_id'] ?? null;

        if ($newPassword !== $confirm || !$userId) {
            $_SESSION['error'] = "Mật khẩu không khớp.";
            header("Location: index.php?route=reset_password");
            return;
        }

        $userModel = new DiseaseModel();

        $hashed = password_hash($newPassword, PASSWORD_BCRYPT);
        $userModel->changePassword($userId, $hashed);

        // Xóa session
        unset($_SESSION['reset_code'], $_SESSION['reset_user_id']);

        $_SESSION['success'] = "Mật khẩu đã được cập nhật.";
        header("Location: index.php?route=login");
    }

    public function resendVerificationCode()
    {
        $email = $_SESSION['reset_email'] ?? null;
        $userId = $_SESSION['reset_user_id'] ?? null;

        if (!$email || !$userId) {
            $_SESSION['error'] = "Không thể gửi lại mã. Vui lòng thử lại từ đầu.";
            header("Location: index.php?route=forgot_password");
            exit;
        }

        $userModel = new DiseaseModel();
        $user = $userModel->getUserById($userId); // Bạn cần thêm hàm này trong model nếu chưa có

        if (!$user) {
            $_SESSION['error'] = "Không tìm thấy người dùng.";
            header("Location: index.php?route=forgot_password");
            exit;
        }

        $code = rand(100000, 999999);
        $_SESSION['reset_code'] = $code;

        // Gửi email
        require_once __DIR__ . '/../../vendor/autoload.php';
        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->Encoding = 'base64';
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'nkhai.contact@gmail.com';
            $mail->Password = 'ophsfnsjvbfrtsrp';
            $mail->SMTPSecure = 'tls';
            $mail->Port = 587;

            $mail->setFrom('nkhai.contact@gmail.com', 'HealMate');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Mã xác nhận khôi phục mật khẩu';
            $mail->Body = "Xin chào <b>{$user['username']}</b>,<br><br>
                      Mã xác nhận mới của bạn là: <h2>$code</h2><br>
                      Nếu không yêu cầu, vui lòng bỏ qua.<br><br>
                      Trân trọng,<br>HealMate Team";

            $mail->send();
            $_SESSION['success'] = "Mã xác nhận mới đã được gửi lại.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Không thể gửi lại mã. Lỗi: {$mail->ErrorInfo}";
        }

        header("Location: index.php?route=verify-reset-code");
    }
}
