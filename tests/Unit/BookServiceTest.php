<?php

declare(strict_types=1);

class BookServiceTest extends TestCase
{
    public function testAllReturnsSeededBooks(): void
    {
        $books = new BookService();
        $all = $books->all();

        $this->assertGreaterThanOrEqual(6, count($all));
        $this->assertSame('1984', $all[0]['title']);
    }

    public function testFindExistingBook(): void
    {
        $books = new BookService();
        $book = $books->find(1);

        $this->assertNotNull($book);
        $this->assertSame('The Great Gatsby', $book['title']);
    }

    public function testFindMissingBookReturnsNull(): void
    {
        $books = new BookService();

        $this->assertNull($books->find(9999));
    }

    public function testSearchByAuthor(): void
    {
        $books = new BookService();
        $results = $books->search('Orwell');

        $this->assertCount(1, $results);
        $this->assertSame('1984', $results[0]['title']);
    }

    public function testSearchWithNoMatches(): void
    {
        $books = new BookService();
        $results = $books->search('zzznomatch');

        $this->assertCount(0, $results);
    }
}
