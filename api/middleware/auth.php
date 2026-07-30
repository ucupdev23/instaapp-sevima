<?php
// Cek apakah user sudah login via session
function requireAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Silakan login terlebih dahulu']);
        exit;
    }

    return $_SESSION['user_id'];
}

// Ambil user ID tanpa paksa (return null kalau belum login)
function getAuthUserId() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    return $_SESSION['user_id'] ?? null;
}
