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

    public function search()
    {
        $keyword = trim($_GET['query'] ?? '');
        $results = $keyword !== '' ? $this->model->searchDrugsWithDetails($keyword) : [];
        require '../app/views/auth/search_result.php';
    }

    // Hiển thị lịch sử người dùng
    public function profile()
    {
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            header("Location: index.php?route=login");
            exit();
        }

        $userHistories = $this->model->getUserHistory($userId); // gọi model

        require '../app/views/auth/profile.php';
    }

    public function historyDetail()
    {
        if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
            echo "ID không hợp lệ.";
            return;
        }

        $historyId = $_GET['id'];
        $userId = $_SESSION['user']['id'] ?? null;

        if (!$userId) {
            echo "Không xác định người dùng.";
            return;
        }

        $history = $this->model->getUserHistoryById($historyId);

        if (!$history || $history['user_id'] != $userId) {
            echo "Không tìm thấy lịch sử.";
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
}
