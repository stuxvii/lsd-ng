<?php
namespace LSD\Controllers;

class AuthController extends BaseController {
    public function Revoke() {
        try {
            if ($this->ratelimitModel->check(parent::getIP(), "auth") != 0) {
                throw new \Exception('Please wait before requesting!', 429);
            }
            $this->ratelimitModel->add(parent::getIP(), "auth", 2);

            if (!isset($this->userInfo['id'])) {
                throw new \Exception('Unauthorized', 401);
            }

            $id = isset($_POST['id']) ? $_POST['id'] : NULL;

            if (!$id || !is_numeric($id)) {
                throw new \Exception('You must supply the session to revoke.', 400);
            }

            $sessions = $this->sessionModel->getSessions($this->userInfo['id']);
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
            $this->sessionModel->revokeSession($id, $this->userInfo['id']);
            http_response_code(200);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            die(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function LogOut() {
        try {
            if (!isset($this->userInfo['id'])) {
                throw new \Exception('Unauthorized', 401);
            }
            foreach (CONFIG['cookie'] as $cookie) {
                setcookie($cookie, '', [
                    'expires' => 1,
                    'path' => '/',
                    'domain' => CONFIG['domain'],
                    'secure' => CONFIG['secureCookie'],
                    'httponly' => TRUE,
                    'samesite' => 'Strict'
                ]);
            }
            $this->sessionModel->revokeSession($this->userInfo['session'], $this->userInfo['id']);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            die(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function Sessions() {
        try {
            if (!isset($this->userInfo['id'])) {
                throw new \Exception('Unauthorized', 401);
            }

            $sessions['sessions'] = $this->sessionModel->getSessions($this->userInfo['id']);
            $sessions['active'] = $this->userInfo['session'];
            echo json_encode($sessions);
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            die(json_encode(['error' => $e->getMessage()]));
        }
    }

    public function Login() {
        try {
            if ($this->ratelimitModel->check(parent::getIP(), "auth") != 0) {
                throw new \Exception('Please wait before requesting!', 429);
            }
            $this->ratelimitModel->add(parent::getIP(), "auth", 2);

            if (isset($this->userInfo['id'])) {
                throw new \Exception('Already authenticated!', 400);
            }

            $username = isset($_POST['username']) ? $_POST['username'] : NULL;
            $password = isset($_POST['password']) ? $_POST['password'] : NULL;

            if (!$username || !$password) {
                throw new \Exception('You must supply username and password', 400);
            }
            $user = $this->userModel->findUserByUsername($username);
            if ($user && password_verify($password, $user['password'])) {
                foreach (CONFIG['cookie'] as $cookie) {
                    setcookie($cookie, $this->sessionModel->initSession($user['id'], parent::getIP()),
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
                throw new \Exception('Invalid credentials', 401);
            }
        } catch (\Exception $e) {
            http_response_code($e->getCode());
            die(json_encode(['error' => $e->getMessage()]));
        }
    }
}
