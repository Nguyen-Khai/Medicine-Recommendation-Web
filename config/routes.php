<?php
$route = $_GET['route'] ?? 'login';
$route = $_GET['route'] ?? 'home';

require_once '../app/controllers/UserController.php';
require_once '../app/controllers/DiagnosisController.php';

// Khởi tạo controller
$userController = new UserController();
$diseaseController = new DiseaseController();

switch ($route) {

    case 'home':
        include '../app/views/auth/home.php';
        break;

    case 'introduction':
        include '../app/views/auth/introduction.php';
        break;

    case 'medicine_cabinet':
        $diseaseController->renderMedicineCabinet(); // ← gọi controller thay vì chỉ include
        break;

    case 'recommendation':
        include '../app/views/auth/recommendation.php';
        break;

    case 'profile':
        $diseaseController->profile();
        break;

    case 'history-detail':
        $diseaseController->historyDetail();
        break;

    case 'update-profile':
        $diseaseController->updateProfile();
        break;

    case 'logout':
        session_start();
        session_destroy();
        header("Location: index.php?route=login");
        exit();

    case 'login':
        $userController->login();
        break;

    case 'register':
        $userController->register();
        break;

    case 'handle-login':
        $userController->handleLogin();
        break;

    case 'handle-register':
        $userController->handleRegister();
        break;

    case 'forgot_password':
        $userController->fogrot_password();
        break;

    case 'handle-forgot-password':
        $userController->handleForgotPassword();
        break;

    case 'verify-reset-code':
        $userController->verify_reset_code();
        break;

    case 'verifyResetCode':
        $userController->verifyResetCode();
        break;

    case 'reset_password':
        $userController->reset_password();
        break;

    case 'handle-reset-password':
        $userController->handleResetPassword();
        break;

    case 'resend-verification-code':
        $userController->resendVerificationCode();
        break;

    case 'change-password':
        require_once '../app/controllers/UserController.php';
        $controller = new UserController();
        $controller->changePassword();
        break;

    case 'diagnose':
        $diseaseController->diagnose();
        break;

    case 'autocomplete':
        $diseaseController->searchSuggestions();
        break;

    case 'search':
        $diseaseController->search();
        break;

    default:
        echo "404 - Không tìm thấy đường dẫn.";
        break;
}
