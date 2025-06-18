<?php
require_once '../config/database.php';

class DiseaseModel
{
    private $pdo;

    public function __construct()
    {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function getDiseaseDetails($diseaseName)
    {
        // 1. Lấy thông tin bệnh
        $stmt = $this->pdo->prepare("SELECT * FROM diseases WHERE disease = :name LIMIT 1");
        $stmt->execute(['name' => $diseaseName]);
        $disease = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$disease) return null;
        $diseaseId = $disease['id'];

        // 2. Lấy thuốc
        $medication = $this->pdo->prepare("SELECT medication FROM medications WHERE disease_id = :id LIMIT 1");
        $medication->execute(['id' => $diseaseId]);
        $medication = $medication->fetchColumn();

        // 3. Chế độ ăn
        $diet = $this->pdo->prepare("SELECT diet FROM diets WHERE disease_id = :id LIMIT 1");
        $diet->execute(['id' => $diseaseId]);
        $diet = $diet->fetchColumn();

        // 4. Tập luyện
        $stmt = $this->pdo->prepare("SELECT workout FROM workouts WHERE disease_id = :id");
        $stmt->execute(['id' => $diseaseId]);
        $workouts = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 5. Phòng ngừa
        $stmt = $this->pdo->prepare("SELECT precaution FROM precautions WHERE disease_id = :id");
        $stmt->execute(['id' => $diseaseId]);
        $precautions = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 6. Trả dữ liệu bệnh
        return [
            'id' => $diseaseId,
            'disease' => $disease['disease'],
            'description' => $disease['description'],
            'medication' => $medication,
            'diet' => $diet,
            'workouts' => $workouts,
            'precautions' => $precautions,
            // Không lấy triệu chứng ở đây nữa
        ];
    }
public function getSymptomWeights(array $symptoms)
{
    if (empty($symptoms)) return [];

    // Tạo chuỗi placeholder: ?, ?, ?
    $placeholders = implode(',', array_fill(0, count($symptoms), '?'));
    $sql = "SELECT symptom, weight FROM symptoms WHERE symptom IN ($placeholders)";


    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(array_values($symptoms)); // ← Truyền dạng mảng tuần tự
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    
}

}