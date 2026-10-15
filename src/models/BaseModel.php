<?php
namespace LSD\Models;

class BaseModel {
    protected $db;

    public function __construct() {
        $this->db = Database::connect();
    }
}