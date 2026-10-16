<?php
namespace LSD\Models;

class RatelimitModel extends BaseModel {
    public function add(string $ip, string $action, int $wait = 3): bool {
        if (rand(1, 100) === 1) {
            $this->cleanup();
        }
        $stmt = $this->db->prepare('INSERT INTO `ratelimits` (`ip`, `wait`, `action`) VALUES (?, ?, ?)');
        return $stmt->execute([$ip, $wait, $action]);
    }

    public function check(string $ip, string $action): int {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM `ratelimits` WHERE `ip` = ? AND `action` = ? AND `timestamp` >= NOW() - INTERVAL `wait` SECOND');
        $stmt->execute([$ip, $action]);
        return $stmt->fetchColumn();
    }

    public function cleanup(): void {
        $this->db->exec('DELETE FROM `ratelimits` WHERE `timestamp` + INTERVAL `wait` SECOND <= NOW()');
    }
}