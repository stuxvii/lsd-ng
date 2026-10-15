<?php
namespace LSD;
define("CONFIG", [
    "domain" => ".localhost",
    "cookie" => ["auth"],
    "secureCookie" => false // for local cert-less development this is turned off
]);
require_once __DIR__ . "/vendor/autoload.php";
require_once __DIR__ . "/router.php";