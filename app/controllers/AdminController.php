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
                $_SESSION['admin_error'] = "The username or password you entered is incorrect.";
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
            $_SESSION['error_message'] = "You do not have permission to access this page.";
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
            $_SESSION['error_message'] = "You do not have permission to access this page.";
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
                $_SESSION['admin_message'] = "Username already exists. Please choose a different one.";
                $_SESSION['admin_message_type'] = "error";
                header("Location: index.php?route=add-admin");
                exit;
            }

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            try {
                $result = $model->createAdmin($username, $email, $hashedPassword, $role, null);
                $model->logAction($_SESSION['admin']['id'], 'Created a new admin: ' . $username);

                if ($result) {
                    $_SESSION['admin_message'] = "Admin account created successfully!";
                    $_SESSION['admin_message_type'] = "success";
                } else {
                    $_SESSION['admin_message'] = "Account creation failed. The username may already exist or there was a system error.";
                    $_SESSION['admin_message_type'] = "error";
                }
            } catch (PDOException $e) {
                $_SESSION['admin_message'] = "Error: " . $e->getMessage();
                $_SESSION['admin_message_type'] = "error";
            }
        } else {
            $_SESSION['admin_message'] = "Please fill in all required information.";
            $_SESSION['admin_message_type'] = "error";
        }

        header("Location: index.php?route=add-admin");
        exit;
    }

    // Thông tin các admin
    public function showAdminList($isPartial = false)
    {
        require_once '../app/models/AdminModel.php';
        $adminModel = new AdminModel();

        $search = $_GET['search'] ?? '';
        $role = $_GET['role'] ?? '';

        $admins = $adminModel->searchAndFilter($search, $role);

        $title = "Admin Management";

        ob_start();
        require '../app/views/admin/admin_list.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
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

        $title = "Edit Admin Details";
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

        $title = "User Feedback";
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
    public function showEditDrugForm($id, $isPartial)
    {
        $drug = $this->model->getDrugById($id);
        $ingredients = $this->model->getActiveIngredientsByDrugId($id);

        if (!$drug) {
            echo "Drug not found.";
            return;
        }

        $title = "Edit Drug";
        ob_start();
        require '../app/views/admin/edit_drug.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }

    public function updateDrug()
    {
        $id = $_POST['id'];
        $tenThuoc = $_POST['ten_thuoc'];
        $dangBaoChe = $_POST['dang_bao_che'];
        $soDangKy = $_POST['so_dang_ky'];
        $quyCach = $_POST['quy_cach'];
        $hanSuDung = $_POST['han_su_dung'];
        $url = $_POST['url'];
        $activeIngredients = $_POST['active_ingredients'];
        $concentrations = $_POST['concentrations'];

        $this->model->updateDrug($id, $tenThuoc, $dangBaoChe, $soDangKy, $quyCach, $hanSuDung, $url, $activeIngredients, $concentrations);

        header("Location: index.php?route=admin-drugs");
    }

    public function deleteDrug($id)
    {
        // Kiểm tra phân quyền: chỉ superadmin mới được xóa
        if (!isset($_SESSION['admin']) || $_SESSION['admin']['role'] !== 'superadmin') {
            header('Location: index.php?route=admin-login');
            exit;
        }

        if (!$id || !is_numeric($id)) {
            echo "Invalid drug ID.";
            return;
        }

        $success = $this->model->deleteDrugById($id);

        if ($success) {
            header('Location: index.php?route=admin-drugs&status=deleted');
        } else {
            echo "Failed to delete drug.";
        }
    }

    // Thêm thuốc
    // Hiển thị form thêm thuốc
    public function showAddDrugForm($isPartial = false)
    {
        // Tải view
        $title = "Add Drug From";
        ob_start();
        require '../app/views/admin/add_drug.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }

    // Xử lý lưu thuốc
    public function storeDrug()
    {
        // Kiểm tra quyền
        if (!isset($_SESSION['admin']) || !in_array($_SESSION['admin']['role'], ['superadmin', 'manager'])) {
            $_SESSION['error'] = 'You do not have permission to access this page.';
            header('Location: index.php?route=admin-drugs');
            exit;
        }

        // Lấy dữ liệu từ form
        $ten_thuoc = $_POST['ten_thuoc'] ?? '';
        $dang_bao_che = $_POST['dang_bao_che'] ?? '';
        $so_dang_ky = $_POST['so_dang_ky'] ?? '';
        $quy_cach = $_POST['quy_cach'] ?? '';
        $han_su_dung = $_POST['han_su_dung'] ?? '';
        $url = $_POST['url'] ?? '';
        $hoat_chat = $_POST['hoat_chat'] ?? [];

        // Kiểm tra tên thuốc bắt buộc
        if (trim($ten_thuoc) === '') {
            $_SESSION['error'] = 'The drug name is required.';
            header('Location: index.php?route=admin-add-drug');
            exit;
        }

        // Gọi model
        require_once '../app/models/AdminModel.php';
        $drugModel = new AdminModel();

        // Thêm vào bảng drugs
        $drugId = $drugModel->insertDrug([
            'ten_thuoc' => $ten_thuoc,
            'dang_bao_che' => $dang_bao_che,
            'so_dang_ky' => $so_dang_ky,
            'quy_cach' => $quy_cach,
            'han_su_dung' => $han_su_dung,
            'url' => $url
        ]);

        // Nếu thêm thành công, thêm hoạt chất
        if ($drugId && !empty($hoat_chat)) {
            foreach ($hoat_chat as $item) {
                $ten = trim($item['ten'] ?? '');
                $ham_luong = trim($item['ham_luong'] ?? '');

                if ($ten !== '') {
                    $drugModel->insertActiveIngredient([
                        'ten_hoat_chat' => $ten,
                        'ham_luong' => $ham_luong,
                        'drug_id' => $drugId
                    ]);
                }
            }
        }

        $_SESSION['success'] = 'The drug has been added successfully.';
        header('Location: index.php?route=admin-drugs');
        exit;
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

    public function showAddDiseaseForm($isPartial)
    {
        // Tải view
        $title = "Add New Disease";
        ob_start();
        require '../app/views/admin/add_disease.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }

    public function addDisease()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $symptoms = explode("\n", $_POST['symptoms'] ?? '');
            $precautions = explode("\n", $_POST['precautions'] ?? '');
            $medications = explode("\n", $_POST['medications'] ?? '');
            $diets = explode("\n", $_POST['diets'] ?? '');
            $workouts = explode("\n", $_POST['workouts'] ?? '');

            // Bắt đầu thêm vào CSDL
            require_once '../app/models/AdminModel.php';
            $model = new AdminModel();

            // 1. Thêm bệnh
            $diseaseId = $model->addDisease($name, $description);

            // 2. Thêm các liên kết
            $model->addDiseaseSymptoms($diseaseId, $symptoms);
            $model->addPrecautions($diseaseId, $precautions);
            $model->addMedications($diseaseId, $medications);
            $model->addDiets($diseaseId, $diets);
            $model->addWorkouts($diseaseId, $workouts);

            // Redirect hoặc hiển thị thông báo
            header('Location: index.php?route=admin-diseases&success=1');
            exit;
        } else {
            require_once '../app/views/admin/add_disease.php';
        }
    }

    //Sửa, xóa disease
    public function showEditDiseaseForm($id, $isPartial = false)
    {
        $disease = $this->model->getDiseaseById($id);
        $symptoms = $this->model->getSymptomsByDisease($id);
        $medications = $this->model->getMedicationsByDisease($id);
        $diets = $this->model->getDietsByDisease($id);
        $precautions = $this->model->getPrecautionsByDisease($id);
        $workouts = $this->model->getWorkoutsByDisease($id);

        $title = "Edit Disease";
        ob_start();
        require '../app/views/admin/edit_disease.php';
        $content = ob_get_clean();

        if ($isPartial) {
            echo $content;
        } else {
            require '../app/views/admin/home.php';
        }
    }

    public function updateDisease()
    {
        $id = $_POST['id'];
        $data = [
            'disease' => $_POST['disease'],
            'description' => $_POST['description'],
            'diets' => $_POST['diets'] ?? [],
            'medications' => $_POST['medications'] ?? [],
            'precautions' => $_POST['precautions'] ?? [],
            'workouts' => $_POST['workouts'] ?? [],
            'symptoms' => $_POST['symptoms'] ?? []
        ];

        $this->model->updateDisease($id, $data);

        header('Location: index.php?route=admin-diseases');
    }

    public function deleteDisease($id)
    {
        if ($_SESSION['admin']['role'] !== 'superadmin') {
            die("Unauthorized");
        }
        $this->model->deleteDisease($id);
        header('Location: index.php?route=admin-diseases');
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
