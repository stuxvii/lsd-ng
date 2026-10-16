<?php
namespace LSD\Controllers;

class BaseController {
    protected $userModel;
    protected $sessionModel;
    protected $ratelimitModel;
    protected $db;
    protected $userInfo = NULL;

    public function __construct() {
        $this->db = \LSD\Models\Database::connect();
        $this->userModel = new \LSD\Models\UserModel();
        $this->sessionModel = new \LSD\Models\SessionModel();
        $this->ratelimitModel = new \LSD\Models\RatelimitModel();

        foreach (CONFIG["cookie"] as $cookie) {
            if (isset($_COOKIE[$cookie])) {
                $this->userInfo = $this->sessionModel->findUserByAuthToken($_COOKIE[$cookie]);
            }
        }

        if (isset($this->userInfo['id'])) {
            $this->userInfo['session'] = $this->userInfo['id'];
            $this->userInfo['id'] = $this->userInfo['userid'];
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
