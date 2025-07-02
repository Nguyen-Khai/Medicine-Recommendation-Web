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

    public function findDrugsByIngredients(array $keywords)
    {
        if (empty($keywords)) return [];

        $placeholders = implode(',', array_fill(0, count($keywords), '?'));

        $sql = "SELECT DISTINCT drugs.ten_thuoc, active_ingredients.ten_hoat_chat, active_ingredients.ham_luong,
                drugs.url, drugs.dang_bao_che
                FROM drugs
                JOIN active_ingredients ON drugs.id = active_ingredients.drug_id
                WHERE active_ingredients.ten_hoat_chat IN ($placeholders)
                LIMIT 20";
        $stmt1 = $this->pdo->prepare($sql);
        $stmt1->execute($keywords);
        $results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

        $sql2 = "SELECT DISTINCT drugs.ten_thuoc, active_ingredients.ten_hoat_chat, active_ingredients.ham_luong,
                drugs.url, drugs.dang_bao_che
                FROM drugs
                JOIN active_ingredients ON drugs.id = active_ingredients.drug_id
                WHERE drugs.ten_thuoc IN ($placeholders)
                LIMIT 20";
        $stmt2 = $this->pdo->prepare($sql2);
        $stmt2->execute($keywords);
        $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        // Gộp kết quả và loại trùng
        return array_unique(array_merge($results1, $results2), SORT_REGULAR);
    }

    //Tủ thuốc
    public function getAllDrugArticles()
    {
        $sql = "SELECT d.ten_thuoc, a.ten_hoat_chat, a.ham_luong, d.url
            FROM drugs d
            JOIN active_ingredients a ON d.id = a.drug_id";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Tìm kiếm
    public function searchDrugSuggestions(string $query): array
    {
        if (trim($query) === '') return [];

        $keyword = strtolower(trim($query));

        // 1. Truy vấn tất cả dữ liệu phù hợp
        $searchTerm = '%' . $keyword . '%';

        $stmt = $this->pdo->prepare("SELECT DISTINCT ten_thuoc AS name FROM drugs WHERE LOWER(ten_thuoc) LIKE ?
                                            UNION
                                            SELECT DISTINCT ten_hoat_chat AS name FROM active_ingredients WHERE LOWER(ten_hoat_chat) LIKE ?");
        $stmt->execute([$searchTerm, $searchTerm]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 2. Ưu tiên kết quả bắt đầu bằng keyword, sau đó là chứa keyword, rồi gần giống
        $sorted = [];
        foreach ($results as $item) {
            $lower = strtolower($item);

            if (strpos($lower, $keyword) === 0) {
                $sorted[] = ['score' => 3, 'value' => $item];
            } elseif (strpos($lower, $keyword) !== false) {
                $sorted[] = ['score' => 2, 'value' => $item];
            } else {
                similar_text($lower, $keyword, $percent);
                if ($percent > 50) {
                    $sorted[] = ['score' => 1, 'value' => $item];
                }
            }
        }

        usort($sorted, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice(array_column($sorted, 'value'), 0, 10); // Trả về tối đa 10 gợi ý
    }
    //Kết quả tìm kiếm
    public function searchDrugsWithDetails(string $keyword): array
    {
        $keyword = '%' . strtolower($keyword) . '%';

        $stmt = $this->pdo->prepare("SELECT DISTINCT d.ten_thuoc, a.ten_hoat_chat, a.ham_luong, d.url
                                            FROM drugs d
                                            JOIN active_ingredients a ON d.id = a.drug_id
                                            WHERE LOWER(d.ten_thuoc) LIKE ? OR LOWER(a.ten_hoat_chat) LIKE ?
                                            LIMIT 50");
        $stmt->execute([$keyword, $keyword]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
