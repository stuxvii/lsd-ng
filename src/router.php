<?php
require (__DIR__ . '/deps/altorouter.php');

// Source - https://stackoverflow.com/a/2692368
// Posted by soulmerge
// Retrieved 2026-09-07, License - CC BY-SA 2.5

foreach (scandir(__DIR__ . "/controllers/") as $filename) {
    $path = __DIR__ . '/' . $filename;
    echo $path;
    if (is_file($path)) {
        require_once $path;
    }
}

$router = new AltoRouter();
$route = function (string $method, string $route, $func) use ($router) {
    $router->map($method, $route, $func);
};

$route('GET', '/betaslam', MainController->Main());

$match = $router->match();
if (is_array($match) && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
    die;
}
