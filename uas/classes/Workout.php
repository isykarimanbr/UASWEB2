<?php
// ========================================================================
// FILE: classes/Workout.php
// Class untuk mengelola data workout (CRUD LENGKAP).
// ========================================================================
?>
<?php
class Workout {
    private $conn;
    private $table_name = "workouts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($keyword = '') {
        $query = "SELECT * FROM " . $this->table_name;
        if (!empty($keyword)) {
            $query .= " WHERE name LIKE ? OR category LIKE ? OR difficulty LIKE ?";
        }
        $query .= " ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($keyword)) {
            $searchTerm = "%{$keyword}%";
            $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
        }
        $stmt->execute();
        return $stmt->get_result();
    }
    
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    public function create($name, $category, $duration, $difficulty, $exercises, $status) {
        if (empty($name) || empty($category) || empty($duration)) {
             return ['status' => 'error', 'message' => 'Nama, Kategori, dan Durasi tidak boleh kosong.'];
        }
        $query = "INSERT INTO " . $this->table_name . " (name, category, duration, difficulty, exercises, status) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssisss", $name, $category, $duration, $difficulty, $exercises, $status);
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Workout plan berhasil ditambahkan.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal: ' . $stmt->error];
        }
    }
    
    public function update($id, $name, $category, $duration, $difficulty, $exercises, $status) {
        if (empty($name) || empty($category) || empty($duration)) {
             return ['status' => 'error', 'message' => 'Nama, Kategori, dan Durasi tidak boleh kosong.'];
        }
        $query = "UPDATE " . $this->table_name . " SET name=?, category=?, duration=?, difficulty=?, exercises=?, status=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssisssi", $name, $category, $duration, $difficulty, $exercises, $status, $id);
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Workout plan berhasil diperbarui.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal: ' . $stmt->error];
        }
    }
    
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Workout plan berhasil dihapus.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal menghapus workout plan.'];
        }
    }
}
?>
