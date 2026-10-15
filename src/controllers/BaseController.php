<?php
namespace LSD\Controllers;

use LSD\Models\Database;
use LSD\Models\UserModel;

class BaseController {
    protected $model;
    protected $db;
    protected $user_info = NULL;
    protected $preferences = NULL;
    protected $profile = NULL;
    protected $economy = NULL;

    public function __CONSTRUCT() {
        $this->db = Database::connect();
        $this->model = new UserModel();

        foreach (CONFIG["cookie"] as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                $this->user_info = $this->model->findUserByAuthToken($_COOKIE[$cookie]);
            }
        }

        if (isset($this->user_info['id'])) {
            $this->user_info['session'] = $this->user_info['id'];
            $this->user_info['id'] = $this->user_info['userid'];
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
}
