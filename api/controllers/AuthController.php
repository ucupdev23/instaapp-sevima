<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;

    public function __construct($pdo) {
        $this->user = new User($pdo);
    }

    // POST /api/auth/register
    public function register() {
        $data = json_decode(file_get_contents('php://input'), true);

        // Validasi input
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Username, email, dan password wajib diisi']);
            return;
        }

        // Validasi email format
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Format email tidak valid']);
            return;
        }

        // Validasi panjang password
        if (strlen($data['password']) < 6) {
            http_response_code(400);
            echo json_encode(['error' => 'Password minimal 6 karakter']);
            return;
        }

        // Cek apakah email sudah terdaftar
        if ($this->user->findByEmail($data['email'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Email sudah terdaftar']);
            return;
        }

        // Cek apakah username sudah dipakai
        if ($this->user->findByUsername($data['username'])) {
            http_response_code(409);
            echo json_encode(['error' => 'Username sudah dipakai']);
            return;
        }

        try {
            $userId = $this->user->register(
                $data['username'],
                $data['email'],
                $data['password']
            );

            http_response_code(201);
            echo json_encode([
                'message' => 'Registrasi berhasil',
                'user_id' => $userId
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal registrasi: ' . $e->getMessage()]);
        }
    }

    // POST /api/auth/login
    public function login() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email dan password wajib diisi']);
            return;
        }

        $user = $this->user->findByEmail($data['email']);

        if (!$user || !password_verify($data['password'], $user['password'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Email atau password salah']);
            return;
        }

        // Simpan session
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        echo json_encode([
            'message' => 'Login berhasil',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'avatar' => $user['avatar']
            ]
        ]);
    }

    // POST /api/auth/logout
    public function logout() {
        session_start();
        session_destroy();
        echo json_encode(['message' => 'Logout berhasil']);
    }

    // GET /api/auth/me — ambil data user yang sedang login
    public function me() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Belum login']);
            return;
        }

        $user = $this->user->findById($_SESSION['user_id']);
        if (!$user) {
            http_response_code(404);
            echo json_encode(['error' => 'User tidak ditemukan']);
            return;
        }

        echo json_encode(['user' => $user]);
    }
}
