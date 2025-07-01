<?php
// ========================================================================
// FILE: config/database.php
// Berisi konfigurasi koneksi ke database.
// ========================================================================
?>
<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'uas_fitness_db';
    private $username = 'root';
    private $password = ''; // Kosongkan jika tidak ada password di XAMPP Anda
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);
            if ($this->conn->connect_error) {
                throw new Exception("Koneksi Gagal: " . $this->conn->connect_error);
            }
            $this->conn->set_charset("utf8mb4");
        } catch(Exception $e) {
            echo "Kesalahan Koneksi: " . $e->getMessage();
            exit();
        }
        return $this->conn;
    }
}
?>