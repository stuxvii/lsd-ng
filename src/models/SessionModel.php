<?php
namespace LSD\Models;

class SessionModel extends BaseModel {
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
}