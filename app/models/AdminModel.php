<?php

class AdminModel
{
    private $pdo;

    public function __construct()
    {
        require_once __DIR__ . '/../../config/database.php'; // Lấy PDO
        global $pdo;
        $this->pdo = $pdo;
    }

    public function findByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //Ghi logs
    public function logAction($adminId, $action, $ipAddress = null, $userAgent = null)
    {
        $stmt = $this->pdo->prepare("INSERT INTO admin_logs (admin_id, action, ip_address, user_agent) VALUES (?, ?, ?, ?)");
        $stmt->execute([$adminId, $action, $ipAddress, $userAgent]);
    }

    //Dashboard
    public function getTotalUsers()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    }

    public function getDiagnosesToday()
    {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM user_history WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        return $stmt->fetchColumn();
    }

    public function getTopDisease()
    {
        $stmt = $this->pdo->query("
            SELECT predicted_disease, COUNT(*) as count 
            FROM user_history 
            GROUP BY predicted_disease
            ORDER BY count DESC 
            LIMIT 1
        ");
        $row = $stmt->fetch();
        return $row ? $row['predicted_disease'] : 'N/A';
    }

    public function getUnreadFeedbackCount()
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM feedback WHERE is_read = 0");
        return $stmt->fetchColumn();
    }

    public function getDiagnosesThisWeek()
    {
        $stmt = $this->pdo->query("
            SELECT DATE(created_at) AS day, COUNT(*) as total 
            FROM user_history 
            WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
            GROUP BY day
        ");
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[$row['day']] = $row['total'];
        }
        return $result;
    }

    //Permission
    public function getAllPermissions()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM permissions ORDER BY role, module");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updatePermission($id, $read, $write, $delete)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE permissions SET can_read = ?, can_write = ?, can_delete = ? WHERE id = ?");
        return $stmt->execute([$read, $write, $delete, $id]);
    }

    //Add admin
    public function createAdmin($username, $email, $hashedPassword, $role, $avatar)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO admins (username, email, password, role)
                           VALUES (:username, :email, :password, :role)");
        return $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':password' => $hashedPassword,
            ':role' => $role,
        ]);
    }

    //Thông tin các admin
    public function getAllAdmins()
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM admins ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdminById($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAdmin($id, $username, $email, $role)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE admins SET username = ?, email = ?, role = ? WHERE id = ?");
        $stmt->execute([$username, $email, $role, $id]);
    }

    public function deleteAdmin($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM admins WHERE id = ?");
        $stmt->execute([$id]);
    }

    public function searchAndFilter($search = '', $role = '')
    {
        $sql = "SELECT * FROM admins WHERE 1";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND username LIKE :search";
            $params['search'] = '%' . $search . '%';
        }

        if (!empty($role)) {
            $sql .= " AND role = :role";
            $params['role'] = $role;
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Feedback
    public function getAllFeedbacks()
    {
        $stmt = $this->pdo->query("
        SELECT DISTINCT feedback.id, feedback.message, feedback.created_at, feedback.is_read, users.username, users.email
        FROM feedback 
        LEFT JOIN users ON feedback.user_id = users.id 
        ORDER BY feedback.created_at DESC
    ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markFeedbackRead($id)
    {
        $stmt = $this->pdo->prepare("UPDATE feedback SET is_read = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function deleteFeedback($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM feedback WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getFilteredFeedbacks($search = '', $status = '')
    {
        $query = "SELECT * FROM feedback WHERE 1";

        $params = [];

        if (!empty($search)) {
            $query .= " AND (email LIKE :search OR user_id LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        if (!empty($status)) {
            if ($status === 'read') {
                $query .= " AND is_read = 1";
            } elseif ($status === 'unread') {
                $query .= " AND is_read = 0";
            } elseif ($status === 'replied') {
                $query .= " AND id IN (SELECT feedback_id FROM feedback_replies)";
            } elseif ($status === 'unreplied') {
                $query .= " AND id NOT IN (SELECT feedback_id FROM feedback_replies)";
            }
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    //Admin logs
    public function getLogsByAdminId($adminId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM admin_logs WHERE admin_id = :admin_id ORDER BY created_at DESC LIMIT 100");
        $stmt->execute(['admin_id' => $adminId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Users
    public function getAllUsers($keyword = '', $status = '')
    {
        global $pdo;

        $sql = "SELECT * FROM users WHERE 1=1"; // Bỏ lọc role
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (username LIKE :kw OR email LIKE :kw)";
            $params['kw'] = '%' . $keyword . '%';
        }

        if ($status === 'active') {
            $sql .= " AND is_active = 1";
        } elseif ($status === 'inactive') {
            $sql .= " AND is_active = 0";
        }

        $sql .= " ORDER BY created_at DESC";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->pdo->prepare("UPDATE users SET is_active = :status WHERE id = :id");
        $stmt->execute(['status' => $status, 'id' => $id]);
    }

    //Diagnosis
    public function getFilteredDiagnosisHistory($userId = null, $keyword = null, $fromDate = null, $toDate = null, $limit = 10, $offset = 0)
    {
        $query = "SELECT uh.*, u.username FROM user_history uh 
              JOIN users u ON uh.user_id = u.id WHERE 1";
        $params = [];

        if (!empty($userId)) {
            $query .= " AND u.id = :user_id";
            $params[':user_id'] = $userId;
        }

        if (!empty($keyword)) {
            // Làm sạch keyword: trim khoảng trắng và loại bỏ ký tự đặc biệt
            $cleanedKeyword = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $keyword));

            // So sánh không phân biệt hoa thường bằng LOWER
            $query .= " AND (LOWER(uh.symptoms) LIKE :kw OR LOWER(uh.predicted_disease) LIKE :kw)";
            $params[':kw'] = '%' . mb_strtolower($cleanedKeyword, 'UTF-8') . '%';
        }

        if (!empty($fromDate)) {
            $query .= " AND DATE(uh.created_at) >= :from_date";
            $params[':from_date'] = $fromDate;
        }

        if (!empty($toDate)) {
            $query .= " AND DATE(uh.created_at) <= :to_date";
            $params[':to_date'] = $toDate;
        }

        $query .= " ORDER BY uh.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($query);

        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function countFilteredDiagnosisHistory($userId = null, $keyword = null, $fromDate = null, $toDate = null)
    {
        $query = "SELECT COUNT(*) FROM user_history uh 
              JOIN users u ON uh.user_id = u.id WHERE 1";
        $params = [];

        if (!empty($userId)) {
            $query .= " AND u.id = :user_id";
            $params[':user_id'] = $userId;
        }

        if (!empty($keyword)) {
            // Làm sạch keyword: loại ký tự đặc biệt và chuyển về thường
            $cleanedKeyword = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $keyword));
            $query .= " AND (LOWER(uh.symptoms) LIKE :kw OR LOWER(uh.predicted_disease) LIKE :kw)";
            $params[':kw'] = '%' . mb_strtolower($cleanedKeyword, 'UTF-8') . '%';
        }

        if (!empty($fromDate)) {
            $query .= " AND DATE(uh.created_at) >= :from_date";
            $params[':from_date'] = $fromDate;
        }

        if (!empty($toDate)) {
            $query .= " AND DATE(uh.created_at) <= :to_date";
            $params[':to_date'] = $toDate;
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getAllUsersSimple()
    {
        $stmt = $this->pdo->query("SELECT id, username FROM users ORDER BY username ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thuốc
    public function getActiveIngredientsByDrugId($drugId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM active_ingredients WHERE drug_id = ?");
        $stmt->execute([$drugId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllDrugs($filters = [], $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM drugs WHERE 1=1";
        $params = [];

        if (!empty($filters['keyword'])) {
            // Chuẩn hóa keyword: loại bỏ ký tự đặc biệt, chuẩn hóa chữ thường
            $cleanedKeyword = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $filters['keyword']));
            $cleanedKeyword = mb_strtolower($cleanedKeyword, 'UTF-8');

            // Sử dụng LOWER trong SQL để tìm không phân biệt hoa thường
            $sql .= " AND (LOWER(ten_thuoc) LIKE :keyword OR LOWER(dang_bao_che) LIKE :keyword)";
            $params[':keyword'] = '%' . $cleanedKeyword . '%';
        }

        $sql .= " ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);

        // Bind các tham số
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        $drugs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Gắn thêm hoạt chất
        foreach ($drugs as &$drug) {
            $stmt2 = $this->pdo->prepare("SELECT ten_hoat_chat, ham_luong FROM active_ingredients WHERE drug_id = ?");
            $stmt2->execute([$drug['id']]);
            $drug['hoat_chat'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $drugs;
    }

    public function countDrugs($filters = [])
    {
        $sql = "SELECT COUNT(*) FROM drugs WHERE 1=1";
        $params = [];

        if (!empty($filters['keyword'])) {
            // Loại bỏ ký tự đặc biệt và chuẩn hóa chữ thường
            $cleanedKeyword = trim(preg_replace('/[^\p{L}\p{N}\s]/u', '', $filters['keyword']));
            $sql .= " AND (LOWER(ten_thuoc) LIKE :keyword OR LOWER(dang_bao_che) LIKE :keyword)";
            $params[':keyword'] = '%' . mb_strtolower($cleanedKeyword, 'UTF-8') . '%';
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn();
    }

    //Thêm sửa xóa thuốc
    public function getDrugById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM drugs WHERE id = ?");
        $stmt->execute([$id]);
        $drug = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$drug) return null;

        $stmt2 = $this->pdo->prepare("SELECT * FROM active_ingredients WHERE drug_id = ?");
        $stmt2->execute([$id]);
        $drug['hoat_chat'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $drug;
    }

    public function updateDrug($id, $tenThuoc, $dangBaoChe, $soDangKy, $quyCach, $hanSuDung, $url, $activeIngredients, $concentrations)
    {
        // Cập nhật thuốc
        $stmt = $this->pdo->prepare("UPDATE drugs SET ten_thuoc = ?, dang_bao_che = ?, so_dang_ky = ?, quy_cach = ?, han_su_dung = ?, url = ? WHERE id = ?");
        $stmt->execute([$tenThuoc, $dangBaoChe, $soDangKy, $quyCach, $hanSuDung, $url, $id]);

        // Xóa hoạt chất cũ
        $this->pdo->prepare("DELETE FROM active_ingredients WHERE drug_id = ?")->execute([$id]);

        // Thêm lại hoạt chất mới
        $stmt2 = $this->pdo->prepare("INSERT INTO active_ingredients (drug_id, ten_hoat_chat, ham_luong) VALUES (?, ?, ?)");
        foreach ($activeIngredients as $index => $ten_hoat_chat) {
            $stmt2->execute([$id, $ten_hoat_chat, $concentrations[$index]]);
        }
    }

    public function deleteDrugById($id)
    {
        try {
            // Xóa hoạt chất trước (foreign key constraint)
            $stmt1 = $this->pdo->prepare("DELETE FROM active_ingredients WHERE drug_id = ?");
            $stmt1->execute([$id]);

            // Xóa thuốc
            $stmt2 = $this->pdo->prepare("DELETE FROM drugs WHERE id = ?");
            return $stmt2->execute([$id]);
        } catch (PDOException $e) {
            error_log("Delete Drug Error: " . $e->getMessage());
            return false;
        }
    }

    // Thêm thuốc
    public function insertDrug($data)
    {
        $sql = "INSERT INTO drugs (ten_thuoc, dang_bao_che, so_dang_ky, quy_cach, han_su_dung, url)
                VALUES (:ten_thuoc, :dang_bao_che, :so_dang_ky, :quy_cach, :han_su_dung, :url)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':ten_thuoc' => $data['ten_thuoc'],
            ':dang_bao_che' => $data['dang_bao_che'],
            ':so_dang_ky' => $data['so_dang_ky'],
            ':quy_cach' => $data['quy_cach'],
            ':han_su_dung' => $data['han_su_dung'],
            ':url' => $data['url']
        ]);

        return $this->pdo->lastInsertId();
    }

    public function insertActiveIngredient($data)
    {
        $sql = "INSERT INTO active_ingredients (ten_hoat_chat, ham_luong, drug_id)
                VALUES (:ten_hoat_chat, :ham_luong, :drug_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':ten_hoat_chat' => $data['ten_hoat_chat'],
            ':ham_luong' => $data['ham_luong'],
            ':drug_id' => $data['drug_id']
        ]);
    }

    //Diseases
    private function sanitizeKeyword($keyword)
    {
        // 1. Xóa khoảng trắng dư thừa (tab, xuống dòng, ...)
        $keyword = trim(preg_replace('/\s+/', ' ', $keyword));

        // 2. Loại bỏ các ký tự đặc biệt gây lỗi truy vấn hoặc không cần thiết
        $keyword = preg_replace('/[^\p{L}\p{N}\s]/u', '', $keyword); // Chỉ giữ lại chữ, số và khoảng trắng

        return $keyword;
    }


    public function getAllDiseases($keyword = '', $limit = 10, $offset = 0)
    {
        $keyword = $this->sanitizeKeyword($keyword); // Làm sạch trước khi dùng
        $sql = "SELECT * FROM diseases WHERE disease LIKE :keyword ORDER BY id DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':keyword', '%' . $keyword . '%', PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $diseases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($diseases as &$disease) {
            $disease['symptoms'] = $this->getSymptomsByDisease($disease['id']);
            $disease['diets'] = $this->getDietsByDisease($disease['id']);
            $disease['medications'] = $this->getMedicationsByDisease($disease['id']);
            $disease['precautions'] = $this->getPrecautionsByDisease($disease['id']);
            $disease['workouts'] = $this->getWorkoutsByDisease($disease['id']);
        }

        return $diseases;
    }

    public function countDiseases($keyword = '')
    {
        $escapedKeyword = $this->sanitizeKeyword($keyword);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM diseases WHERE disease LIKE :keyword ESCAPE '\\\'");
        $stmt->execute([':keyword' => "%$escapedKeyword%"]);
        return $stmt->fetchColumn();
    }

    public function getSymptomsByDisease($diseaseId)
    {
        $stmt = $this->pdo->prepare("SELECT s.symptom, s.weight FROM symptoms s JOIN disease_symptoms ds ON s.id = ds.symptom_id WHERE ds.disease_id = ?");
        $stmt->execute([$diseaseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMedicationsByDisease($diseaseId)
    {
        $stmt = $this->pdo->prepare("SELECT medication FROM medications WHERE disease_id = ?");
        $stmt->execute([$diseaseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDietsByDisease($diseaseId)
    {
        $stmt = $this->pdo->prepare("SELECT diet FROM diets WHERE disease_id = ?");
        $stmt->execute([$diseaseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPrecautionsByDisease($diseaseId)
    {
        $stmt = $this->pdo->prepare("SELECT precaution FROM precautions WHERE disease_id = ?");
        $stmt->execute([$diseaseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWorkoutsByDisease($diseaseId)
    {
        $stmt = $this->pdo->prepare("SELECT workout FROM workouts WHERE disease_id = ?");
        $stmt->execute([$diseaseId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Thêm bệnh 
    public function getAllSymptoms()
    {
        $stmt = $this->pdo->prepare("SELECT * FROM symptoms ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function addDisease($name, $description)
    {
        $stmt = $this->pdo->prepare("INSERT INTO diseases (disease, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);
        return $this->pdo->lastInsertId();
    }
    public function addDiseaseSymptoms($diseaseId, $symptomNames)
    {
        $stmtSelect = $this->pdo->prepare("SELECT id FROM symptoms WHERE symptom = ?");
        $stmtInsert = $this->pdo->prepare("INSERT INTO symptoms (symptom, weight) VALUES (?, ?)");
        $stmtLink = $this->pdo->prepare("INSERT INTO disease_symptoms (disease_id, symptom_id) VALUES (?, ?)");

        foreach ($symptomNames as $name) {
            $name = trim($name);
            if ($name === '') continue;

            // Tìm xem triệu chứng đã có chưa
            $stmtSelect->execute([$name]);
            $symptom = $stmtSelect->fetch(PDO::FETCH_ASSOC);

            if ($symptom) {
                $symptomId = $symptom['id'];
            } else {
                // Nếu chưa có thì thêm mới với severity mặc định (vd: 5)
                $stmtInsert->execute([$name, 5]);
                $symptomId = $this->pdo->lastInsertId();
            }

            // Liên kết với disease
            $stmtLink->execute([$diseaseId, $symptomId]);
        }
    }

    public function addPrecautions($diseaseId, $precautionList)
    {
        $stmt = $this->pdo->prepare("INSERT INTO precautions (disease_id, precaution) VALUES (?, ?)");
        foreach ($precautionList as $tip) {
            $tip = trim($tip);
            if ($tip !== '') {
                $stmt->execute([$diseaseId, $tip]);
            }
        }
    }

    public function addMedications($diseaseId, $medList)
    {
        $stmt = $this->pdo->prepare("INSERT INTO medications (disease_id, medication) VALUES (?, ?)");
        foreach ($medList as $med) {
            $med = trim($med);
            if ($med !== '') {
                $stmt->execute([$diseaseId, $med]);
            }
        }
    }

    public function addDiets($diseaseId, $dietList)
    {
        $stmt = $this->pdo->prepare("INSERT INTO diets (disease_id, diet) VALUES (?, ?)");
        foreach ($dietList as $diet) {
            $diet = trim($diet);
            if ($diet !== '') {
                $stmt->execute([$diseaseId, $diet]);
            }
        }
    }

    public function addWorkouts($diseaseId, $workoutList)
    {
        $stmt = $this->pdo->prepare("INSERT INTO workouts (disease_id, workout) VALUES (?, ?)");
        foreach ($workoutList as $workout) {
            $workout = trim($workout);
            if ($workout !== '') {
                $stmt->execute([$diseaseId, $workout]);
            }
        }
    }

    //Sửa, xóa bệnh
    public function getDiseaseById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM diseases WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateDisease($id, $data)
    {
        $stmt = $this->pdo->prepare("UPDATE diseases SET disease = :disease, description = :description WHERE id = :id");
        $stmt->execute([
            'disease' => $data['disease'],
            'description' => $data['description'],
            'id' => $id
        ]);

        // Cập nhật symptoms
        $this->pdo->prepare("DELETE FROM disease_symptoms WHERE disease_id = ?")->execute([$id]);
        // Xử lý triệu chứng mới
        if (!empty($data['symptoms'])) {
            $symptomLines = array_filter(array_map('trim', explode("\n", $data['symptoms'])));
            $stmtInsertSymptom = $this->pdo->prepare("INSERT INTO symptoms (symptom) VALUES (?)");
            $stmtSelectSymptom = $this->pdo->prepare("SELECT id FROM symptoms WHERE symptom = ?");
            $stmtLink = $this->pdo->prepare("INSERT INTO disease_symptoms (disease_id, symptom_id) VALUES (?, ?)");

            foreach ($symptomLines as $symptomName) {
                if (strlen($symptomName) > 100 || empty($symptomName)) continue;

                // Tìm ID nếu đã có
                $stmtSelectSymptom->execute([$symptomName]);
                $symptom = $stmtSelectSymptom->fetch();

                if ($symptom) {
                    $symptom_id = $symptom['id'];
                } else {
                    $stmtInsertSymptom->execute([$symptomName]);
                    $symptom_id = $this->pdo->lastInsertId();
                }

                $stmtLink->execute([$id, $symptom_id]);
            }
        }

        // Cập nhật diets
        $this->pdo->prepare("DELETE FROM diets WHERE disease_id = ?")->execute([$id]);
        $stmt = $this->pdo->prepare("INSERT INTO diets (disease_id, diet) VALUES (?, ?)");
        foreach (explode("\n", trim($data['diets'])) as $diet) {
            $diet = trim($diet);
            if ($diet) $stmt->execute([$id, $diet]);
        }

        // Tương tự cho medications
        $this->pdo->prepare("DELETE FROM medications WHERE disease_id = ?")->execute([$id]);
        $stmt = $this->pdo->prepare("INSERT INTO medications (disease_id, medication) VALUES (?, ?)");
        foreach (explode("\n", trim($data['medications'])) as $med) {
            $med = trim($med);
            if ($med) $stmt->execute([$id, $med]);
        }

        // precautions
        $this->pdo->prepare("DELETE FROM precautions WHERE disease_id = ?")->execute([$id]);
        $stmt = $this->pdo->prepare("INSERT INTO precautions (disease_id, precaution) VALUES (?, ?)");
        foreach (explode("\n", trim($data['precautions'])) as $p) {
            $p = trim($p);
            if ($p) $stmt->execute([$id, $p]);
        }

        // workouts
        $this->pdo->prepare("DELETE FROM workouts WHERE disease_id = ?")->execute([$id]);
        $stmt = $this->pdo->prepare("INSERT INTO workouts (disease_id, workout) VALUES (?, ?)");
        foreach (explode("\n", trim($data['workouts'])) as $w) {
            $w = trim($w);
            if ($w) $stmt->execute([$id, $w]);
        }
    }

    public function deleteDisease($id)
    {
        // Xóa các mối quan hệ liên quan đến bệnh
        $relationTables = ['disease_symptoms', 'diets', 'medications', 'precautions', 'workouts'];
        foreach ($relationTables as $table) {
            $stmt = $this->pdo->prepare("DELETE FROM $table WHERE disease_id = :id");
            $stmt->execute(['id' => $id]);
        }

        // Cuối cùng, xóa bệnh khỏi bảng diseases
        $stmt = $this->pdo->prepare("DELETE FROM diseases WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    //Setting
    public function updateAdminInfo($id, $name, $email)
    {
        $stmt = $this->pdo->prepare("UPDATE admins SET name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $id]);
    }

    public function updatePassword($adminId, $hashedPassword)
    {
        $stmt = $this->pdo->prepare("UPDATE admins SET password = :password WHERE id = :id");
        return $stmt->execute([
            ':password' => $hashedPassword,
            ':id' => $adminId
        ]);
    }

    public function updateAvatar($id, $data, $mimeType)
    {
        $stmt = $this->pdo->prepare("UPDATE admins SET avatar = ?, avatar_type = ? WHERE id = ?");
        return $stmt->execute([$data, $mimeType, $id]);
    }


    public function setTheme($id, $theme)
    {
        $stmt = $this->pdo->prepare("UPDATE admins SET theme = ? WHERE id = ?");
        return $stmt->execute([$theme, $id]);
    }

    //Feedbacks
    public function getFeedbackById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM feedback WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function storeFeedbackReply($feedback_id, $message)
    {
        $stmt = $this->pdo->prepare("INSERT INTO feedback_replies (feedback_id, reply_message, replied_at) VALUES (?, ?, NOW())");
        $stmt->execute([$feedback_id, $message]);
    }
    public function hasReplied($feedbackId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM feedback_replies WHERE feedback_id = ?");
        $stmt->execute([$feedbackId]);
        return $stmt->fetchColumn() > 0;
    }
}
