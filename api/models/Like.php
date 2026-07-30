<?php
class Like {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Toggle like — kalau sudah like, unlike. Kalau belum, like.
    public function toggle($postId, $userId) {
        // Cek apakah sudah di-like
        $stmt = $this->pdo->prepare(
            "SELECT id FROM likes WHERE post_id = ? AND user_id = ?"
        );
        $stmt->execute([$postId, $userId]);
        $existing = $stmt->fetch();

        if ($existing) {
            // Unlike
            $stmt = $this->pdo->prepare("DELETE FROM likes WHERE id = ?");
            $stmt->execute([$existing['id']]);
            $liked = false;
        } else {
            // Like
            $stmt = $this->pdo->prepare(
                "INSERT INTO likes (post_id, user_id) VALUES (?, ?)"
            );
            $stmt->execute([$postId, $userId]);
            $liked = true;
        }

        // Hitung total likes
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM likes WHERE post_id = ?");
        $stmt->execute([$postId]);
        $count = $stmt->fetch()['total'];

        return ['liked' => $liked, 'like_count' => (int)$count];
    }
}
