<?php
require(__DIR__ . "/deps/altorouter.php");

$router = new AltoRouter();

$router->map("GET","/",function() {require_once __DIR__."/views/loggedout.php";});
$match = $router->match();

if (is_array($match) && is_callable($match["target"])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    http_response_code(404);
    require __DIR__."/views/errors/404.php";
    die;
}