<?php
require_once __DIR__ . '/../models/Post.php';

class PostController {
    private $post;
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->post = new Post($pdo);
    }

    // GET /api/posts — ambil semua post (feed)
    public function index() {
        $userId = getAuthUserId();
        $posts = $this->post->getAll($userId);

        // Tambahkan URL lengkap untuk image
        foreach ($posts as &$post) {
            $post['image_url'] = '/instaapp-sevima/public/uploads/' . $post['image_path'];
            $post['is_liked'] = isset($post['is_liked']) ? (bool)$post['is_liked'] : false;
        }

        echo json_encode(['posts' => $posts]);
    }

    // GET /api/posts/{id} — ambil satu post
    public function show($id) {
        $userId = getAuthUserId();
        $post = $this->post->findById($id, $userId);

        if (!$post) {
            http_response_code(404);
            echo json_encode(['error' => 'Post tidak ditemukan']);
            return;
        }

        $post['image_url'] = '/instaapp-sevima/public/uploads/' . $post['image_path'];
        $post['is_liked'] = isset($post['is_liked']) ? (bool)$post['is_liked'] : false;

        echo json_encode(['post' => $post]);
    }

    // POST /api/posts — buat post baru (dengan upload gambar)
    public function create() {
        $userId = requireAuth();

        // Validasi: harus ada file gambar
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            http_response_code(400);
            echo json_encode(['error' => 'Gambar wajib diupload']);
            return;
        }

        $file = $_FILES['image'];
        $caption = $_POST['caption'] ?? '';

        // Validasi tipe file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            http_response_code(400);
            echo json_encode(['error' => 'Format gambar harus JPEG, PNG, GIF, atau WebP']);
            return;
        }

        // Validasi ukuran (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            http_response_code(400);
            echo json_encode(['error' => 'Ukuran gambar maksimal 5MB']);
            return;
        }

        // Generate nama file unik
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'post_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;

        // Pindahkan file ke folder uploads
        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
            http_response_code(500);
            echo json_encode(['error' => 'Gagal menyimpan gambar']);
            return;
        }

        // Simpan ke database
        $postId = $this->post->create($userId, $filename, $caption);

        http_response_code(201);
        echo json_encode([
            'message' => 'Post berhasil dibuat',
            'post_id' => $postId
        ]);
    }

    // DELETE /api/posts/{id} — hapus post (hanya pemilik)
    public function delete($id) {
        $userId = requireAuth();

        $post = $this->post->findById($id);
        if (!$post) {
            http_response_code(404);
            echo json_encode(['error' => 'Post tidak ditemukan']);
            return;
        }

        // Cek hak akses — hanya pemilik yang boleh hapus
        if ($post['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'Anda tidak memiliki akses untuk menghapus post ini']);
            return;
        }

        // Hapus file gambar
        $imagePath = __DIR__ . '/../../public/uploads/' . $post['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $this->post->delete($id);
        echo json_encode(['message' => 'Post berhasil dihapus']);
    }

    // GET /api/posts/user/{userId} — ambil post milik user tertentu
    public function getByUser($userId) {
        $posts = $this->post->getByUserId($userId);

        foreach ($posts as &$post) {
            $post['image_url'] = '/instaapp-sevima/public/uploads/' . $post['image_path'];
        }

        echo json_encode(['posts' => $posts]);
    }
}
