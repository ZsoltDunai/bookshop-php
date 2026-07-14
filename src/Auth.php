<?php

declare(strict_types=1);

class Auth
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function user(): ?array
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $stmt = $this->db->prepare('SELECT id, email, created_at FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function requireLogin(): array
    {
        $user = $this->user();
        if (!$user) {
            flash('error', 'Please log in to continue.');
            redirect('/login');
        }

        return $user;
    }

    public function register(string $email, string $password): array
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['ok' => false, 'error' => 'Please enter a valid email address.'];
        }

        if (strlen($password) < 6) {
            return ['ok' => false, 'error' => 'Password must be at least 6 characters.'];
        }

        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['ok' => false, 'error' => 'An account with this email already exists.'];
        }

        $stmt = $this->db->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);

        $_SESSION['user_id'] = (int) $this->db->lastInsertId();

        return ['ok' => true];
    }

    public function login(string $email, string $password): array
    {
        $email = trim(strtolower($email));

        $stmt = $this->db->prepare('SELECT id, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return ['ok' => false, 'error' => 'Invalid email or password.'];
        }

        $_SESSION['user_id'] = (int) $user['id'];

        return ['ok' => true];
    }

    public function logout(): void
    {
        unset($_SESSION['user_id']);
    }
}
