<?php

use app\Controllers\HomeController;
use app\core\Application;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

$config = [
    'db' => [
        'dsn' => $_ENV['DB_DSN'],
        'user' => $_ENV['DB_USER'],
        'password' => $_ENV['DB_PASSWORD'],
    ]
];

$app = new Application(dirname(__DIR__), $config);

$app->router->get('/', [HomeController::class, 'home']);
$app->router->post('/', [HomeController::class, 'show_table']);

//delete item
//$app->router->delete('/', [HomeController::class, 'destroy']);
$app->router->post('/delete', [HomeController::class, 'destroy']);

//create item
$app->router->get('/create_item', [HomeController::class, 'create_item']);
//store item
$app->router->post('/store_item', [HomeController::class, 'store_item']);

//edit item
$app->router->get('/edit', [HomeController::class, 'edit']);
//update item
$app->router->post('/update', [HomeController::class, 'update']);

//db
$app->router->get('/create_db', [HomeController::class, 'create_db']);
$app->router->post('/store_db', [HomeController::class, 'store_db']);

$app->run();
