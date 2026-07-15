<?php

declare(strict_types=1);

final class CartRequestValidator
{
    public static function addItem(array $body): array
    {
        $bookId = RequestValidator::requirePositiveInt($body, 'book_id');
        if ($bookId === null) {
            return RequestValidator::fail('book_id must be a positive integer.');
        }

        if (!array_key_exists('quantity', $body)) {
            return RequestValidator::ok([
                'book_id' => $bookId,
                'quantity' => 1,
            ]);
        }

        $quantity = RequestValidator::requirePositiveInt($body, 'quantity');
        if ($quantity === null) {
            return RequestValidator::fail('quantity must be a positive integer.');
        }

        return RequestValidator::ok([
            'book_id' => $bookId,
            'quantity' => $quantity,
        ]);
    }

    public static function updateItem(array $body): array
    {
        if (!array_key_exists('quantity', $body)) {
            return RequestValidator::fail('quantity is required.');
        }

        $quantity = RequestValidator::requirePositiveInt($body, 'quantity');
        if ($quantity === null) {
            return RequestValidator::fail('quantity must be a positive integer.');
        }

        return RequestValidator::ok(['quantity' => $quantity]);
    }
}
