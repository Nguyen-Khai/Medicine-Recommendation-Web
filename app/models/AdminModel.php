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

    //Users
    public function getAllUsers()
    {
        $stmt = $this->pdo->prepare("SELECT id, username, email, created_at FROM users ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Diagnosis
    public function getFilteredDiagnosisHistory($userId = null, $keyword = null, $fromDate = null, $toDate = null, $limit = 10, $offset = 0)
    {
        $query = "SELECT uh.*, u.username FROM user_history uh 
              JOIN users u ON uh.user_id = u.id WHERE 1";
        $params = [];

        if ($userId) {
            $query .= " AND u.id = :user_id";
            $params[':user_id'] = $userId;
        }

        if ($keyword) {
            $query .= " AND (uh.symptoms LIKE :kw OR uh.disease_name LIKE :kw)";
            $params[':kw'] = '%' . $keyword . '%';
        }

        if ($fromDate) {
            $query .= " AND DATE(uh.created_at) >= :from_date";
            $params[':from_date'] = $fromDate;
        }

        if ($toDate) {
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

        if ($userId) {
            $query .= " AND u.id = :user_id";
            $params[':user_id'] = $userId;
        }

        if ($keyword) {
            $query .= " AND (uh.symptoms LIKE :kw OR uh.disease_name LIKE :kw)";
            $params[':kw'] = '%' . $keyword . '%';
        }

        if ($fromDate) {
            $query .= " AND DATE(uh.created_at) >= :from_date";
            $params[':from_date'] = $fromDate;
        }

        if ($toDate) {
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
}
