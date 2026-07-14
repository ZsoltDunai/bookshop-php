<?php

declare(strict_types=1);

final class ApiFormatter
{
    public static function book(array $book): array
    {
        return [
            'id' => (int) $book['id'],
            'title' => $book['title'],
            'author' => $book['author'],
            'price' => (float) $book['price'],
            'stock' => (int) $book['stock'],
        ];
    }

    public static function user(array $user): array
    {
        return [
            'id' => (int) $user['id'],
            'email' => $user['email'],
        ];
    }

    public static function cartItem(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'book_id' => (int) $row['book_id'],
            'quantity' => (int) $row['quantity'],
            'book' => self::book([
                'id' => $row['book_id'],
                'title' => $row['title'],
                'author' => $row['author'],
                'price' => $row['price'],
                'stock' => $row['stock'],
            ]),
        ];
    }

    public static function orderItem(array $row, ?array $book = null): array
    {
        $bookData = $book ?? [
            'id' => $row['book_id'],
            'title' => $row['title'],
            'author' => $row['author'],
            'price' => $row['price'],
            'stock' => 0,
        ];

        return [
            'id' => (int) $row['id'],
            'book_id' => (int) $row['book_id'],
            'quantity' => (int) $row['quantity'],
            'unit_price' => (float) ($row['unit_price'] ?? $row['price']),
            'book' => self::book($bookData),
        ];
    }

    public static function order(array $order, array $items): array
    {
        $books = new BookService();
        $formattedItems = [];

        foreach ($items as $item) {
            $book = $books->find((int) $item['book_id']);
            $formattedItems[] = self::orderItem($item, $book);
        }

        return [
            'id' => (int) $order['id'],
            'total' => (float) $order['total'],
            'status' => $order['status'],
            'created_at' => $order['created_at'],
            'items' => $formattedItems,
        ];
    }
}
