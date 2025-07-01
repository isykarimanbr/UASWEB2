<?php
require_once 'config/database.php';
require_once 'classes/User.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    $user = new User($db);
    $result = $user->register($_POST['username'], $_POST['nama_lengkap'], $_POST['email'], $_POST['password']);

    if ($result === true) {
        $success_message = "Registrasi berhasil! Silakan <a href='login.php' class='font-bold underline'>login</a>.";
    } else {
        $error_message = $result;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FitTracker Pro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/heroicons@2.0.18/dist/heroicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1517836357463-d25dfeac3438?ixlib=rb-4.0.3&auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            animation: fadeIn 1s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .auth-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
        }
        .btn-primary {
            background: linear-gradient(to right, #10b981, #3b82f6);
            transition: background 0.3s ease, transform 0.2s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #059669, #2563eb);
            transform: scale(1.05);
        }
        .btn-primary svg {
            display: inline-block;
            vertical-align: middle;
            margin-right: 8px;
        }
    </style>
</head>
<body>
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="auth-card rounded-2xl shadow-2xl p-8 max-w-md w-full">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800 tracking-tight">FitTracker Pro</h1>
                <p class="text-gray-600 mt-2">Buat akun FitTracker baru</p>
            </div>
            
            <?php if(!empty($error_message)): ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-6 animate-fade-in" role="alert"><span><?= $error_message ?></span></div>
            <?php endif; ?>
            <?php if(!empty($success_message)): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 animate-fade-in" role="alert"><span><?= $success_message ?></span></div>
            <?php endif; ?>

            <form action="register.php" method="post">
                <div class="mb-5"><label for="username" class="block text-gray-700 text-sm font-semibold mb-2">Username</label><input type="text" id="username" name="username" placeholder="Masukkan username" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"></div>
                <div class="mb-5"><label for="nama_lengkap" class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label><input type="text" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"></div>
                <div class="mb-5"><label for="email" class="block text-gray-700 text-sm font-semibold mb-2">Email</label><input type="email" id="email" name="email" placeholder="Masukkan email" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"></div>
                <div class="mb-6"><label for="password" class="block text-gray-700 text-sm font-semibold mb-2">Password</label><input type="password" id="password" name="password" placeholder="Masukkan password" required class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"></div>
                <button type="submit" class="btn-primary w-full text-white font-semibold py-3 px-4 rounded-lg focus:outline-none transition duration-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Register
                </button>
                <p class="text-center text-gray-600 text-sm mt-4">Sudah punya akun? <a href="login.php" class="font-semibold text-blue-400 hover:text-blue-600 transition duration-200">Login disini</a></p>
            </form>
        </div>
    </div>
</body>
</html>