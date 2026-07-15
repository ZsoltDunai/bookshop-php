<?php

declare(strict_types=1);

final class BookController
{
    public function __construct(private readonly BookService $books)
    {
    }

    public function index(): never
    {
        $query = Request::query('q');
        $rows = $query !== '' ? $this->books->search($query) : $this->books->all();

        JsonResponse::json(array_map(
            static fn (array $book) => ApiFormatter::book($book),
            $rows
        ));
    }

    public function show(int $id): never
    {
        $book = $this->books->find($id);
        if (!$book) {
            JsonResponse::error('Book not found', 404);
        }

        JsonResponse::json(ApiFormatter::book($book));
    }
}
