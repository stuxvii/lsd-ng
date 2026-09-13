<?php
namespace LSD\Controllers;
require_once __DIR__ . "/base.php";
class MainController extends BaseController {
    public function __CONSTRUCT()
    {
        parent::__CONSTRUCT();
    }

    public function Main()
    {
        echo 'works!';
    }
}
