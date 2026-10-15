<?php
namespace LSD\Controllers;

use LSD\Models\UserModel;

class AuthController extends BaseController {
    public function __CONSTRUCT() {
        parent::__CONSTRUCT();
    }

    public function Revoke() {
        try {
            if (!isset($this->user_info['id'])) {
                throw new \Exception('Unauthorized', 401);
            }

            $id = isset($_POST['id']) ? $_POST['id'] : NULL;

            if (!$id || !is_numeric($id)) {
                throw new \Exception('You must supply the session to revoke.', 400);
            }

            $sessions = $this->model->getSessions($this->user_info['id']);
            $found = FALSE;
            foreach ($sessions as $item) {
                if ($item->id == $id) {
                    $found = TRUE;
                    break;
                }
            }
            if (!$found) {
                // even if this is not necessarily true the only legitimate way this gets triggered is if a user tries revoking an already revoked session id
                throw new \Exception('Session not found.', 404);
            }
            $this->model->revokeSession($id, $this->user_info['id']);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            die($e->getMessage());
        }
    }

    public function LogOut() {
        foreach (CONFIG['cookie'] as $cookie) {
            setcookie($cookie, '', [
                'expires' => time() - (86400 * 30),
                'path' => '/',
                'domain' => CONFIG['domain'],
                'secure' => CONFIG['secureCookie'],
                'httponly' => TRUE,
                'samesite' => 'Strict'
            ]);
        }
    }

    public function Sessions() {
        try {
            if (!isset($this->user_info['id'])) {
                throw new \Exception('Unauthorized', 401);
            }

            $sessions['sessions'] = $this->model->getSessions($this->user_info['id']);
            $sessions['thissesh'] = $this->user_info['session'];
            echo json_encode($sessions);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            die($e->getMessage());
        }
    }

    public function Login() {
        try {
            if (isset($this->user_info['id'])) {
                throw new \Exception('Already authenticated!', 200);
            }

            $username = isset($_POST['username']) ? $_POST['username'] : NULL;
            $password = isset($_POST['password']) ? $_POST['password'] : NULL;

            if (!$username || !$password) {
                throw new \Exception('You must supply username and password', 1);
            }
            $user = $this->model->findUserByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                foreach (CONFIG['cookie'] as $cookie) {
                    setcookie($cookie, $this->model->initSession($user['id'], UserModel::getIP()),
                        [
                            'expires' => time() + (86400 * 30),
                            'path' => '/',
                            'domain' => CONFIG['domain'],
                            'secure' => CONFIG['secureCookie'],
                            'httponly' => TRUE,
                            'samesite' => 'Strict'
                        ]);
                }
            } else {
                throw new \Exception('Invalid credentials', 2);
            }
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            die($e->getMessage());
        }
    }
}
