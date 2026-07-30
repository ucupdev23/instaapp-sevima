<?php
class Comment {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ambil semua komentar dari suatu post
    public function getByPostId($postId) {
        $stmt = $this->pdo->prepare(
            "SELECT c.*, u.username, u.avatar 
             FROM comments c 
             JOIN users u ON c.user_id = u.id 
             WHERE c.post_id = ? 
             ORDER BY c.created_at ASC"
        );
        $stmt->execute([$postId]);
        return $stmt->fetchAll();
    }

    // Tambah komentar baru
    public function create($postId, $userId, $content) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)"
        );
        $stmt->execute([$postId, $userId, $content]);
        return $this->pdo->lastInsertId();
    }

    // Cari komentar berdasarkan ID
    public function findById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM comments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Hapus komentar
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM comments WHERE id = ?");
        $stmt->execute([$id]);
    }
}
