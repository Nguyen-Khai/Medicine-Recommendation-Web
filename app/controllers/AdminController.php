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

    private function logAction($action)
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        if (isset($_SESSION['admin'])) {
            $adminId = $_SESSION['admin']['id'];
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;
            $model->logAction($adminId, $action, $ip, $userAgent);
        }
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
                    'name' => $admin['name'],
                    'email' => $admin['email'],
                    'avatar' => $admin['avatar'] ?? 'assets/images/default_avatar.png',
                ];
                $this->logAction("Login successful");
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
        $this->logAction("Logged out");
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
        $this->logAction("Added user ID {$id}");
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
            $this->logAction("Updated admin ID {$id}");
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
        $this->logAction("Deleted user ID {$id}");
        header('Location: index.php?route=admins');
        exit;
    }

    //Feedback
    public function feedbacks($isPartial = false)
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $reply = $_GET['reply'] ?? '';

        $feedbacks = $model->getFilteredFeedbacks($search, $status);

        // Lọc trùng ID feedback
        $seen = [];
        $unique_feedbacks = [];
        foreach ($feedbacks as $f) {
            if (!in_array($f['id'], $seen)) {
                $seen[] = $f['id'];
                $unique_feedbacks[] = $f;
            }
        }
        $feedbacks = $unique_feedbacks;

        // Đánh dấu đã trả lời
        foreach ($feedbacks as $key => $f) {
            $feedbacks[$key]['has_replied'] = $model->hasReplied($f['id']);
        }

        // Lọc theo phản hồi
        if ($reply === 'replied') {
            $feedbacks = array_filter($feedbacks, fn($f) => $f['has_replied']);
        } elseif ($reply === 'not_replied') {
            $feedbacks = array_filter($feedbacks, fn($f) => !$f['has_replied']);
        }

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

    //Phản hồi feedback
    public function replyFeedback($id)
    {
        if (!$id) {
            echo "Thiếu ID phản hồi";
            return;
        }

        $model = new AdminModel();
        $feedback = $model->getFeedbackById($id);

        if (!$feedback) {
            echo "Không tìm thấy phản hồi";
            return;
        }

        include '../app/views/admin/reply_feedback.php';
    }

    public function sendFeedbackReply($id)
    {
        if (!$id || $_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=feedbacks");
            return;
        }

        $replyMessage = trim($_POST['reply_message'] ?? '');

        if ($replyMessage === '') {
            echo "Vui lòng nhập nội dung phản hồi.";
            return;
        }

        $model = new AdminModel();
        $feedback = $model->getFeedbackById($id);

        if (!$feedback) {
            echo "Phản hồi không tồn tại.";
            return;
        }

        // Gửi email ở đây hoặc lưu lại
        // Gợi ý: dùng PHPMailer nếu cần gửi mail

        $model->storeFeedbackReply($id, $replyMessage); // nếu có lưu vào DB

        header("Location: index.php?route=feedbacks&status=success&message=Phản hồi đã được gửi");
        exit;
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
        $this->logAction("Updated drug ID {$id}");

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
        $this->logAction("Deleted drug ID {$id}");

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
        $this->logAction("Added drug ID {$drugId}");
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
            $this->logAction("Added disease ID {$diseaseId}");
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
        $this->logAction("Updated disease ID {$id}");


        header('Location: index.php?route=admin-diseases');
    }

    public function deleteDisease($id)
    {
        if ($_SESSION['admin']['role'] !== 'superadmin') {
            die("Unauthorized");
        }
        $this->model->deleteDisease($id);
        $this->logAction("Deleted disease ID {$id}");

        header('Location: index.php?route=admin-diseases');
    }

    // Settings
    public function index()
    {
        // Bảo vệ truy cập nếu chưa đăng nhập
        if (!isset($_SESSION['admin']['id'])) {
            header("Location: index.php?route=admin-login");
            exit();
        }

        // Lấy dữ liệu admin từ session
        $admin = $_SESSION['admin'];

        // Hoặc nếu bạn muốn lấy lại từ DB để đảm bảo mới nhất:
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();
        $admin = $model->getAdminById($admin['id']);

        // Truyền sang view
        require_once '../app/views/admin/admin-settings.php';
    }

    public function editInfo()
    {
        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();

        if (!isset($_SESSION['admin']['id'])) {
            header("Location: index.php?route=admin-login");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $adminId = $_SESSION['admin']['id'];
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);

            if ($name !== $_SESSION['admin']['name'] || $email !== $_SESSION['admin']['email']) {
                $model->updateAdminInfo($adminId, $name, $email);

                // Cập nhật lại session
                $_SESSION['admin']['name'] = $name;
                $_SESSION['admin']['email'] = $email;

                $_SESSION['admin_success'] = "Updated successfully!";
            } else {
                $_SESSION['admin_info'] = "No changes detected.";
            }
        }
        $this->logAction("Updated profile information");

        // Quay lại trang settings sau khi xử lý
        header("Location: index.php?route=admin-settings&tab=personal-info");
        exit();
    }

    public function changePassword()
    {
        // Check if the admin is logged in
        if (!isset($_SESSION['admin']['id'])) {
            header("Location: index.php?route=admin-login");
            exit();
        }

        // Check if the form sent 'current_password'
        if (!isset($_POST['current_password'])) {
            echo "Form did not submit current_password";
            exit();
        }

        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        // Check if the new passwords match
        if ($new !== $confirm) {
            $_SESSION['password_error'] = "New passwords do not match.";
            header("Location: index.php?route=admin-settings&tab=password");
            exit();
        }

        require_once '../app/models/AdminModel.php';
        $model = new AdminModel();
        $adminId = $_SESSION['admin']['id'];
        $admin = $model->getAdminById($adminId);

        // Verify current password
        if (!$admin || !password_verify($current, $admin['password'])) {
            $_SESSION['password_error'] = "Current password is incorrect.";
            header("Location: index.php?route=admin-settings&tab=password");
            exit();
        }

        // Hash and update new password
        $hashedPassword = password_hash($new, PASSWORD_DEFAULT);
        $updated = $model->updatePassword($adminId, $hashedPassword);

        // Set success or error message
        if ($updated) {
            $_SESSION['password_success'] = "Password has been updated.";
        } else {
            $_SESSION['password_error'] = "An error occurred while updating the password.";
        }
        $this->logAction("Changed password");

        header("Location: index.php?route=admin-settings&tab=password");
        exit();
    }

    public function uploadAvatar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
            $file = $_FILES['avatar'];

            if ($file['error'] === UPLOAD_ERR_OK) {
                $fileData = file_get_contents($file['tmp_name']);
                $mimeType = mime_content_type($file['tmp_name']);

                $model = new AdminModel();
                $adminId = $_SESSION['admin']['id'];
                $model->updateAvatar($adminId, $fileData, $mimeType);

                $_SESSION['success'] = "Avatar đã được cập nhật thành công!";
                header("Location: index.php?route=admin-settings&tab=avatar");
                exit();
            } else {
                $_SESSION['error'] = "Không thể upload ảnh.";
            }
        }
        $this->logAction("Updated avatar");

        header("Location: index.php?route=admin-settings&tab=avatar");
        exit();
    }

    public function updateLanguage()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['language'])) {
            $lang = $_POST['language'];

            if (in_array($lang, ['en', 'vi'])) {
                $_SESSION['admin']['language'] = $lang;

                // Optional: Lưu vào database nếu cần
                // $adminId = $_SESSION['admin']['id'];
                // (new AdminModel())->updateLanguage($adminId, $lang);
            }
        }

        header("Location: index.php?route=admin-settings&tab=language");
        exit();
    }

    public function showSettings()
    {
        require_once '../app/models/AdminModel.php';
        $logModel = new AdminModel();
        $adminId = $_SESSION['admin']['id'];
        $logs = $logModel->getLogsByAdminId($adminId);

        $title = "Admin Settings";
        ob_start();
        require '../app/views/admin/settings.php';
        $content = ob_get_clean();
        require '../app/views/admin/home.php'; // hoặc layout chính
    }
}
