<?php
namespace LSD\Models;

class UserModel extends BaseModel {
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

    public function getUserInfo(int $userId): ?array {
        $sql = 'SELECT username, discord, joindate FROM users WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);

        $info = $stmt->fetch();
        return $info ?: NULL;
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
}
