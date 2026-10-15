<?php
namespace LSD;

$router = new \AltoRouter();
$route = function (string $method, string $route, $func) use ($router) {
    $router->map($method, $route, $func);
};

$route('GET', '/', 'LSD\Controllers\MainController#Main');
$route('POST', '/session', 'LSD\Controllers\AuthController#Login');
$route('GET', '/session', 'LSD\Controllers\AuthController#Sessions');
$route('GET', '/session/logout', 'LSD\Controllers\AuthController#LogOut');
$route('POST', '/session/revoke', 'LSD\Controllers\AuthController#Revoke');

$match = $router->match();
if ($match) {
    $target = $match["target"];

    if (\is_string($target) && strpos($target, '#') !== false) {
        list($controllerName, $methodName) = explode('#', $target);
        if (class_exists($controllerName)) {
            $controller = new $controllerName();

            if (method_exists($controller, $methodName)) {
                \call_user_func_array([$controller, $methodName], $match['params']);
            } else {
                throw new \Exception("Method {$methodName} not found in {$controllerName}");
            }
        } else {
            throw new \Exception("Class {$controllerName} not found");
        }
    }
} else {
    http_response_code(404);
    require __DIR__ . '/views/errors/404.php';
    die;
}
