<?php
// controllers/DealController.php

class DealController {
    private Deal $model;
    private Auth $auth;

    public function __construct() {
        $this->model = new Deal();
        $this->auth = new Auth();
        if (!$this->auth->check()) {
            header('Location: /login');
            exit;
        }
    }

    public function pipeline() {
        $pipeline = $this->model->getPipeline();
        $pageTitle = 'خط لوله فروش';
        $currentUser = $this->auth->user();
        
        include __DIR__ . '/../views/deals/pipeline.php';
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
            'company_id' => (int)$_POST['company_id'],
            'contact_id' => isset($_POST['contact_id']) ? (int)$_POST['contact_id'] : null,
            'title' => Auth::sanitize($_POST['title']),
            'stage_id' => (int)$_POST['stage_id'],
            'probability' => (int)$_POST['probability'],
            'close_date' => $_POST['close_date'],
            'discount_percent' => (float)($_POST['discount_percent'] ?? 0),
            'tax_rate' => (float)($_POST['tax_rate'] ?? 9),
            'subtotal' => (int)$_POST['subtotal_raw'],
            'items' => []
        ];

        if (isset($_POST['item_desc'])) {
            $descriptions = $_POST['item_desc'];
            $quantities = $_POST['item_qty'];
            $prices = $_POST['item_price'];
            
            foreach ($descriptions as $key => $desc) {
                if (!empty($desc)) {
                    $data['items'][] = [
                        'description' => Auth::sanitize($desc),
                        'quantity' => (int)$quantities[$key],
                        'unit_price' => (int) str_replace(',', '', $prices[$key])
                    ];
                }
            }
        }

        try {
            $this->model->create($data);
            header('Location: /deals/pipeline?success=1');
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function move() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (isset($input['deal_id']) && isset($input['stage_id'])) {
            $success = $this->model->updateStage((int)$input['deal_id'], (int)$input['stage_id']);
            echo json_encode(['success' => $success]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid data']);
        }
    }
}
