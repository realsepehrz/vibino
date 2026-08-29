<?php
// models/Contact.php

class Contact {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all contacts with search
    public function getAll(array $filters = []): array {
        $sql = "SELECT c.*, co.name as company_name 
                FROM contacts c 
                LEFT JOIN companies co ON c.company_id = co.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $sql .= " AND (c.first_name LIKE :search OR c.last_name LIKE :search OR c.mobile LIKE :search OR c.email LIKE :search)";
            $params['search'] = $search;
        }

        if (!empty($filters['company_id'])) {
            $sql .= " AND c.company_id = :company_id";
            $params['company_id'] = $filters['company_id'];
        }

        $sql .= " ORDER BY c.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Get single contact by ID
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("
            SELECT c.*, co.name as company_name, co.legal_type
            FROM contacts c
            LEFT JOIN companies co ON c.company_id = co.id
            WHERE c.id = :id
        ");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // Create new contact
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO contacts (company_id, first_name, last_name, position, mobile, phone, email, national_id, is_primary, notes)
            VALUES (:company_id, :first_name, :last_name, :position, :mobile, :phone, :email, :national_id, :is_primary, :notes)
        ");
        
        $stmt->execute([
            'company_id' => $data['company_id'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'position' => $data['position'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'is_primary' => $data['is_primary'] ?? 0,
            'notes' => $data['notes'] ?? null
        ]);

        return (int)$this->db->lastInsertId();
    }

    // Update contact
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE contacts SET
                company_id = :company_id,
                first_name = :first_name,
                last_name = :last_name,
                position = :position,
                mobile = :mobile,
                phone = :phone,
                email = :email,
                national_id = :national_id,
                is_primary = :is_primary,
                notes = :notes
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'company_id' => $data['company_id'] ?? null,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'position' => $data['position'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'national_id' => $data['national_id'] ?? null,
            'is_primary' => $data['is_primary'] ?? 0,
            'notes' => $data['notes'] ?? null
        ]);
    }

    // Delete contact
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM contacts WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Set primary contact for a company (unsets others)
    public function setAsPrimary(int $contactId, int $companyId): bool {
        try {
            $this->db->beginTransaction();
            
            // Unset all primaries for this company
            $stmt = $this->db->prepare("UPDATE contacts SET is_primary = 0 WHERE company_id = :company_id");
            $stmt->execute(['company_id' => $companyId]);
            
            // Set this one as primary
            $stmt = $this->db->prepare("UPDATE contacts SET is_primary = 1 WHERE id = :id AND company_id = :company_id");
            $result = $stmt->execute(['id' => $contactId, 'company_id' => $companyId]);
            
            $this->db->commit();
            return $result;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}