<?php
require_once 'classes/User.php';
if (!User::isLoggedIn()) { header("Location: login.php"); exit; }

require_once 'config/database.php';
require_once 'classes/Member.php';

$database = new Database();
$db = $database->getConnection();
$member = new Member($db);

$keyword = $_GET['search'] ?? '';
$stmt = $member->read($keyword);
$num = $stmt->num_rows;

$page_title = "Member Management";
include 'templates/header.php';
?>
<div class="container mx-auto px-4 sm:px-8 max-w-7xl">
    <div class="py-8">
        <div class="flex flex-col sm:flex-row mb-4 sm:mb-0 justify-between w-full">
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Member Management</h2>
            <div class="text-end mt-4 sm:mt-0">
                <button id="add-member-btn" class="bg-gradient-to-r from-indigo-600 to-blue-500 px-4 py-2 rounded-lg text-white font-semibold tracking-wide hover:from-indigo-700 hover:to-blue-600 transition duration-300 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Member
                </button>
            </div>
        </div>
        
        <div class="my-4 p-4 bg-white rounded-2xl shadow-lg">
            <h3 class="text-lg font-semibold text-gray-800 mb-2 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                Search Members
            </h3>
            <form action="members.php" method="GET">
                <input class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200" type="text" placeholder="Search by name, email, or ID..." name="search" value="<?= htmlspecialchars($keyword) ?>">
            </form>
        </div>

        <div class="bg-white p-4 rounded-2xl shadow-lg overflow-x-auto">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Members List</h3>
            <p class="text-gray-600 mb-4">Total: <?= $num ?> members</p>
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Photo</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ID</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Contact</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Membership</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num > 0) { while ($row = $stmt->fetch_assoc()) { 
                        $random_photo = 'https://placehold.co/40x40/E2E8F0/A0AEC0?text=??'; // Default fallback
                        if (is_dir('public/uploads/')) {
                            $files = array_diff(scandir('public/uploads/'), ['..', '.']); // Remove . and .. from directory listing
                            $image_extensions = ['jpg', 'jpeg', 'png', 'gif']; // Supported image types
                            $image_files = array_filter($files, function($file) use ($image_extensions) {
                                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                return in_array($ext, $image_extensions);
                            });
                            if (!empty($image_files)) {
                                $random_photo = 'public/uploads/' . $image_files[array_rand($image_files)];
                            }
                        }
                    ?>
                        <tr id="member-<?= htmlspecialchars($row['id']) ?>" class="hover:bg-gray-50 transition duration-200">
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><div class="flex-shrink-0 w-10 h-10"><img class="w-full h-full rounded-full object-cover" src="<?= $row['photo'] ? 'public/uploads/' . $row['photo'] : $random_photo ?>" alt="Member Photo"/></div></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($row['id']) ?></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= htmlspecialchars($row['name']) ?></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><p class="text-gray-900 whitespace-no-wrap"><?= htmlspecialchars($row['email']) ?></p><p class="text-gray-600 whitespace-no-wrap"><?= htmlspecialchars($row['phone']) ?></p></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><span class="relative inline-block px-3 py-1 font-semibold text-purple-900 leading-tight"><span aria-hidden class="absolute inset-0 bg-purple-200 opacity-50 rounded-full"></span><span class="relative"><?= htmlspecialchars($row['membership']) ?></span></span></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm"><?= $row['status'] == 'active' ? '<span class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight"><span aria-hidden class="absolute inset-0 bg-green-200 opacity-50 rounded-full"></span><span class="relative">Active</span></span>' : '<span class="relative inline-block px-3 py-1 font-semibold text-red-900 leading-tight"><span aria-hidden class="absolute inset-0 bg-red-200 opacity-50 rounded-full"></span><span class="relative">Inactive</span></span>' ?></td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm flex space-x-2">
                                <button onclick="editMember('<?= htmlspecialchars($row['id']) ?>')" class="text-indigo-600 hover:text-indigo-900 transition duration-200 flex items-center px-2 py-1 rounded">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11 14.828 6.828 19H2"></path></svg>
                                    Edit
                                </button>
                                <button onclick="deleteMember('<?= htmlspecialchars($row['id']) ?>')" class="text-red-600 hover:text-red-900 ml-2 transition duration-200 flex items-center px-2 py-1 rounded">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php }} else { ?>
                        <tr><td colspan="8" class="text-center py-10 text-gray-500">No members found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Member Modal -->
<div id="member-modal" class="fixed z-10 inset-0 overflow-y-auto hidden">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true"></span>
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form id="member-form" enctype="multipart/form-data">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-member">Add New Member</h3>
                    <input type="hidden" name="action" id="form-action-member">
                    <input type="hidden" name="id" id="member-id">
                    <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                        <div><label for="id-input" class="block text-sm font-medium text-gray-700">Member ID (NIM)</label><input type="text" name="id" id="id-input" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="name" class="block text-sm font-medium text-gray-700">Full Name</label><input type="text" name="name" id="name" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="email" class="block text-sm font-medium text-gray-700">Email</label><input type="email" name="email" id="email" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="phone" class="block text-sm font-medium text-gray-700">Phone</label><input type="tel" name="phone" id="phone" class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="membership" class="block text-sm font-medium text-gray-700">Membership</label><select id="membership" name="membership" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"><option>Basic</option><option>Standard</option><option>Premium</option></select></div>
                        <div><label for="join_date" class="block text-sm font-medium text-gray-700">Join Date</label><input type="date" name="join_date" id="join_date" required class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500"></div>
                        <div><label for="status" class="block text-sm font-medium text-gray-700">Status</label><select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
                        <div><label for="photo" class="block text-sm font-medium text-gray-700">Photo</label><input type="file" name="photo" id="photo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"></div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Save
                    </button>
                    <button type="button" id="cancel-btn-member" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const memberModal = document.getElementById('member-modal');
    const addMemberBtn = document.getElementById('add-member-btn');
    const cancelMemberBtn = document.getElementById('cancel-btn-member');
    const memberForm = document.getElementById('member-form');
    const memberModalTitle = document.getElementById('modal-title-member');
    const memberIdInput = document.getElementById('id-input');
    const photoInput = document.getElementById('photo');

    function openMemberModal() { memberModal.classList.remove('hidden'); }
    function closeMemberModal() { memberModal.classList.add('hidden'); memberForm.reset(); }
    
    addMemberBtn.onclick = () => {
        memberModalTitle.textContent = 'Add New Member';
        memberForm['action'].value = 'add_member';
        memberIdInput.readOnly = false;
        openMemberModal();
    };
    cancelMemberBtn.onclick = closeMemberModal;

    async function editMember(id) {
        const response = await fetch(`ajax_handler.php?action=get_member&id=${id}`);
        const result = await response.json();
        if (result.status === 'success') {
            memberModalTitle.textContent = 'Edit Member';
            memberForm['action'].value = 'update_member';
            memberIdInput.readOnly = true;
            Object.keys(result.data).forEach(key => {
                const el = memberForm.elements[key];
                if (el) el.value = result.data[key] || '';
            });
            // Clear photo input for new upload
            photoInput.value = '';
            openMemberModal();
        } else {
            showNotification(result.message, 'error');
        }
    }

    async function deleteMember(id) {
        if (confirm('Apakah Anda yakin ingin menghapus member ini?')) {
            const formData = new FormData();
            formData.append('action', 'delete_member');
            formData.append('id', id);
            const response = await fetch('ajax_handler.php', { method: 'POST', body: formData });
            const result = await response.json();
            showNotification(result.message, result.status);
            if (result.status === 'success') document.getElementById(`member-${id}`).remove();
        }
    }
    
    memberForm.onsubmit = async (e) => {
        e.preventDefault();
        const formData = new FormData(memberForm);
        // Ensure photo is included only if a new file is selected
        if (photoInput.files.length > 0) {
            formData.set('photo', photoInput.files[0]); // Use set to replace any existing photo
        }
        const response = await fetch('ajax_handler.php', { method: 'POST', body: formData });
        const result = await response.json();
        showNotification(result.message, result.status);
        if (result.status === 'success') {
            closeMemberModal();
            setTimeout(() => location.reload(), 1000);
        }
    };
</script>

<?php include 'templates/footer.php'; ?>