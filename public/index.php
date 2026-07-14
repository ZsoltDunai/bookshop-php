<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/src/bootstrap.php';

$auth = new Auth();
$books = new BookService();
$cart = new CartService();
$orders = new OrderService();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$path = rtrim($path, '/') ?: '/';

switch (true) {
    case $path === '/' && $method === 'GET':
        $query = trim($_GET['q'] ?? '');
        $bookList = $query !== '' ? $books->search($query) : $books->all();
        view('home', ['books' => $bookList, 'searchQuery' => $query]);
        break;

    case $path === '/book' && $method === 'GET':
        $id = (int) ($_GET['id'] ?? 0);
        $book = $books->find($id);
        if (!$book) {
            http_response_code(404);
            view('404');
            break;
        }
        view('book', ['book' => $book]);
        break;

    case $path === '/login' && $method === 'GET':
        if ($auth->user()) {
            redirect('/');
        }
        view('login');
        break;

    case $path === '/login' && $method === 'POST':
        $result = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['ok']) {
            flash('success', 'Welcome back!');
            redirect('/');
        }
        view('login', ['error' => $result['error'], 'email' => $_POST['email'] ?? '']);
        break;

    case $path === '/register' && $method === 'GET':
        if ($auth->user()) {
            redirect('/');
        }
        view('register');
        break;

    case $path === '/register' && $method === 'POST':
        $result = $auth->register($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($result['ok']) {
            flash('success', 'Account created! Start browsing our collection.');
            redirect('/');
        }
        view('register', ['error' => $result['error'], 'email' => $_POST['email'] ?? '']);
        break;

    case $path === '/logout' && $method === 'POST':
        $auth->logout();
        flash('success', 'You have been logged out.');
        redirect('/');
        break;

    case $path === '/cart' && $method === 'GET':
        $user = $auth->requireLogin();
        $items = $cart->items((int) $user['id']);
        view('cart', ['items' => $items, 'total' => $cart->total((int) $user['id'])]);
        break;

    case $path === '/cart/add' && $method === 'POST':
        $user = $auth->requireLogin();
        $bookId = (int) ($_POST['book_id'] ?? 0);
        $quantity = max(1, (int) ($_POST['quantity'] ?? 1));
        $result = $cart->add((int) $user['id'], $bookId, $quantity);

        if ($result['ok']) {
            flash('success', 'Added to cart!');
        } else {
            flash('error', $result['error']);
        }

        $redirectTo = $_POST['redirect'] ?? '/cart';
        redirect($redirectTo);
        break;

    case $path === '/cart/update' && $method === 'POST':
        $user = $auth->requireLogin();
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);
        $result = $cart->update((int) $user['id'], $itemId, $quantity);

        if (!$result['ok']) {
            flash('error', $result['error']);
        }

        redirect('/cart');
        break;

    case $path === '/cart/remove' && $method === 'POST':
        $user = $auth->requireLogin();
        $itemId = (int) ($_POST['item_id'] ?? 0);
        $cart->remove((int) $user['id'], $itemId);
        flash('success', 'Item removed from cart.');
        redirect('/cart');
        break;

    case $path === '/checkout' && $method === 'POST':
        $user = $auth->requireLogin();
        $result = $orders->checkout((int) $user['id']);

        if ($result['ok']) {
            flash('success', 'Order #' . $result['order_id'] . ' placed successfully!');
            redirect('/orders');
        }

        flash('error', $result['error']);
        redirect('/cart');
        break;

    case $path === '/orders' && $method === 'GET':
        $user = $auth->requireLogin();
        $userOrders = $orders->forUser((int) $user['id']);
        $orderDetails = [];

        foreach ($userOrders as $order) {
            $orderDetails[] = [
                'order' => $order,
                'items' => $orders->items((int) $order['id']),
            ];
        }

        view('orders', ['orderDetails' => $orderDetails]);
        break;

    case $path === '/health' && $method === 'GET':
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'app' => 'bookshop-php']);
        break;

    default:
        http_response_code(404);
        view('404');
        break;
}
