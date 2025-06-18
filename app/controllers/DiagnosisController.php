<?php
require_once '../app/models/DiseaseModel.php';

class DiseaseController {
    private $model;

    public function __construct() {
        $this->model = new DiseaseModel();
    }

    public function diagnose() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
           // Chuẩn hoá dữ liệu người dùng nhập
            $raw_input = $_POST['symptoms'] ?? '';
            $input_symptoms = strtolower(trim($raw_input));
            $input_symptoms = preg_replace('/\s*,\s*/', ',', $input_symptoms);

            // ✅ Thay dấu cách bằng gạch dưới (skin rash → skin_rash)
            $input_symptoms = str_replace(' ', '_', $input_symptoms);

            $enteredSymptoms = explode(',', $input_symptoms);


            // Đường dẫn tuyệt đối
            $pythonPath  = 'C:\Users\Admin\AppData\Local\Programs\Python\Python311\python.exe';
            $scriptPath  = 'C:\xampp\htdocs\disease_diagnosis_system\ml\predict.py';
            $command     = "\"$pythonPath\" \"$scriptPath\" " . escapeshellarg($input_symptoms);

            // Gọi mô hình
            $command .= " 2>&1";  // Gom cả lỗi (stderr) vào kết quả
            $output = shell_exec($command);

            // Phân tích kết quả JSON
            $data = json_decode($output, true);
            $diseaseName = $data['disease'] ?? 'Không xác định';

            // Truy vấn MySQL
            $enteredSymptoms = array_map('trim', explode(',', $input_symptoms));
            $enteredSymptoms = array_filter($enteredSymptoms); // loại bỏ chuỗi rỗng nếu có


            // Lấy thông tin bệnh
            $diseaseInfo = $this->model->getDiseaseDetails($diseaseName);

            // Lấy trọng số các triệu chứng người dùng nhập
            $symptomWeights = $this->model->getSymptomWeights($enteredSymptoms);

            // Gửi sang view
            require '../app/views/auth/result.php';
        } else {
            require '../app/views/auth/form.php';
        }
    }
}
