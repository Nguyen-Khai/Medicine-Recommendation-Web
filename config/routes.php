<?php
$route = $_GET['route'] ?? 'login';
$route = $_GET['route'] ?? 'home';
$pdo = new PDO("mysql:host=localhost;dbname=medicine_system;charset=utf8", "root", "");
$isPartial = isset($_GET['layout']) && $_GET['layout'] === 'partial';

require_once '../app/controllers/UserController.php';
require_once '../app/controllers/DiagnosisController.php';
require_once '../app/controllers/AdminController.php';
require_once '../config/database.php';

// Khởi tạo controller
$userController = new UserController();
$diseaseController = new DiseaseController();
$adminController = new AdminController($pdo);

switch ($route) {
    // Admin
    case 'admin-dashboard':
        $adminController->dashboard($isPartial);
        break;
    case 'feedbacks':
        $adminController->feedbacks();
        break;
    case 'mark-feedback':
        $model = new AdminModel();
        $model->markFeedbackRead($_GET['id']);
        header("Location: index.php?route=feedbacks");
        break;
    case 'delete-feedback':
        $model = new AdminModel();
        $model->deleteFeedback($_GET['id']);
        header("Location: index.php?route=feedbacks");
        break;
    case 'manage-permissions':
        $adminController->managePermissions();
        break;
    case 'update-permissions':
        $adminController->updatePermissions();
        break;
    case 'add-admin':
        $adminController->addAdmin($isPartial);
        break;
    case 'create-admin':
        $adminController->createAdmin();
        break;
    case 'admins':
        $adminController->showAdminList();
        break;
    case 'edit-admin':
        $adminController->editAdmin($_GET['id']);
        break;
    case 'delete-admin':
        $adminController->deleteAdmin($_GET['id']);
        break;
    case 'admin-users':
        $adminController->users($isPartial);
        break;
    case 'deactivate-user':
        $model = new AdminModel();
        $model->updateStatus($_GET['id'], 0);
        header('Location: index.php?route=admin-users');
        break;
    case 'activate-user':
        $model = new AdminModel();
        $model->updateStatus($_GET['id'], 1);
        header('Location: index.php?route=admin-users');
        break;
    case 'admin-diagnosis':
        $adminController->diagnosis($isPartial);
        break;
    case 'admin-drugs':
        $adminController->drugs($isPartial);
        break;
    case 'admin-diseases':
        $adminController->diseases($isPartial);
        break;
    case 'admin-settings':
        include '../app/views/admin/settings.php';
        break;
    case 'admin-update-password':
        require_once '../app/controllers/AdminController.php';
        $settingsController = new AdminController($pdo);
        $settingsController->updatePassword();
        break;
    case 'admin-login':
        $adminController->showLogin();
        break;
    case 'admin-login-handler':
        $adminController->handleLogin();
        break;
    case 'admin-logout':
        $adminController->logout();
        break;
    // Người dùng
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
