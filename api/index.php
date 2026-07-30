<?php
// API Router - mengarahkan request ke controller yang tepat
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/middleware/auth.php';

// Parse URL path
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/instaapp-sevima/api';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$path = trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Routing
switch (true) {
    // === AUTH ===
    case $path === 'auth/register' && $method === 'POST':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->register();
        break;

    case $path === 'auth/login' && $method === 'POST':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->login();
        break;

    case $path === 'auth/logout' && $method === 'POST':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->logout();
        break;

    case $path === 'auth/me' && $method === 'GET':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        $controller->me();
        break;

    // === POSTS ===
    case $path === 'posts' && $method === 'GET':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController($pdo);
        $controller->index();
        break;

    case $path === 'posts' && $method === 'POST':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController($pdo);
        $controller->create();
        break;

    case preg_match('/^posts\/(\d+)$/', $path, $m) && $method === 'GET':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController($pdo);
        $controller->show($m[1]);
        break;

    case preg_match('/^posts\/(\d+)$/', $path, $m) && $method === 'DELETE':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController($pdo);
        $controller->delete($m[1]);
        break;

    case preg_match('/^posts\/user\/(\d+)$/', $path, $m) && $method === 'GET':
        require_once __DIR__ . '/controllers/PostController.php';
        $controller = new PostController($pdo);
        $controller->getByUser($m[1]);
        break;

    // === LIKES ===
    case preg_match('/^posts\/(\d+)\/like$/', $path, $m) && $method === 'POST':
        require_once __DIR__ . '/controllers/LikeController.php';
        $controller = new LikeController($pdo);
        $controller->toggle($m[1]);
        break;

    // === COMMENTS ===
    case preg_match('/^posts\/(\d+)\/comments$/', $path, $m) && $method === 'GET':
        require_once __DIR__ . '/controllers/CommentController.php';
        $controller = new CommentController($pdo);
        $controller->index($m[1]);
        break;

    case preg_match('/^posts\/(\d+)\/comments$/', $path, $m) && $method === 'POST':
        require_once __DIR__ . '/controllers/CommentController.php';
        $controller = new CommentController($pdo);
        $controller->create($m[1]);
        break;

    case preg_match('/^comments\/(\d+)$/', $path, $m) && $method === 'DELETE':
        require_once __DIR__ . '/controllers/CommentController.php';
        $controller = new CommentController($pdo);
        $controller->delete($m[1]);
        break;

    // === USER PROFILE ===
    case preg_match('/^users\/(\d+)$/', $path, $m) && $method === 'GET':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController($pdo);
        // Ambil data user
        $user = (new User($pdo))->findById($m[1]);
        if ($user) {
            $user['post_count'] = (new User($pdo))->getPostCount($m[1]);
            echo json_encode(['user' => $user]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'User tidak ditemukan']);
        }
        break;

    // === 404 ===
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint tidak ditemukan', 'path' => $path]);
        break;
}
