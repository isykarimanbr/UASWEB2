<?php
require_once 'classes/User.php';
if (!User::isLoggedIn()) { header("Location: login.php"); exit; }

require_once 'config/database.php';
require_once 'classes/Workout.php';

$database = new Database();
$db = $database->getConnection();
$workout = new Workout($db);

$keyword = $_GET['search'] ?? '';
$stmt = $workout->read($keyword);
$num = $stmt->num_rows;

$page_title = "Workout Plans Management";
include 'templates/header.php';
?>
<div class="container mx-auto px-4 sm:px-8 max-w-7xl">
    <div class="py-8">
        <div class="flex flex-col sm:flex-row mb-4 sm:mb-0 justify-between w-full">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Workout Plans</h2>
            <div class="text-end mt-4 sm:mt-0">
                <button id="add-workout-btn" class="bg-gradient-to-r from-indigo-600 to-blue-500 px-4 py-2 rounded-lg text-white font-semibold tracking-wide hover:from-indigo-700 hover:to-blue-600 transition duration-300">+ Add Workout Plan</button>
            </div>
        </div>
        
        <div class="my-4 p-4 bg-white rounded-2xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Search Workout Plans</h3>
            <form action="workouts.php" method="GET">
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200" type="text" placeholder="Search by name, category, or difficulty..." name="search" value="<?= htmlspecialchars($keyword) ?>">
            </form>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-lg overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Workout Plans List</h3>
            <p class="text-gray-600 mb-4">Total Plans: <?= $num ?></p>
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Duration</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Difficulty</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num > 0) { while ($row = $stmt->fetch_assoc()) { ?>
                        <tr id="workout-<?= htmlspecialchars($row['id']) ?>" class="hover:bg-gray-50 transition duration-200">
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap font-semibold"><?= htmlspecialchars($row['name']) ?></p><p class="text-gray-600 whitespace-no-wrap text-xs"><?= htmlspecialchars($row['exercises']) ?></p></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($row['category']) ?></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($row['duration']) ?> min</td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><span class="relative inline-block px-3 py-1 font-semibold text-yellow-900 leading-tight"><span aria-hidden class="absolute inset-0 bg-yellow-200 opacity-50 rounded-full"></span><span class="relative"><?= htmlspecialchars($row['difficulty']) ?></span></span></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= $row['status'] == 'Active' ? '<span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"><span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span><span class="relative">Active</span></span>' : '<span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight"><span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span><span class="relative">Inactive</span></span>' ?></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><button onclick="editWorkout('<?= htmlspecialchars($row['id']) ?>')" class="text-indigo-600 hover:text-indigo-900 transition duration-200">Edit</button><button onclick="deleteWorkout('<?= htmlspecialchars($row['id']) ?>')" class="text-red-600 hover:text-red-900 ml-2 transition duration-200">Delete</button></td>
                        </tr>
                    <?php }} else { ?>
                        <tr><td colspan="6" class="text-center py-10 text-gray-500">No workout plans found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Workout Modal -->
<div id="workout-modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="workout-form">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-workout">Add New Workout Plan</h3>
                    <input type="hidden" name="action" id="form-action-workout">
                    <input type="hidden" name="id" id="workout-id">
                    <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                        <div class="sm:col-span-2"><label for="workout-name" class="block text-sm font-medium text-gray-700">Plan Name</label><input type="text" name="name" id="workout-name" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="workout-category" class="block text-sm font-medium text-gray-700">Category</label><input type="text" name="category" id="workout-category" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="workout-duration" class="block text-sm font-medium text-gray-700">Duration (minutes)</label><input type="number" name="duration" id="workout-duration" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="workout-difficulty" class="block text-sm font-medium text-gray-700">Difficulty</label><select id="workout-difficulty" name="difficulty" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"><option>Beginner</option><option>Intermediate</option><option>Advanced</option></select></div>
                        <div><label for="workout-status" class="block text-sm font-medium text-gray-700">Status</label><select id="workout-status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"><option>Active</option><option>Inactive</option></select></div>
                        <div class="sm:col-span-2"><label for="workout-exercises" class="block text-sm font-medium text-gray-700">Exercises (comma separated)</label><textarea name="exercises" id="workout-exercises" rows="3" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></textarea></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                    <button type="button" id="cancel-btn-workout" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const workoutModal = document.getElementById('workout-modal');
    const addWorkoutBtn = document.getElementById('add-workout-btn');
    const cancelWorkoutBtn = document.getElementById('cancel-btn-workout');
    const workoutForm = document.getElementById('workout-form');
    const workoutModalTitle = document.getElementById('modal-title-workout');

    function openWorkoutModal() { workoutModal.classList.remove('hidden'); }
    function closeWorkoutModal() { workoutModal.classList.add('hidden'); workoutForm.reset(); }
    
    addWorkoutBtn.onclick = () => {
        workoutModalTitle.textContent = 'Add New Workout Plan';
        workoutForm['action'].value = 'add_workout';
        openWorkoutModal();
    };
    cancelWorkoutBtn.onclick = closeWorkoutModal;

    async function editWorkout(id) {
        const response = await fetch(`ajax_handler.php?action=get_workout&id=${id}`);
        const result = await response.json();
        if (result.status === 'success') {
            workoutModalTitle.textContent = 'Edit Workout Plan';
            workoutForm['action'].value = 'update_workout';
            Object.keys(result.data).forEach(key => {
                const el = workoutForm.elements[key];
                if (el) el.value = result.data[key];
            });
            openWorkoutModal();
        }
    }

    async function deleteWorkout(id) {
        if (confirm('Apakah Anda yakin ingin menghapus workout plan ini?')) {
            const formData = new FormData();
            formData.append('action', 'delete_workout');
            formData.append('id', id);
            const response = await fetch('ajax_handler.php', { method: 'POST', body: formData });
            const result = await response.json();
            showNotification(result.message, result.status);
            if (result.status === 'success') document.getElementById(`workout-${id}`).remove();
        }
    }
    
    workoutForm.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(workoutForm);
        const response = await fetch('ajax_handler.php', { method: 'POST', body: formData });
        const result = await response.json();
        showNotification(result.message, result.status);
        if (result.status === 'success') {
            closeWorkoutModal();
            setTimeout(() => location.reload(), 1000);
        }
    };
</script>

<?php include 'templates/footer.php'; ?>