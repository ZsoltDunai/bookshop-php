<?php

declare(strict_types=1);

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = getenv('BOOKSHOP_DB');
            if ($dsn === false || $dsn === '') {
                if (!is_dir(DATA_PATH)) {
                    mkdir(DATA_PATH, 0777, true);
                }

                $dsn = 'sqlite:' . DATA_PATH . '/bookshop.sqlite';
            }

            self::$instance = new PDO($dsn);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        }

        return self::$instance;
    }

    public static function reset(): void
    {
        self::$instance = null;
    }

    public static function initialize(): void
    {
        $pdo = self::getInstance();

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS books (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                author TEXT NOT NULL,
                price REAL NOT NULL,
                stock INTEGER NOT NULL DEFAULT 0,
                description TEXT
            )
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS cart_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                book_id INTEGER NOT NULL,
                quantity INTEGER NOT NULL DEFAULT 1,
                UNIQUE(user_id, book_id),
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (book_id) REFERENCES books(id) ON DELETE CASCADE
            )
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                total REAL NOT NULL,
                status TEXT NOT NULL DEFAULT "completed",
                created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id)
            )
        ');

        $pdo->exec('
            CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                book_id INTEGER NOT NULL,
                title TEXT NOT NULL,
                author TEXT NOT NULL,
                price REAL NOT NULL,
                quantity INTEGER NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
            )
        ');

        self::seed();
    }

    private static function seed(): void
    {
        $pdo = self::getInstance();

        $bookCount = (int) $pdo->query('SELECT COUNT(*) FROM books')->fetchColumn();
        if ($bookCount === 0) {
            $books = [
                ['The Great Gatsby', 'F. Scott Fitzgerald', 12.99, 10, 'A portrait of the Jazz Age and the American Dream in 1920s New York.'],
                ['1984', 'George Orwell', 10.49, 15, 'A dystopian novel about totalitarian surveillance and thought control.'],
                ['To Kill a Mockingbird', 'Harper Lee', 11.99, 8, 'A gripping tale of racial injustice and childhood innocence in the Deep South.'],
                ['Pride and Prejudice', 'Jane Austen', 9.99, 12, 'A witty romance exploring love, class, and social expectations in Regency England.'],
                ['The Hobbit', 'J.R.R. Tolkien', 14.99, 20, 'A hobbit embarks on an unexpected journey to reclaim a dwarf kingdom.'],
                ['Dune', 'Frank Herbert', 13.49, 6, 'Epic science fiction set on the desert planet Arrakis.'],
                ['The Catcher in the Rye', 'J.D. Salinger', 10.99, 9, 'Holden Caulfield navigates alienation and identity in post-war New York.'],
                ['Brave New World', 'Aldous Huxley', 11.49, 11, 'A chilling vision of a genetically engineered future society.'],
            ];

            $stmt = $pdo->prepare('INSERT INTO books (title, author, price, stock, description) VALUES (?, ?, ?, ?, ?)');
            foreach ($books as $book) {
                $stmt->execute($book);
            }
        }

        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute(['demo@bookshop.io']);
        if (!$stmt->fetch()) {
            $stmt = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
            $stmt->execute(['demo@bookshop.io', password_hash('password123', PASSWORD_DEFAULT)]);
        }
    }
}
