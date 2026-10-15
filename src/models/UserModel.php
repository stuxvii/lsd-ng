<?php
namespace LSD\Models;

use PDO;

class UserModel {
    private $db;

    public function __construct() {
        $this->db = Database::connect();
    }

    public function findUserByUsername(string $username): ?array {
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            return NULL;
        }
        $sql = 'SELECT * FROM users WHERE username = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$username]);

        $user = $stmt->fetch();
        return $user ?: NULL;
    }

    public function initSession(int $id, string $ip): ?string {
        $stmt = $this->db->prepare('INSERT INTO `sessions` (userid, useragent, ip) VALUES (?, ?, ?) RETURNING cookie');
        $stmt->execute([$id, $_SERVER['HTTP_USER_AGENT'] ?? '', $ip]);

        $result = $stmt->fetch();
        return $result['cookie'] ?? NULL;
    }

    public function getSessions(int $userId): ?array {
        $sql = 'SELECT id, useragent, `timestamp` FROM `sessions` WHERE userid = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        $info = $stmt->fetchAll();
        return $info ?: NULL;
    }

    public function revokeSession(int $id, int $userId): bool {
        $sql = 'DELETE FROM `sessions` WHERE id = ? AND userid = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id, $userId]);
    }

    public function findUserByAuthToken(string $token): ?array {
        if (!preg_match('/^[0-9A-F]{128}$/', $token)) {
            return NULL;
        }
        $sql = 'SELECT * FROM users JOIN sessions ON users.id = sessions.userid WHERE sessions.cookie = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$token]);

        $user = $stmt->fetch();
        return $user ?: NULL;
    }

    public function getUserInfo(int $userId): ?array {
        $sql = 'SELECT username, discord, joindate FROM users WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        $info = $stmt->fetch();
        return $info ?: NULL;
    }

    public function getUserEconomy(int $userId): ?array {
        $sql = 'SELECT * FROM economy WHERE receiver = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        $econ = $stmt->fetchAll();
        return $econ ?: NULL;
    }

    public function initUserEconomy(int $userId, int $defaultMoney = 100): bool {
        $sql = 'INSERT INTO economy (receiver, money) VALUES (?, ?)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$userId, $defaultMoney]);
    }

    public function getUserSettings(int $userId): ?array {
        $sql = 'SELECT * FROM config WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: NULL;
    }

    public function getUserProfile(int $userId): ?array {
        $sql = 'SELECT * FROM profiles WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: NULL;
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
