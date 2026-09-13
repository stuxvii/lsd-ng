<?php
namespace LSD\Controllers;
require_once dirname(__DIR__) . "/models/database.php";
require_once dirname(__DIR__) . "/models/user.php";
class BaseController {
    protected $model;
    protected $db;
    protected $user_info = null;
    protected $preferences = null;
    protected $profile = null;
    protected $economy = null;

    public function __CONSTRUCT() {
        $this->db = \LSD\Models\Database::connect();
        $this->model = new \LSD\Models\UserModel();

        if (isset($_COOKIE["auth"])) {
            $this->user_info = $this->model->findUserByAuthToken($_COOKIE["auth"]);
        }

        if (isset($this->user_info['id'])) {
            $user_id = $this->user_info['id'];
            $this->preferences = $this->model->getUserSettings($user_id);
            $this->profile = $this->model->getUserProfile($user_id);
            $this->economy = $this->model->getUserEconomy($user_id);
            if (!$this->preferences) {
                $stmt = $this->db->prepare('INSERT IGNORE INTO config (id) VALUES (?)');
                $stmt->execute([$user_id]);
            }
            if (!$this->profile) {
                $stmt = $this->db->prepare('INSERT IGNORE INTO profiles (id) VALUES (?)');
                $stmt->execute([$user_id]);
            }
            if (!$this->economy) {
                $stmt = $this->db->prepare('INSERT IGNORE INTO economy (id) VALUES (?)');
                $stmt->execute([$user_id]);
            }
        }
    }

    public function getuser(int $user_id): Array {
        $getuserstmt = $this->db->prepare("
        SELECT username, discordid, isoperator, registerts
        FROM users 
        WHERE id = ?
        ");
        $getuserstmt->execute([$user_id]);
        $userinfo = $getuserstmt->fetch(PDO::FETCH_ASSOC);
        return $userinfo;
    }

    public function showPage($path) {
        ob_start();
        require_once ROOT_PATH . "/views/{$path}";
        $page_content = ob_get_clean();
        require_once ROOT_PATH . "/views/layout/template.php";
    }
}