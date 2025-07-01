<?php
// ========================================================================
// FILE: classes/Member.php
// Class untuk mengelola data member (CRUD LENGKAP).
// ========================================================================
?>
<?php
class Member {
    private $conn;
    private $table_name = "members";

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function read($keyword = '') {
        $query = "SELECT * FROM " . $this->table_name;
        if (!empty($keyword)) {
            $query .= " WHERE name LIKE ? OR email LIKE ? OR id LIKE ?";
        }
        $query .= " ORDER BY join_date DESC";

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
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($id, $name, $email, $phone, $membership, $join_date, $status, $photo) {
        // Validasi
        if (empty($id) || empty($name) || empty($email)) {
            return ['status' => 'error', 'message' => 'ID, Nama, dan Email tidak boleh kosong.'];
        }

        $photo_name = $this->handleUpload($photo);
        if ($photo_name === false) {
             return ['status' => 'error', 'message' => 'Upload file gagal. Pastikan formatnya benar (JPG, JPEG, PNG) dan ukurannya tidak melebihi 1MB.'];
        }

        $query = "INSERT INTO " . $this->table_name . " (id, name, email, phone, membership, join_date, status, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssss", $id, $name, $email, $phone, $membership, $join_date, $status, $photo_name);
        
        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Member berhasil ditambahkan.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal menambahkan member: ' . $stmt->error];
        }
    }

    public function update($id, $name, $email, $phone, $membership, $join_date, $status, $photo) {
        $member = $this->readOne($id);
        $current_photo = $member['photo'];

        $photo_name = $current_photo;
        if (!empty($photo['name'])) {
            $photo_name = $this->handleUpload($photo);
            if ($photo_name === false) {
                 return ['status' => 'error', 'message' => 'Upload file baru gagal.'];
            }
            // Hapus foto lama jika ada & upload baru berhasil
            if ($current_photo && file_exists("public/uploads/" . $current_photo)) {
                unlink("public/uploads/" . $current_photo);
            }
        }

        $query = "UPDATE " . $this->table_name . " SET name=?, email=?, phone=?, membership=?, join_date=?, status=?, photo=? WHERE id=?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssssss", $name, $email, $phone, $membership, $join_date, $status, $photo_name, $id);

        if ($stmt->execute()) {
            return ['status' => 'success', 'message' => 'Member berhasil diperbarui.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal memperbarui member: ' . $stmt->error];
        }
    }

    public function delete($id) {
        $member = $this->readOne($id);
        $photo_to_delete = $member['photo'] ?? null;

        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $id);
        
        if ($stmt->execute()) {
             // Hapus file foto jika ada
            if ($photo_to_delete && file_exists("public/uploads/" . $photo_to_delete)) {
                unlink("public/uploads/" . $photo_to_delete);
            }
            return ['status' => 'success', 'message' => 'Member berhasil dihapus.'];
        } else {
            return ['status' => 'error', 'message' => 'Gagal menghapus member.'];
        }
    }

    private function handleUpload($file) {
        if (empty($file['name'])) {
            return null; // Tidak ada file yang diupload
        }

        $target_dir = "public/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = uniqid() . '-' . basename($file["name"]);
        $target_file = $target_dir . $file_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Validasi
        if ($file["size"] > 1000000) return false; // Max 1MB
        if (!in_array($imageFileType, ['jpg', 'png', 'jpeg'])) return false;
        
        if (move_uploaded_file($file["tmp_name"], $target_file)) {
            return $file_name;
        } else {
            return false;
        }
    }
}
?>
