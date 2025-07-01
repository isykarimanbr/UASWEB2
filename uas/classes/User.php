<?php
// ========================================================================
// FILE: classes/User.php
// Class untuk mengelola data dan otentikasi pengguna.
// ========================================================================
?>
<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function register($username, $nama_lengkap, $email, $password) {
        if (empty($username) || empty($password) || empty($nama_lengkap) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Semua kolom harus diisi dengan benar.";
        }
        
        $query = "SELECT id FROM " . $this->table_name . " WHERE username = ? OR email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            return "Username atau Email sudah terdaftar.";
        }
        $stmt->close();

        $query = "INSERT INTO " . $this->table_name . " (username, password, nama_lengkap, email) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt->bind_param("ssss", $username, $password_hash, $nama_lengkap, $email);

        if ($stmt->execute()) {
            $stmt->close();
            return true;
        }
        $stmt->close();
        return false;
    }

    public function login($username, $password) {
        $query = "SELECT id, username, nama_lengkap, password FROM " . $this->table_name . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                if (session_status() === PHP_SESSION_NONE) session_start();
                $_SESSION['loggedin'] = true;
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
                return true;
            }
        }
        return false;
    }
    
    public static function isLoggedIn() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }

    public static function logout() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        session_unset();
        session_destroy();
    }
}
?>
