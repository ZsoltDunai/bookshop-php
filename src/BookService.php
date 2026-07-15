<?php

declare(strict_types=1);

class BookService
{
    public function __construct(private readonly PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM books ORDER BY title')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->execute([$id]);
        $book = $stmt->fetch();

        return $book ?: null;
    }

    public function search(string $query): array
    {
        $term = '%' . trim($query) . '%';
        $stmt = $this->db->prepare('
            SELECT * FROM books
            WHERE title LIKE ? OR author LIKE ?
            ORDER BY title
        ');
        $stmt->execute([$term, $term]);

        return $stmt->fetchAll();
    }
}
