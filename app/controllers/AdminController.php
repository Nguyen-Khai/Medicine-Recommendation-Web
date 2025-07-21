<?php
require_once '../config/database.php';
require_once '../app/models/AdminModel.php';
global $pdo;

class AdminController
{
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
                    'avatar' => $admin['avatar'] ?? null,
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
        session_unset();        // Xóa tất cả biến phiên
        session_destroy();      // Hủy session
        header('Location: index.php?route=admin-login');
        exit;
    }

    public function dashboard($isPartial = false)
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        $totalUsers = $model->getTotalUsers();
        $diagnosesToday = $model->getDiagnosesToday();
        $topDisease = $model->getTopDisease();
        $unreadFeedback = $model->getUnreadFeedbackCount();
        $diagnosesPerDay = $model->getDiagnosesThisWeek();

        // load view và xử lý layout
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

        $keyword = $_GET['keyword'] ?? '';
        $status = $_GET['status'] ?? '';

        $users = $adminModel->getAllUsers($keyword, $status);

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

    // Permission
    public function managePermissions()
    {
        if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'superadmin') {
            echo "Bạn không phải superadmin. Role hiện tại là: " . ($_SESSION['admin']['role'] ?? 'không xác định');
            exit;
        }

        // Nếu muốn hiển thị giao diện quản lý quyền:
        $permissions = $this->model->getAllPermissions();
        $title = "Manage Permissions";

        ob_start();
        require '../app/views/admin/permissions.php';
        $content = ob_get_clean();

        require '../app/views/admin/home.php';
    }

    public function updatePermissions()
    {
        if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'superadmin') {
            $_SESSION['error_message'] = "Bạn không có quyền truy cập trang này.";
            header("Location: index.php?route=manage-permissions");
            exit;
        }

        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        $read = $_POST['read'] ?? [];
        $write = $_POST['write'] ?? [];
        $delete = $_POST['delete'] ?? [];

        foreach ($read + $write + $delete as $id => $_) {
            $canRead = isset($read[$id]) ? 1 : 0;
            $canWrite = isset($write[$id]) ? 1 : 0;
            $canDelete = isset($delete[$id]) ? 1 : 0;

            $model->updatePermission($id, $canRead, $canWrite, $canDelete);
        }

        $_SESSION['admin_message'] = "Permissions updated successfully!";
        $_SESSION['admin_message_type'] = "success";
        header("Location: index.php?route=manage-permissions");
    }


    //Add admin
    public function addAdmin($isPartial = false)
    {
        // Kiểm tra quyền superadmin
        if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'superadmin') {
            $_SESSION['error_message'] = "Bạn không có quyền truy cập trang này.";
            header("Location: index.php?route=admin-dashboard");
            exit;
        }

        $title = "Add Admin";
        ob_start();
        require '../app/views/admin/add_admin.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }


    public function createAdmin()
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'staff';

        if ($username && $email && $password) {
            // Kiểm tra username đã tồn tại chưa
            if ($model->findByUsername($username)) {
                $_SESSION['admin_message'] = "Tên đăng nhập đã tồn tại. Vui lòng chọn tên khác.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: index.php?route=add-admin");
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            try {
                $result = $model->createAdmin($username, $email, $hashedPassword, $role, null);
                $model->logAction($_SESSION['admin']['id'], 'Created a new admin: ' . $username);

                if ($result) {
                    $_SESSION['admin_message'] = "Tạo tài khoản admin thành công!";
                    $_SESSION['admin_message_type'] = "success";
                } else {
                    $_SESSION['admin_message'] = "Không thể tạo tài khoản. Có thể do trùng tên đăng nhập hoặc lỗi hệ thống.";
                    $_SESSION['admin_message_type'] = "error";
                }
            } catch (PDOException $e) {
                $_SESSION['admin_message'] = "Lỗi: " . $e->getMessage();
                $_SESSION['admin_message_type'] = "error";
            }
        } else {
            $_SESSION['admin_message'] = "Vui lòng điền đầy đủ thông tin.";
            $_SESSION['admin_message_type'] = "error";
        }

        header("Location: index.php?route=add-admin");
        exit;
    }

    // Thông tin các admin
    public function showAdminList()
    {
        require_once '../app/models/AdminModel.php';
        $adminModel = new AdminModel();
        $admins = $adminModel->getAllAdmins();

        // Lấy admin hiện tại từ session
        $currentAdmin = $_SESSION['admin'] ?? null;

        $title = "Admin List";
        ob_start();
        require '../app/views/admin/admin_list.php';
        $content = ob_get_clean();
        require '../app/views/admin/home.php';
    }

    // Hiển thị form sửa admin
    public function editAdmin($id)
    {
        require_once '../app/models/AdminModel.php';
        $adminModel = new AdminModel();
        $admin = $adminModel->getAdminById($id);

        // Kiểm tra phân quyền: chỉ superadmin và manager được sửa, không cho sửa superadmin nếu không phải superadmin
        $currentRole = $_SESSION['admin']['role'];
        if (!$admin || ($admin['role'] === 'superadmin' && $currentRole !== 'superadmin')) {
            header('Location: index.php?route=admins');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $role = $_POST['role'] ?? '';

            // Không cho cấp thấp sửa quyền cao hơn
            if ($currentRole !== 'superadmin' && $role === 'superadmin') {
                $role = $admin['role'];
            }

            $adminModel->updateAdmin($id, $username, $email, $role);
            header('Location: index.php?route=admins');
            exit;
        }

        $title = "Sửa thông tin Admin";
        ob_start();
        require '../app/views/admin/edit_admin.php';
        $content = ob_get_clean();
        require '../app/views/admin/home.php';
    }

    // Xóa admin
    public function deleteAdmin($id)
    {
        require_once '../app/models/AdminModel.php';
        $adminModel = new AdminModel();
        $admin = $adminModel->getAdminById($id);

        // Kiểm tra phân quyền: chỉ superadmin xóa được, không xóa superadmin
        if ($_SESSION['admin']['role'] === 'superadmin' && $admin && $admin['role'] !== 'superadmin') {
            $adminModel->deleteAdmin($id);
        }

        header('Location: index.php?route=admins');
        exit;
    }

    //Feedback
    public function feedbacks($isPartial = false)
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();
        $feedbacks = $model->getAllFeedbacks();

        $title = "Phản hồi người dùng";
        ob_start();
        require '../app/views/admin/feedbacks.php';
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

    // Thuốc
    public function drugs($isPartial = false)
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        // Lấy các tham số lọc từ GET, với fallback an toàn
        $keyword = trim($_GET['keyword'] ?? '');
        $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

        // Phân trang
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Thiết lập điều kiện lọc
        $filters = [
            'keyword' => $keyword,
            'category_id' => $categoryId,
        ];

        // Lấy dữ liệu thuốc theo trang và lọc
        $drugs = $model->getAllDrugs($filters, $perPage, $offset);
        $totalDrugs = $model->countDrugs($filters);
        $totalPages = ceil($totalDrugs / $perPage);

        $currentPage = $page;

        // Tải view
        $title = "Drugs";
        ob_start();
        require '../app/views/admin/drugs.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }

    //Thêm sửa xóa thuốc
    // Trong AdminController.php
    public function deleteDrug()
    {
        session_start();
        if ($_SESSION['admin']['role'] !== 'superadmin') {
            header('Location: index.php?route=admin-drugs&error=unauthorized');
            exit;
        }

        $id = $_GET['id'] ?? null;
        if ($id) {
            require_once '../app/models/AdminModel.php';
            $model = new AdminModel();
            $model->deleteDrugById($id);
        }

        header('Location: index.php?route=admin-drugs');
        exit;
    }

    public function editDrug()
    {
        session_start();
        if (!in_array($_SESSION['admin']['role'], ['superadmin', 'manager'])) {
            header('Location: index.php?route=admin-drugs&error=unauthorized');
            exit;
        }

        $id = $_GET['id'] ?? null;
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $model->updateDrug($id, $data);
            header('Location: index.php?route=admin-drugs');
            exit;
        }

        $drug = $model->getDrugById($id);
        $ingredients = $model->getIngredientsByDrugId($id); // Load thành phần

        require '../app/views/admin/edit_drug.php';
    }

    //Xử lí cập nhật thuốc
    public function updateDrug()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            die("Thiếu ID thuốc");
        }

        $data = [
            'ten_thuoc' => $_POST['ten_thuoc'],
            'dang_bao_che' => $_POST['dang_bao_che'],
            'so_dang_ky' => $_POST['so_dang_ky'],
            'quy_cach' => $_POST['quy_cach'],
            'han_su_dung' => $_POST['han_su_dung'],
            'url' => $_POST['url'],
            'hoat_chat' => $_POST['hoat_chat'] ?? []
        ];

        $model = new AdminModel();
        if ($model->updateDrug($id, $data)) {
            header("Location: index.php?route=admin-drugs&msg=success");
            exit;
        } else {
            echo "Cập nhật thất bại.";
        }
    }

    private $model;
    private $pdo;

    public function __construct($pdo)
    {
        $this->model = new AdminModel($pdo);
    }

    public function diseases($isPartial = false)
    {
        // Lấy dữ liệu tìm kiếm và phân trang
        $keyword = $_GET['keyword'] ?? '';
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($currentPage - 1) * $limit;

        // Lấy danh sách bệnh từ model
        $diseases = $this->model->getAllDiseases($keyword, $limit, $offset);
        $totalDiseases = $this->model->countDiseases($keyword);
        $totalPages = ceil($totalDiseases / $limit);

        // Đưa dữ liệu ra view
        include '../app/views/admin/diseases.php';
    }

    // Settings
    public function updatePassword()
    {
        if (!isset($_SESSION['admin'])) {
            header('Location: index.php?route=admin-login');
            exit;
        }

        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if ($newPassword !== $confirmPassword) {
            $_SESSION['flash_error'] = "New passwords do not match.";
            header('Location: index.php?route=admin-settings');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$_SESSION['admin']['id']]);
        $admin = $stmt->fetch();

        if (!$admin || !password_verify($currentPassword, $admin['password'])) {
            $_SESSION['flash_error'] = "Current password is incorrect.";
            header('Location: index.php?route=admin-settings');
            exit;
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $this->pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
        $update->execute([$newHash, $_SESSION['admin']['id']]);

        $_SESSION['flash_success'] = "Password updated successfully.";
        header('Location: index.php?route=admin-settings');
    }
}
