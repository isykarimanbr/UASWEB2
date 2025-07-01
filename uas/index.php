<?php
require_once 'classes/User.php';
if (!User::isLoggedIn()) {
    header("Location: login.php");
    exit;
}

require_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$stmt_members = $db->query("SELECT COUNT(*) as count FROM members");
$total_members = $stmt_members->fetch_assoc()['count'];
$stmt_workouts = $db->query("SELECT COUNT(*) as count FROM workouts WHERE status = 'Active'");
$active_workouts = $stmt_workouts->fetch_assoc()['count'];

$sessions_today = 23; // Data statis
$monthly_growth = 18; // Data statis

$page_title = "Overview";
include 'templates/header.php';
?>
<div class="container mx-auto px-4 sm:px-8 max-w-7xl">
    <div class="py-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Dashboard</h2>
            <p class="text-gray-500 mt-2">Monitor your fitness center's performance</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
            <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition duration-300 flex items-center">
                <svg class="w-10 h-10 text-indigo-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v2h5m-2-2a3 3 0 005.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <div>
                    <h3 class="text-gray-600 text-sm font-semibold">Total Members</h3>
                    <p class="text-4xl font-bold text-gray-800 mt-2"><?= $total_members ?></p>
                    <p class="text-green-500 text-sm mt-1">+12% from last month</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition duration-300 flex items-center">
                <svg class="w-10 h-10 text-indigo-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <div>
                    <h3 class="text-gray-600 text-sm font-semibold">Active Workouts</h3>
                    <p class="text-4xl font-bold text-gray-800 mt-2"><?= $active_workouts ?></p>
                    <p class="text-green-500 text-sm mt-1">+8% from last month</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition duration-300 flex items-center">
                <svg class="w-10 h-10 text-indigo-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <h3 class="text-gray-600 text-sm font-semibold">Sessions Today</h3>
                    <p class="text-4xl font-bold text-gray-800 mt-2"><?= $sessions_today ?></p>
                    <p class="text-green-500 text-sm mt-1">+15% from last month</p>
                </div>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg transform hover:scale-105 transition duration-300 flex items-center">
                <svg class="w-10 h-10 text-indigo-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                <div>
                    <h3 class="text-gray-600 text-sm font-semibold">Monthly Growth</h3>
                    <p class="text-4xl font-bold text-gray-800 mt-2"><?= $monthly_growth ?>%</p>
                    <p class="text-green-500 text-sm mt-1">+3% from last month</p>
                </div>
            </div>
        </div>
        <div class="mt-8 bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800">Quick Actions</h3>
            <p class="text-gray-500 mb-6">Common tasks you might want to perform</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="members.php" class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white text-center font-bold py-3 px-4 rounded-lg hover:from-indigo-700 hover:to-blue-600 transition duration-300 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Manage Members
                </a>
                <a href="workouts.php" class="bg-gradient-to-r from-indigo-600 to-blue-500 text-white text-center font-bold py-3 px-4 rounded-lg hover:from-indigo-700 hover:to-blue-600 transition duration-300 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Manage Workouts
                </a>
                <a href="reports.php" class="bg-gray-200 text-gray-800 text-center font-bold py-3 px-4 rounded-lg hover:bg-gray-300 transition duration-300 flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Generate Report
                </a>
            </div>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>