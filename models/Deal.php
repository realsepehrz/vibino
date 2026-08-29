<?php
// models/Deal.php

class Deal {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getStages(): array {
        $stmt = $this->db->query("SELECT * FROM pipeline_stages ORDER BY order_index ASC");
        return $stmt->fetchAll();
    }

    public function getPipeline(): array {
        $stages = $this->getStages();
        $pipeline = [];

        foreach ($stages as $stage) {
            $stmt = $this->db->prepare("
                SELECT d.*, c.name as company_name, u.first_name as owner_name 
                FROM deals d
                JOIN companies c ON d.company_id = c.id
                JOIN users u ON d.created_by = u.id
                WHERE d.stage_id = :stage_id AND d.is_won = 0 AND d.is_lost = 0
                ORDER BY d.updated_at DESC
            ");
            $stmt->execute(['stage_id' => $stage['id']]);
            
            $deals = $stmt->fetchAll();
            $stage['deals'] = $deals;
            $stage['total_value'] = array_sum(array_column($deals, 'value'));
            
            $pipeline[] = $stage;
        }
        return $pipeline;
    }

    public function create(array $data): int {
        try {
            $this->db->beginTransaction();

            $subtotal = (int) $data['subtotal'];
            $discount = $subtotal * ((float) $data['discount_percent'] / 100);
            $afterDiscount = $subtotal - $discount;
            $tax = $afterDiscount * ((float) $data['tax_rate'] / 100);
            $final = $afterDiscount + $tax;

            $stmt = $this->db->prepare("
                INSERT INTO deals (company_id, contact_id, title, stage_id, value, probability, expected_close_date, discount_percent, tax_rate, final_amount, created_by)
                VALUES (:company_id, :contact_id, :title, :stage_id, :value, :probability, :close_date, :discount, :tax, :final, :user_id)
            ");

            $stmt->execute([
                'company_id' => $data['company_id'],
                'contact_id' => $data['contact_id'] ?? null,
                'title' => $data['title'],
                'stage_id' => $data['stage_id'],
                'value' => $subtotal,
                'probability' => $data['probability'],
                'close_date' => $data['close_date'],
                'discount' => $data['discount_percent'],
                'tax' => $data['tax_rate'],
                'final' => $final,
                'user_id' => $_SESSION['user_id']
            ]);

            $dealId = (int) $this->db->lastInsertId();

            if (!empty($data['items'])) {
                $itemStmt = $this->db->prepare("INSERT INTO deal_items (deal_id, description, quantity, unit_price, total_price) VALUES (:deal_id, :desc, :qty, :price, :total)");
                foreach ($data['items'] as $item) {
                    $itemStmt->execute([
                        'deal_id' => $dealId,
                        'desc' => $item['description'],
                        'qty' => $item['quantity'],
                        'price' => $item['unit_price'],
                        'total' => $item['quantity'] * $item['unit_price']
                    ]);
                }
            }

            $this->db->commit();
            return $dealId;

        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateStage(int $dealId, int $stageId): bool {
        $stmt = $this->db->prepare("UPDATE deals SET stage_id = :stage_id, updated_at = NOW() WHERE id = :id");
        return $stmt->execute(['stage_id' => $stageId, 'id' => $dealId]);
    }
    
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM deals WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getItems(int $dealId): array {
        $stmt = $this->db->prepare("SELECT * FROM deal_items WHERE deal_id = :deal_id");
        $stmt->execute(['deal_id' => $dealId]);
        return $stmt->fetchAll();
    }
}
