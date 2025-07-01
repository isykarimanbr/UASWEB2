<?php
require_once 'classes/User.php';
if (!User::isLoggedIn()) { header("Location: login.php"); exit; }
$page_title = "Reports";
include 'templates/header.php';
?>
<div class="container mx-auto px-4 sm:px-8 max-w-7xl">
    <div class="py-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Reports</h2>
            <p class="text-gray-500 mt-2">Generate comprehensive reports for your fitness center</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-6">
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-lg font-semibold text-gray-800">Report Configuration</h3>
                <p class="text-gray-500 mb-4">Configure your report settings and filters</p>
                <form action="generate_report.php" method="POST" target="_blank">
                    <div class="mb-4">
                        <label for="report_type" class="block text-sm font-medium text-gray-700">Report Type *</label>
                        <select id="report_type" name="report_type" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="all_members">All Members List</option>
                            <option value="active_members">Active Members Only</option>
                            <option value="all_workouts">All Workouts List</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Export Format</label>
                        <div class="mt-2">
                            <button type="submit" name="format" value="pdf" class="bg-gradient-to-r from-red-500 to-red-600 text-white font-bold py-2 px-4 rounded-lg hover:from-red-600 hover:to-red-700 transition duration-300">PDF Report</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="bg-white p-6 rounded-2xl shadow-lg flex flex-col items-center justify-center text-center">
                <svg class="w-24 h-24 text-gray-300 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V7a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-800">Report Preview</h3>
                <p class="text-gray-500">Select a report type to generate</p>
            </div>
        </div>
    </div>
</div>
<?php include 'templates/footer.php'; ?>