<?php
// controllers/LeadController.php

class LeadController {
    private Lead $model;
    private Auth $auth;

    public function __construct() {
        $this->model = new Lead();
        $this->auth = new Auth();
        if (!$this->auth->check()) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $filters = [];
        if (isset($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (isset($_GET['source'])) {
            $filters['source'] = $_GET['source'];
        }

        $leads = $this->model->getAll($filters);
        $pageTitle = 'مدیریت سرنخ‌ها';
        $currentUser = $this->auth->user();
        
        include 'views/leads/index.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        if (!$this->auth->verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo "Invalid CSRF Token";
            exit;
        }

        $data = [
            'company_id' => isset($_POST['company_id']) ? (int)$_POST['company_id'] : null,
            'contact_id' => isset($_POST['contact_id']) ? (int)$_POST['contact_id'] : null,
            'title' => Auth::sanitize($_POST['title']),
            'source' => $_POST['source'] ?? 'website',
            'status' => $_POST['status'] ?? 'new',
            'estimated_value' => (int) str_replace(',', '', $_POST['estimated_value'] ?? '0'),
            'currency' => $_POST['currency'] ?? 'rial',
            'notes' => Auth::sanitize($_POST['notes'] ?? '')
        ];

        try {
            $this->model->create($data);
            header('Location: /leads?success=1');
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateStatus() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['id']) && isset($input['status'])) {
            $success = $this->model->updateStatus((int)$input['id'], $input['status']);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid data']);
        }
    }

    public function delete() {
        if (!isset($_GET['id'])) {
            header('Location: /leads');
            exit;
        }

        $this->model->delete((int)$_GET['id']);
        header('Location: /leads?deleted=1');
    }
}
