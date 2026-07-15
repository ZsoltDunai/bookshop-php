<?php

declare(strict_types=1);

class Auth
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function findUserById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, email, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function register(string $email, string $password): array
    {
        $email = trim(strtolower($email));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'ok' => false,
                'error' => 'Please enter a valid email address.',
                'code' => 'validation',
            ];
        }

        if (strlen($password) < 6) {
            return [
                'ok' => false,
                'error' => 'Password must be at least 6 characters.',
                'code' => 'validation',
            ];
        }

        $stmt = $this->db->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return [
                'ok' => false,
                'error' => 'An account with this email already exists.',
                'code' => 'conflict',
            ];
        }

        $stmt = $this->db->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
        $userId = (int) $this->db->lastInsertId();

        return [
            'ok' => true,
            'user' => [
                'id' => $userId,
                'email' => $email,
            ],
        ];
    }

    public function authenticate(string $email, string $password): array
    {
        $email = trim(strtolower($email));

        $stmt = $this->db->prepare('SELECT id, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            return [
                'ok' => false,
                'error' => 'Invalid credentials',
                'code' => 'unauthorized',
            ];
        }

        return ['ok' => true, 'user_id' => (int) $user['id']];
    }
}
