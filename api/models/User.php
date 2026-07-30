<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    // Daftar user baru
    public function register($username, $email, $password) {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare(
            "INSERT INTO users (username, email, password) VALUES (?, ?, ?)"
        );
        $stmt->execute([$username, $email, $hash]);
        return $this->pdo->lastInsertId();
    }

    // Cari user berdasarkan email (untuk login)
    public function findByEmail($email) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    // Cari user berdasarkan username
    public function findByUsername($username) {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    // Ambil data user berdasarkan ID (tanpa password)
    public function findById($id) {
        $stmt = $this->pdo->prepare(
            "SELECT id, username, email, avatar, bio, created_at FROM users WHERE id = ?"
        );
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Update profil user
    public function updateProfile($id, $bio, $avatar = null) {
        if ($avatar) {
            $stmt = $this->pdo->prepare("UPDATE users SET bio = ?, avatar = ? WHERE id = ?");
            $stmt->execute([$bio, $avatar, $id]);
        } else {
            $stmt = $this->pdo->prepare("UPDATE users SET bio = ? WHERE id = ?");
            $stmt->execute([$bio, $id]);
        }
    }

    // Hitung jumlah post user
    public function getPostCount($userId) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM posts WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch()['total'];
    }
}
