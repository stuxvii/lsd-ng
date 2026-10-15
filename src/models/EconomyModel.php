<?php
namespace LSD\Models;

class EconomyModel extends BaseModel {
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
}