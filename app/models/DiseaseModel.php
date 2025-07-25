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
        // 1. Lọc sạch từ khóa đầu vào
        $cleanedQuery = preg_replace('/[^a-z0-9\s\-]/i', '', strtolower(trim($query)));

        if ($cleanedQuery === '') return [];

        $searchTerm = '%' . $cleanedQuery . '%';

        // 2. Truy vấn DB
        $stmt = $this->pdo->prepare("
        SELECT DISTINCT ten_thuoc AS name 
        FROM drugs 
        WHERE LOWER(ten_thuoc) LIKE ?
        UNION
        SELECT DISTINCT ten_hoat_chat AS name 
        FROM active_ingredients 
        WHERE LOWER(ten_hoat_chat) LIKE ?
    ");
        $stmt->execute([$searchTerm, $searchTerm]);
        $results = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // 3. Ưu tiên sắp xếp
        $sorted = [];
        foreach ($results as $item) {
            $lower = strtolower($item);

            if (strpos($lower, $cleanedQuery) === 0) {
                $sorted[] = ['score' => 3, 'value' => $item];
            } elseif (strpos($lower, $cleanedQuery) !== false) {
                $sorted[] = ['score' => 2, 'value' => $item];
            } else {
                similar_text($lower, $cleanedQuery, $percent);
                if ($percent > 50) {
                    $sorted[] = ['score' => 1, 'value' => $item];
                }
            }
        }

        usort($sorted, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice(array_column($sorted, 'value'), 0, 10);
    }

    //Kết quả tìm kiếm
    public function searchDrugsWithDetails(string $query): array
    {
        // 1. Làm sạch từ khóa người dùng nhập
        $keyword = preg_replace('/[^a-z0-9\s\-]/i', '', strtolower(trim($query)));

        if ($keyword === '') return [];

        $searchTerm = '%' . $keyword . '%';

        // 2. Thực hiện truy vấn chi tiết
        $stmt = $this->pdo->prepare("
        SELECT DISTINCT d.ten_thuoc, a.ten_hoat_chat, a.ham_luong, d.url
        FROM drugs d
        JOIN active_ingredients a ON d.id = a.drug_id
        WHERE LOWER(d.ten_thuoc) LIKE ? OR LOWER(a.ten_hoat_chat) LIKE ?
        LIMIT 50
    ");
        $stmt->execute([$searchTerm, $searchTerm]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lưu lịch sử người dùng
    public function saveUserHistory(array $data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO user_history (user_id, symptoms, predicted_disease, description, medications, diet, workouts, precautions)
                                            VALUES (:user_id, :symptoms, :disease, :description, :medications, :diet, :workouts, :precautions)");

        $stmt->execute([
            'user_id' => $data['user_id'],
            'symptoms' => $data['symptoms'],
            'disease' => $data['disease'],
            'description' => $data['description'],
            'medications' => $data['medications'],
            'diet' => $data['diet'],
            'workouts' => $data['workouts'],
            'precautions' => $data['precautions'],
        ]);
    }

    //Hiển thị lịch sử tư vấn
    public function getUserHistoryById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user_history WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Hiển thị chi tiết lịch sử người dùng
    public function getUserHistory($userId, $search = '')
    {
        $sql = "
        SELECT uh.*,
               EXISTS (
                   SELECT 1 FROM feedback f WHERE f.history_id = uh.id
               ) AS has_feedback
        FROM user_history uh
        WHERE uh.user_id = :userId
    ";

        if (!empty($search)) {
            $sql .= " AND (uh.symptoms LIKE :search OR uh.	predicted_disease LIKE :search)";
        }

        $sql .= " ORDER BY uh.created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //Lưu lịch sử tìm kiếm
    public function saveSearchHistory($userId, $keyword)
    {
        $stmt = $this->pdo->prepare("INSERT INTO search_history (user_id, keyword) VALUES (?, ?)");
        $stmt->execute([$userId, $keyword]);
    }

    //Hiển thị lịch sử tìm kiếm
    public function getUserSearchHistory($userId, $search = '')
    {
        $sql = "SELECT keyword, created_at 
            FROM search_history 
            WHERE user_id = :userId";

        if (!empty($search)) {
            $sql .= " AND keyword LIKE :search";
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':userId', $userId, PDO::PARAM_INT);

        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Hiển thị hồ sơ
    public function getUserById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Cập nhập hồ sơ
    public function updateUserProfile($id, $name, $email, $dob, $gender, $avatar)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET name = ?, email = ?, dob = ?, gender = ?, avatar = ? WHERE id = ?");
        $stmt->execute([$name, $email, $dob, $gender, $avatar, $id]);
    }

    //Đổi mật khẩu
    public function updatePassword($userId, $hashedPassword)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $userId
        ]);
    }

    //Mã xác nhận
    public function findByUsernameAndEmail($username, $email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ? AND email = ?");
        $stmt->execute([$username, $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function changePassword($userId, $newHashedPassword)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        return $stmt->execute([$newHashedPassword, $userId]);
    }

    //Feedbacks
    public function saveFeedback($userId, $email, $message, $historyId)
    {
        $stmt = $this->pdo->prepare("INSERT INTO feedback (user_id, email, message, history_id, created_at, is_read)
                           VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmt->execute([$userId, $email, $message, $historyId]);
    }
    public function getFeedbackByHistoryId($historyId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM feedback WHERE history_id = ?");
        $stmt->execute([$historyId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : null;
    }

    public function getAdminReplyByFeedbackId($feedbackId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM feedback_replies WHERE feedback_id = ?");
        $stmt->execute([$feedbackId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }
}
