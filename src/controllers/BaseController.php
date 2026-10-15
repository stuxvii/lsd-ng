<?php
namespace LSD\Controllers;

use LSD\Models\Database;
use LSD\Models\SessionModel;
use LSD\Models\UserModel;

class BaseController {
    protected $userModel;
    protected $sessionModel;
    protected $db;
    protected $userInfo = NULL;
    protected $preferences = NULL;
    protected $profile = NULL;
    protected $economy = NULL;

    public function __CONSTRUCT() {
        $this->db = Database::connect();
        $this->userModel = new UserModel();
        $this->sessionModel = new SessionModel();

        foreach (CONFIG["cookie"] as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                $this->userInfo = $this->sessionModel->findUserByAuthToken($_COOKIE[$cookie]);
            }
        }

        if (isset($this->userInfo['id'])) {
            $this->userInfo['session'] = $this->userInfo['id'];
            $this->userInfo['id'] = $this->userInfo['userid'];
            // $user_id = $this->user_info['id'];
            // $this->preferences = $this->model->getUserSettings($user_id);
            // $this->profile = $this->model->getUserProfile($user_id);
            // $this->economy = $this->model->getUserEconomy($user_id);
            // if (!$this->preferences) {
            //     $stmt = $this->db->prepare('INSERT IGNORE INTO config (id) VALUES (?)');
            //     $stmt->execute([$user_id]);
            // }
            // if (!$this->profile) {
            //     $stmt = $this->db->prepare('INSERT IGNORE INTO profiles (id) VALUES (?)');
            //     $stmt->execute([$user_id]);
            // }
            // if (!$this->economy) {
            //     $stmt = $this->db->prepare('INSERT IGNORE INTO economy (id) VALUES (?)');
            //     $stmt->execute([$user_id]);
            // }
        }
    }

    public static function getIP(): string {
        $ipaddress = NULL;

        if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
            $ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ipaddress = trim(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0]);
        } else {
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        }

        if ($ipaddress !== NULL) {
            return hash('sha256', $ipaddress);
        } else {
            return hash('sha256', 'UNKNOWN');
        }
    }
}
