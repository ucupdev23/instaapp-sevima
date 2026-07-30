<?php
require_once __DIR__ . '/../models/Like.php';

class LikeController {
    private $like;

    public function __construct($pdo) {
        $this->like = new Like($pdo);
    }

    // POST /api/posts/{id}/like — toggle like/unlike
    public function toggle($postId) {
        $userId = requireAuth();

        $result = $this->like->toggle($postId, $userId);

        echo json_encode([
            'message' => $result['liked'] ? 'Post di-like' : 'Like dibatalkan',
            'liked' => $result['liked'],
            'like_count' => $result['like_count']
        ]);
    }
}
