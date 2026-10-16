<?php
namespace LSD\Controllers;

class MainController extends BaseController {
    public function __construct() {
        parent::__construct();
    }

    public function Main() {
        echo 'works!';
    }
}
