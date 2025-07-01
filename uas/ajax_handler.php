<?php
// ========================================================================
// FILE: ajax_handler.php
// DILENGKAPI DENGAN LOGIKA UNTUK WORKOUT
// ========================================================================
?>
<?php
header('Content-Type: application/json');
require_once 'classes/User.php';
require_once 'config/database.php';
require_once 'classes/Member.php';
require_once 'classes/Workout.php';

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', '/opt/lampp/htdocs/cekicrot/web2/uas/php_errors.log');

if (!User::isLoggedIn()) {
    echo json_encode(['status' => 'error', 'message' => 'Akses ditolak. Sesi tidak valid.']);
    exit;
}

$database = new Database();
$db = $database->getConnection();
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$member = new Member($db);
$workout = new Workout($db);

switch ($action) {
    // Member actions
    case 'get_member':
        $data = $member->readOne($_GET['id']);
        echo json_encode($data ? ['status' => 'success', 'data' => $data] : ['status' => 'error', 'message' => 'Member tidak ditemukan.']);
        break;
    case 'add_member':
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = 'public/uploads/';
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0775, true);
            }
            // Ensure directory is writable
            if (!is_writable($upload_dir)) {
                chmod($upload_dir, 0775);
            }
            $photo = time() . '_' . basename($_FILES['photo']['name']);
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo)) {
                $error = error_get_last();
                error_log("Upload failed: " . ($error ? $error['message'] : 'Unknown error'));
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah foto: ' . ($error ? $error['message'] : 'Unknown error. Periksa izin public/uploads/ atau log di php_errors.log.')]);
                exit;
            }
        }
        // Pass the photo filename to the existing create method
        $result = $member->create($_POST['id'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['membership'], $_POST['join_date'], $_POST['status'], $photo);
        echo json_encode($result);
        break;
    case 'update_member':
        $photo = '';
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = 'public/uploads/';
            // Create directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0775, true);
            }
            // Ensure directory is writable
            if (!is_writable($upload_dir)) {
                chmod($upload_dir, 0775);
            }
            $photo = time() . '_' . basename($_FILES['photo']['name']);
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo)) {
                $error = error_get_last();
                error_log("Upload failed: " . ($error ? $error['message'] : 'Unknown error'));
                echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah foto: ' . ($error ? $error['message'] : 'Unknown error. Periksa izin public/uploads/ atau log di php_errors.log.')]);
                exit;
            }
        } else {
            // Only retain existing photo if no new upload
            $stmt = $db->prepare("SELECT photo FROM members WHERE id = ?");
            $stmt->bind_param("s", $_POST['id']);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            $photo = $result['photo'] ?? '';
        }
        // Pass the photo filename to the existing update method
        $result = $member->update($_POST['id'], $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['membership'], $_POST['join_date'], $_POST['status'], $photo);
        echo json_encode($result);
        break;
    case 'delete_member':
        $result = $member->delete($_POST['id']);
        echo json_encode($result);
        break;

    // Workout actions
    case 'get_workout':
        $data = $workout->readOne($_GET['id']);
        echo json_encode($data ? ['status' => 'success', 'data' => $data] : ['status' => 'error', 'message' => 'Workout tidak ditemukan.']);
        break;
    case 'add_workout':
        $result = $workout->create($_POST['name'], $_POST['category'], $_POST['duration'], $_POST['difficulty'], $_POST['exercises'], $_POST['status']);
        echo json_encode($result);
        break;
    case 'update_workout':
        $result = $workout->update($_POST['id'], $_POST['name'], $_POST['category'], $_POST['duration'], $_POST['difficulty'], $_POST['exercises'], $_POST['status']);
        echo json_encode($result);
        break;
    case 'delete_workout':
        $result = $workout->delete($_POST['id']);
        echo json_encode($result);
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid.']);
        break;
}
?>