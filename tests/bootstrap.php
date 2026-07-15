<?php

declare(strict_types=1);

define('ROOT_PATH', dirname(__DIR__));
define('DATA_PATH', ROOT_PATH . '/data');

require_once ROOT_PATH . '/src/Database.php';
require_once ROOT_PATH . '/src/Http/JsonResponse.php';
require_once ROOT_PATH . '/src/Http/Request.php';
require_once ROOT_PATH . '/src/Http/AuthContext.php';
require_once ROOT_PATH . '/src/Http/Cors.php';
require_once ROOT_PATH . '/src/JwtAuth.php';
require_once ROOT_PATH . '/src/ApiFormatter.php';
require_once ROOT_PATH . '/src/Auth.php';
require_once ROOT_PATH . '/src/BookService.php';
require_once ROOT_PATH . '/src/CartService.php';
require_once ROOT_PATH . '/src/OrderService.php';
require_once ROOT_PATH . '/src/Api/Controllers/BookController.php';
require_once ROOT_PATH . '/src/Api/Controllers/AuthController.php';
require_once ROOT_PATH . '/src/Api/Controllers/CartController.php';
require_once ROOT_PATH . '/src/Api/Controllers/OrderController.php';
require_once ROOT_PATH . '/src/ApiRouter.php';
require_once ROOT_PATH . '/src/App.php';

require_once __DIR__ . '/Support/HttpClient.php';
require_once __DIR__ . '/Support/HttpResponse.php';
require_once __DIR__ . '/Support/TestServer.php';
require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/IntegrationTestCase.php';
