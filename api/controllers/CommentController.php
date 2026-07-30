<?php
require_once __DIR__ . '/../models/Comment.php';

class CommentController {
    private $comment;

    public function __construct($pdo) {
        $this->comment = new Comment($pdo);
    }

    // GET /api/posts/{id}/comments — ambil semua komentar dari post
    public function index($postId) {
        $comments = $this->comment->getByPostId($postId);
        echo json_encode(['comments' => $comments]);
    }

    // POST /api/posts/{id}/comments — tambah komentar baru
    public function create($postId) {
        $userId = requireAuth();

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['content'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Komentar tidak boleh kosong']);
            return;
        }

        $commentId = $this->comment->create($postId, $userId, $data['content']);

        http_response_code(201);
        echo json_encode([
            'message' => 'Komentar berhasil ditambahkan',
            'comment_id' => $commentId
        ]);
    }

    // DELETE /api/comments/{id} — hapus komentar (hanya pemilik komentar)
    public function delete($id) {
        $userId = requireAuth();

        $comment = $this->comment->findById($id);
        if (!$comment) {
            http_response_code(404);
            echo json_encode(['error' => 'Komentar tidak ditemukan']);
            return;
        }

        // Cek hak akses — hanya pemilik komentar yang boleh hapus
        if ($comment['user_id'] != $userId) {
            http_response_code(403);
            echo json_encode(['error' => 'Anda tidak memiliki akses untuk menghapus komentar ini']);
            return;
        }

        $this->comment->delete($id);
        echo json_encode(['message' => 'Komentar berhasil dihapus']);
    }
}
