<?php
// controllers/CompanyController.php

class CompanyController {
    private Company $model;
    private Auth $auth;

    public function __construct() {
        $this->model = new Company();
        $this->auth = new Auth();
        
        if (!$this->auth->check()) {
            header('Location: /login');
            exit;
        }
    }

    // List all companies with search and filters
    public function index() {
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'province' => $_GET['province'] ?? ''
        ];

        $companies = $this->model->getAll($filters);
        $provinces = $this->model->getProvinces();
        $pageTitle = 'شرکت‌ها';
        $currentUser = $this->auth->user();
        
        include 'views/companies/index.php';
    }

    // Show company profile (360 view)
    public function profile() {
        $id = (int)($_GET['id'] ?? 0);
        
        if (!$id) {
            header('Location: /companies');
            exit;
        }

        $company = $this->model->find($id);
        
        if (!$company) {
            http_response_code(404);
            echo "شرکت مورد نظر یافت نشد";
            exit;
        }

        $contacts = $this->model->getContacts($id);
        $activities = $this->model->getActivities($id);
        $pageTitle = $company['name'];
        $currentUser = $this->auth->user();
        
        include 'views/companies/profile.php';
    }

    // Show create form
    public function create() {
        $pageTitle = 'ایجاد شرکت جدید';
        $currentUser = $this->auth->user();
        include 'views/companies/create.php';
    }

    // Store new company
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

        // Validate Iranian fields
        $errors = [];
        
        $nationalId = Validator::toEnglishDigits($_POST['national_id'] ?? '');
        if (!empty($nationalId) && !Validator::validateNationalId($nationalId)) {
            $errors[] = 'کد ملی وارد شده معتبر نیست';
        }

        $economicCode = Validator::toEnglishDigits($_POST['economic_code'] ?? '');
        if (!empty($economicCode) && !Validator::validateEconomicCode($economicCode)) {
            $errors[] = 'کد اقتصادی وارد شده معتبر نیست';
        }

        $sheba = strtoupper(str_replace(' ', '', $_POST['sheba'] ?? ''));
        if (!empty($sheba) && !Validator::validateSheba($sheba)) {
            $errors[] = 'شماره شبا وارد شده معتبر نیست';
        }

        $postalCode = Validator::toEnglishDigits($_POST['postal_code'] ?? '');
        if (!empty($postalCode) && !Validator::validatePostalCode($postalCode)) {
            $errors[] = 'کد پستی باید ۱۰ رقم باشد';
        }

        $phone = Validator::toEnglishDigits($_POST['phone'] ?? '');
        if (!empty($phone) && !Validator::validateMobile($phone)) {
            $errors[] = 'شماره موبایل وارد شده معتبر نیست (فرمت: 09xxxxxxxxx)';
        }

        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
            header('Location: /companies/create');
            exit;
        }

        $data = [
            'name' => Validator::sanitize($_POST['name']),
            'legal_type' => $_POST['legal_type'] ?? 'real',
            'national_id' => $nationalId ?: null,
            'economic_code' => $economicCode ?: null,
            'sheba' => $sheba ?: null,
            'province' => Validator::sanitize($_POST['province'] ?? ''),
            'city' => Validator::sanitize($_POST['city'] ?? ''),
            'address' => Validator::sanitize($_POST['address'] ?? ''),
            'postal_code' => $postalCode ?: null,
            'phone' => $phone ?: null,
            'website' => Validator::sanitize($_POST['website'] ?? ''),
            'status' => $_POST['status'] ?? 'active'
        ];

        $companyId = $this->model->create($data);

        // Log activity
        $this->logActivity($companyId, 'created', 'شرکت ایجاد شد');

        unset($_SESSION['old_input']);
        header('Location: /companies/profile?id=' . $companyId);
        exit;
    }

    // Update existing company
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        
        if (!$id || !$this->auth->verifyCsrf($_POST['csrf_token'] ?? '')) {
            http_response_code(400);
            exit;
        }

        // Similar validation as store...
        $nationalId = Validator::toEnglishDigits($_POST['national_id'] ?? '');
        $economicCode = Validator::toEnglishDigits($_POST['economic_code'] ?? '');
        $sheba = strtoupper(str_replace(' ', '', $_POST['sheba'] ?? ''));
        $postalCode = Validator::toEnglishDigits($_POST['postal_code'] ?? '');
        $phone = Validator::toEnglishDigits($_POST['phone'] ?? '');

        $data = [
            'name' => Validator::sanitize($_POST['name']),
            'legal_type' => $_POST['legal_type'] ?? 'real',
            'national_id' => $nationalId ?: null,
            'economic_code' => $economicCode ?: null,
            'sheba' => $sheba ?: null,
            'province' => Validator::sanitize($_POST['province'] ?? ''),
            'city' => Validator::sanitize($_POST['city'] ?? ''),
            'address' => Validator::sanitize($_POST['address'] ?? ''),
            'postal_code' => $postalCode ?: null,
            'phone' => $phone ?: null,
            'website' => Validator::sanitize($_POST['website'] ?? ''),
            'status' => $_POST['status'] ?? 'active'
        ];

        $this->model->update($id, $data);
        $this->logActivity($id, 'updated', 'اطلاعات شرکت به‌روزرسانی شد');

        header('Location: /companies/profile?id=' . $id);
        exit;
    }

    // Delete company
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id && $this->auth->verifyCsrf($_POST['csrf_token'] ?? '')) {
            $this->model->delete($id);
            $this->logActivity($id, 'deleted', 'شرکت حذف شد');
        }

        header('Location: /companies');
        exit;
    }

    // Helper to log activities
    private function logActivity(int $entityId, string $action, string $description): void {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("INSERT INTO activities (user_id, entity_type, entity_id, action, description) VALUES (:user_id, 'company', :entity_id, :action, :description)");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'entity_id' => $entityId,
            'action' => $action,
            'description' => $description
        ]);
    }
}