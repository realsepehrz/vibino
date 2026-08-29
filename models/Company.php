<?php
// models/Company.php

class Company {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // Get all companies with search and filter
    public function getAll(array $filters = []): array {
        $sql = "SELECT * FROM companies WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $sql .= " AND (name LIKE :search OR national_id LIKE :search OR city LIKE :search)";
            $params['search'] = $search;
        }

        if (!empty($filters['status'])) {
            $sql .= " AND status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['province'])) {
            $sql .= " AND province = :province";
            $params['province'] = $filters['province'];
        }

        $sql .= " ORDER BY created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Get single company by ID
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM companies WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // Create new company
    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO companies (name, legal_type, national_id, economic_code, sheba, province, city, address, postal_code, phone, website, status)
            VALUES (:name, :legal_type, :national_id, :economic_code, :sheba, :province, :city, :address, :postal_code, :phone, :website, :status)
        ");
        
        $stmt->execute([
            'name' => $data['name'],
            'legal_type' => $data['legal_type'] ?? 'real',
            'national_id' => $data['national_id'] ?? null,
            'economic_code' => $data['economic_code'] ?? null,
            'sheba' => $data['sheba'] ?? null,
            'province' => $data['province'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'postal_code' => $data['postal_code'] ?? null,
            'phone' => $data['phone'] ?? null,
            'website' => $data['website'] ?? null,
            'status' => $data['status'] ?? 'active'
        ]);

        return (int)$this->db->lastInsertId();
    }

    // Update company
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE companies SET
                name = :name,
                legal_type = :legal_type,
                national_id = :national_id,
                economic_code = :economic_code,
                sheba = :sheba,
                province = :province,
                city = :city,
                address = :address,
                postal_code = :postal_code,
                phone = :phone,
                website = :website,
                status = :status
            WHERE id = :id
        ");

        return $stmt->execute([
            'id' => $id,
            'name' => $data['name'],
            'legal_type' => $data['legal_type'],
            'national_id' => $data['national_id'],
            'economic_code' => $data['economic_code'],
            'sheba' => $data['sheba'],
            'province' => $data['province'],
            'city' => $data['city'],
            'address' => $data['address'],
            'postal_code' => $data['postal_code'],
            'phone' => $data['phone'],
            'website' => $data['website'],
            'status' => $data['status']
        ]);
    }

    // Delete company
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM companies WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    // Get contacts for a company
    public function getContacts(int $companyId): array {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE company_id = :company_id ORDER BY is_primary DESC, id ASC");
        $stmt->execute(['company_id' => $companyId]);
        return $stmt->fetchAll();
    }

    // Get activities for a company
    public function getActivities(int $companyId): array {
        $stmt = $this->db->prepare("
            SELECT a.*, u.first_name, u.last_name 
            FROM activities a
            JOIN users u ON a.user_id = u.id
            WHERE a.entity_type = 'company' AND a.entity_id = :entity_id
            ORDER BY a.created_at DESC
        ");
        $stmt->execute(['entity_id' => $companyId]);
        return $stmt->fetchAll();
    }

    // Get distinct provinces for filter dropdown
    public function getProvinces(): array {
        $stmt = $this->db->query("SELECT DISTINCT province FROM companies WHERE province IS NOT NULL ORDER BY province");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}