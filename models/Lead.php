<?php
// models/Lead.php

class Lead {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(array $filters = []): array {
        $sql = "SELECT l.*, c.name as company_name, CONCAT(co.first_name, ' ', co.last_name) as contact_name 
                FROM leads l 
                LEFT JOIN companies c ON l.company_id = c.id 
                LEFT JOIN contacts co ON l.contact_id = co.id 
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND l.status = :status";
            $params['status'] = $filters['status'];
        }
        
        if (!empty($filters['source'])) {
            $sql .= " AND l.source = :source";
            $params['source'] = $filters['source'];
        }
        
        $sql .= " ORDER BY l.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        // Calculate lead score based on criteria
        $score = $this->calculateScore($data);
        
        $stmt = $this->db->prepare("
            INSERT INTO leads (company_id, contact_id, title, source, status, score, estimated_value, currency, notes)
            VALUES (:company_id, :contact_id, :title, :source, :status, :score, :estimated_value, :currency, :notes)
        ");
        
        $stmt->execute([
            'company_id' => $data['company_id'] ?? null,
            'contact_id' => $data['contact_id'] ?? null,
            'title' => $data['title'],
            'source' => $data['source'] ?? 'website',
            'status' => $data['status'] ?? 'new',
            'score' => $score,
            'estimated_value' => $data['estimated_value'] ?? 0,
            'currency' => $data['currency'] ?? 'rial',
            'notes' => $data['notes'] ?? ''
        ]);
        
        return (int) $this->db->lastInsertId();
    }

    private function calculateScore(array $data): int {
        $score = 0;
        
        // Source scoring
        $sourceScores = [
            'referral' => 30,
            'linkedin' => 25,
            'website' => 20,
            'cold_call' => 10,
            'instagram' => 15
        ];
        $score += $sourceScores[$data['source']] ?? 10;
        
        // Estimated value scoring
        if (($data['estimated_value'] ?? 0) > 100000000) { // 10M Rial
            $score += 40;
        } elseif (($data['estimated_value'] ?? 0) > 50000000) {
            $score += 25;
        } elseif (($data['estimated_value'] ?? 0) > 10000000) {
            $score += 15;
        }
        
        return min($score, 100); // Cap at 100
    }

    public function updateStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare("UPDATE leads SET status = :status, updated_at = NOW() WHERE id = :id");
        return $stmt->execute(['status' => $status, 'id' => $id]);
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM leads WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM leads WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
