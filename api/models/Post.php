<?php
class Post {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Ambil semua post (untuk feed) — dengan info user, like count, dan status liked
    public function getAll($currentUserId = null) {
        $sql = "SELECT p.*, u.username, u.avatar,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count";
        
        if ($currentUserId) {
            $sql .= ", (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as is_liked";
        }

        $sql .= " FROM posts p 
                  JOIN users u ON p.user_id = u.id 
                  ORDER BY p.created_at DESC";

        if ($currentUserId) {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$currentUserId]);
        } else {
            $stmt = $this->pdo->query($sql);
        }

        return $stmt->fetchAll();
    }

    // Ambil satu post berdasarkan ID
    public function findById($id, $currentUserId = null) {
        $sql = "SELECT p.*, u.username, u.avatar,
                (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count";
        
        if ($currentUserId) {
            $sql .= ", (SELECT COUNT(*) FROM likes WHERE post_id = p.id AND user_id = ?) as is_liked";
        }

        $sql .= " FROM posts p JOIN users u ON p.user_id = u.id WHERE p.id = ?";

        $stmt = $this->pdo->prepare($sql);
        if ($currentUserId) {
            $stmt->execute([$currentUserId, $id]);
        } else {
            $stmt->execute([$id]);
        }

        return $stmt->fetch();
    }

    // Ambil post milik user tertentu
    public function getByUserId($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, 
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as like_count,
                    (SELECT COUNT(*) FROM comments WHERE post_id = p.id) as comment_count
             FROM posts p WHERE p.user_id = ? ORDER BY p.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    // Buat post baru
    public function create($userId, $imagePath, $caption) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO posts (user_id, image_path, caption) VALUES (?, ?, ?)"
        );
        $stmt->execute([$userId, $imagePath, $caption]);
        return $this->pdo->lastInsertId();
    }

    // Hapus post (cek kepemilikan di controller)
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$id]);
    }
}
