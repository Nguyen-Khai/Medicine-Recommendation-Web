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

    public function logAction($adminId, $action)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO admin_logs (admin_id, action, ip_address, user_agent)
                           VALUES (:admin_id, :action, :ip, :agent)");
        return $stmt->execute([
            ':admin_id' => $adminId,
            ':action' => $action,
            ':ip' => $_SERVER['REMOTE_ADDR'],
            ':agent' => $_SERVER['HTTP_USER_AGENT']
        ]);
    }

    //Feedback
    public function getAllFeedbacks()
    {
        $stmt = $this->pdo->query("
        SELECT feedback.*, users.username 
        FROM feedback 
        LEFT JOIN users ON feedback.user_id = users.id 
        ORDER BY created_at DESC
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
    public function deleteDrugById($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM drugs WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getDrugById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM drugs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateDrug($id, $data)
    {
        try {
            // Bắt đầu transaction
            $this->pdo->beginTransaction();

            // Cập nhật bảng drugs
            $stmt = $this->pdo->prepare("
            UPDATE drugs 
            SET ten_thuoc = ?, dang_bao_che = ?, so_dang_ky = ?, quy_cach = ?, han_su_dung = ?, url = ?
            WHERE id = ?
        ");
            $stmt->execute([
                $data['ten_thuoc'],
                $data['dang_bao_che'],
                $data['so_dang_ky'],
                $data['quy_cach'],
                $data['han_su_dung'],
                $data['url'],
                $id
            ]);

            // Xóa hoạt chất cũ
            $deleteStmt = $this->pdo->prepare("DELETE FROM active_ingredients WHERE drug_id = ?");
            $deleteStmt->execute([$id]);

            // Thêm lại các hoạt chất mới
            if (!empty($data['hoat_chat']) && is_array($data['hoat_chat'])) {
                $insertStmt = $this->pdo->prepare("
                INSERT INTO active_ingredients (ten_hoat_chat, ham_luong, drug_id)
                VALUES (?, ?, ?)
            ");

                foreach ($data['hoat_chat'] as $hoatChat) {
                    if (!empty($hoatChat['ten_hoat_chat']) && !empty($hoatChat['ham_luong'])) {
                        $insertStmt->execute([
                            $hoatChat['ten_hoat_chat'],
                            $hoatChat['ham_luong'],
                            $id
                        ]);
                    }
                }
            }

            // Hoàn tất transaction
            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            // Hủy transaction nếu có lỗi
            $this->pdo->rollBack();
            error_log('Update drug failed: ' . $e->getMessage());
            return false;
        }
    }

    // Xử lí cập nhật thuốc
    public function getIngredientsByDrugId($drugId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM active_ingredients WHERE drug_id = ?");
        $stmt->execute([$drugId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Diseases
    public function getAllDiseases($keyword = '', $limit = 10, $offset = 0)
    {
        $sql = "SELECT * FROM diseases WHERE disease LIKE :keyword ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':keyword', "%$keyword%", PDO::PARAM_STR);
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
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM diseases WHERE disease LIKE :keyword");
        $stmt->execute([':keyword' => "%$keyword%"]);
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
}
