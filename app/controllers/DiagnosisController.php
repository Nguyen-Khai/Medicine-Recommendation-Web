<?php
session_start();  // <- THÊM DÒNG NÀY
require_once '../app/models/DiseaseModel.php';


class DiseaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new DiseaseModel();
    }

    public function diagnose()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Chuẩn hoá triệu chứng
            $raw_input = $_POST['symptoms'] ?? '';
            $input_symptoms = str_replace(' ', '_', strtolower(trim($raw_input)));
            $input_symptoms = preg_replace('/\s*,\s*/', ',', $input_symptoms);
            $enteredSymptoms = array_filter(array_map('trim', explode(',', $input_symptoms)));

            // Gọi mô hình Python
            $pythonPath = 'C:\Users\Admin\AppData\Local\Programs\Python\Python311\python.exe';
            $scriptPath = 'C:\xampp\htdocs\disease_diagnosis_system\ml\predict.py';
            $command = "\"$pythonPath\" \"$scriptPath\" " . escapeshellarg($input_symptoms) . " 2>&1";
            $output = shell_exec($command);

            $data = json_decode($output, true);
            $diseaseName = $data['disease'] ?? 'Không xác định';

            // Truy vấn chi tiết bệnh
            $diseaseInfo = $this->model->getDiseaseDetails($diseaseName);

            // Gợi ý thuốc
            $medications = [];
            if (!empty($diseaseInfo['medication'])) {
                $clean = str_replace(["[", "]", "'", '"', "_"], '', $diseaseInfo['medication']);
                $medications = array_filter(array_map('trim', explode(',', $clean)));
            }

            // Lưu lịch sử người dùng
            $userId = $_SESSION['user']['id'] ?? null;
            if ($userId && $diseaseInfo) {
                $this->model->saveUserHistory([
                    'user_id' => $userId,
                    'symptoms' => implode(', ', $enteredSymptoms),
                    'disease' => $diseaseInfo['disease'],
                    'description' => $diseaseInfo['description'],
                    'medications' => implode(', ', $medications),
                    'diet' => $diseaseInfo['diet'] ?? '',
                    'workouts' => implode('| ', $diseaseInfo['workouts'] ?? []),
                    'precautions' => implode('| ', $diseaseInfo['precautions'] ?? [])
                ]);
            }

            // Thuốc trong hệ thống
            $matchingDrugs = $this->model->findDrugsByIngredients($medications);

            // Trọng số triệu chứng
            $symptomWeights = $this->model->getSymptomWeights($enteredSymptoms);

            // Gửi sang view
            require '../app/views/auth/result.php';
        } else {
            require '../app/views/auth/form.php';
        }
    }

    public function renderMedicineCabinet()
    {
        $medicines = $this->model->getAllDrugArticles();
        require '../app/views/auth/medicine_cabinet.php';
    }

    public function searchSuggestions()
    {
        $term = $_GET['query'] ?? '';
        echo json_encode($term !== '' ? $this->model->searchDrugSuggestions($term) : []);
    }

    //Lịch sử tìm kiếm
    public function search()
    {
        $keyword = $_GET['query'] ?? '';
        $keyword = trim($keyword);

        $results = [];
        if ($keyword !== '') {
            $results = $this->model->searchDrugsWithDetails($keyword);

            // ✅ Lưu lịch sử tìm kiếm nếu người dùng đã đăng nhập
            if (isset($_SESSION['user']['id'])) {
                $this->model->saveSearchHistory($_SESSION['user']['id'], $keyword);
            }
        }

        require '../app/views/auth/search_result.php';
    }

    // Hiển thị lịch sử người dùng
    public function profile()
    {
        $userId = $_SESSION['user']['id'] ?? null;
        $search = $_GET['search'] ?? ''; // 👈 Lấy từ khóa tìm kiếm

        if (!$userId) {
            header("Location: index.php?route=login");
            exit();
        }

        // 👇 Truyền từ khóa vào model
        $userHistories = $this->model->getUserHistory($userId, $search);
        $searchHistories = $this->model->getUserSearchHistory($userId);
        $userInfo = $this->model->getUserById($userId);

        foreach ($userHistories as &$record) {
            $feedback = $this->model->getFeedbackByHistoryId($record['id']);
            $record['has_feedback'] = $feedback !== null;
        }

        require '../app/views/auth/profile.php';
    }

    // Hiển thị lịch sử người dùng
    public function avatar()
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header("Location: index.php?route=login");
            exit();
        }

        $userHistories = $this->model->getUserHistory($userId);
        $searchHistories = $this->model->getUserSearchHistory($userId);
        $userInfo = $this->model->getUserById($userId);

        require '../app/views/auth/home.php';
    }

    public function historyDetail()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            echo "Invalid ID.";
            return;
        }

        $historyId = $_GET['id'];
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            echo "User unknown.";
            return;
        }

        $history = $this->model->getUserHistoryById($historyId);

        if (!$history || $history['user_id'] != $userId) {
            echo "No history found.";
            return;
        }

        // Chuẩn bị biến tương thích với history.php
        $enteredSymptoms = explode(',', $history['symptoms']);
        $diseaseName = $history['predicted_disease'];
        $diseaseInfo = [
            'description' => $history['description'],
            'medication' => $history['medications'],
            'diet' => $history['diet'],
            'workouts' => explode('| ', $history['workouts']),
            'precautions' => explode('| ', $history['precautions']),
        ];

        // Giả lập $symptomWeights nếu muốn (không bắt buộc)
        $symptomWeights = [];

        // Tìm thuốc từ hệ thống
        $keywords = array_filter(array_map('trim', explode(',', $history['medications'])));
        $matchingDrugs = $this->model->findDrugsByIngredients($keywords);

        require '../app/views/auth/history.php';
    }

    //Cập nhập hồ sơ
    public function updateProfile()
    {
        $userId = $_SESSION['user']['id'];

        // Lấy thông tin form
        $name   = $_POST['name'] ?? '';
        $email  = $_POST['email'] ?? '';
        $dob    = $_POST['dob'] ?? null;
        $gender = $_POST['gender'] ?? '';

        // Chuẩn bị avatar nếu có
        $avatarData = null;
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $avatarData = file_get_contents($_FILES['avatar']['tmp_name']);
        }

        // Cập nhật vào DB
        $pdo = require '../config/database.php';

        if ($avatarData) {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, dob = ?, gender = ?, avatar = ? WHERE id = ?");
            $stmt->execute([$name, $email, $dob, $gender, $avatarData, $userId]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, dob = ?, gender = ? WHERE id = ?");
            $stmt->execute([$name, $email, $dob, $gender, $userId]);
        }

        // Cập nhật session (nếu có)
        $_SESSION['user']['name'] = $name;

        header('Location: index.php?route=profile#profile');
        exit();
    }

    //Feedbacks
    public function feedback()
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header("Location: index.php?route=login");
            exit();
        }

        $userInfo = $this->model->getUserById($userId);
        include '../app/views/auth/feedback.php';
    }

    public function handleFeedback()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $message = trim($_POST['message'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $user_id = $_SESSION['user']['id'] ?? null;
            $history_id = $_POST['history_id'] ?? null; // Lấy history_id từ form

            if ($message !== '' && $history_id) {
                require_once '../app/models/DiseaseModel.php';
                $model = new DiseaseModel();
                $model->saveFeedback($user_id, $email, $message, $history_id); // Truyền thêm

                $_SESSION['feedback_info'] = [
                    'message' => $message,
                    'created_at' => date('Y-m-d H:i:s')
                ];

                header("Location: index.php?route=feedback&success=1");
                exit();
            } else {
                header("Location: index.php?route=feedback&error=1");
                exit();
            }
        }
    }

    public function viewFeedback()
    {
        if (!isset($_GET['id'])) {
            echo "Thiếu ID lịch sử";
            return;
        }

        $historyId = $_GET['id'];

        require_once '../app/models/DiseaseModel.php';
        $this->model = new DiseaseModel();

        $feedback = $this->model->getFeedbackByHistoryId($historyId);

        if (!$feedback) {
            echo "Không tìm thấy phản hồi.";
            return;
        }

        require_once '../app/views/auth/view_feedback.php';
    }
}
