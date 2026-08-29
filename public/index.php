<?php
// public/index.php - Main Entry Point

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Validator.php';

// Models
require_once __DIR__ . '/../models/Company.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../models/Lead.php';
require_once __DIR__ . '/../models/Deal.php';

// Controllers
require_once __DIR__ . '/../controllers/CompanyController.php';
require_once __DIR__ . '/../controllers/LeadController.php';
require_once __DIR__ . '/../controllers/DealController.php';

$router = new Router();
$auth = new Auth();

// Public Routes
$router->get('/login', function() {
    include __DIR__ . '/../views/auth/login.php';
});

$router->post('/login', function() use ($auth) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($auth->login($email, $password)) {
        header('Location: /dashboard');
        exit;
    } else {
        $error = "نام کاربری یا رمز عبور اشتباه است";
        include __DIR__ . '/../views/auth/login.php';
    }
});

// Protected Routes
$router->get('/dashboard', function() use ($auth) {
    if (!$auth->check()) {
        header('Location: /login');
        exit;
    }
    
    $pageTitle = 'داشبورد';
    $currentUser = $auth->user();
    ob_start();
    ?>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm">کل شرکت‌ها</h3>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-2 persian-num">۰</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm">معاملات فعال</h3>
            <p class="text-2xl font-bold text-primary-600 mt-2 persian-num">۰</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm">درآمد این ماه</h3>
            <p class="text-2xl font-bold text-green-600 mt-2">۰ ریال</p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
            <h3 class="text-gray-500 dark:text-gray-400 text-sm">تیکت‌های باز</h3>
            <p class="text-2xl font-bold text-orange-600 mt-2 persian-num">۰</p>
        </div>
    </div>
    <?php
    $content = ob_get_clean();
    include __DIR__ . '/../views/layouts/layout.php';
});

$router->get('/logout', function() use ($auth) {
    $auth->logout();
    header('Location: /login');
    exit;
});

// Company Routes
$router->get('/companies', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new CompanyController();
    $controller->index();
});

$router->get('/companies/profile', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new CompanyController();
    $controller->profile();
});

$router->post('/companies/store', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new CompanyController();
    $controller->store();
});

// Lead Routes
$router->get('/leads', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new LeadController();
    $controller->index();
});

$router->post('/leads/store', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new LeadController();
    $controller->store();
});

$router->get('/leads/delete', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new LeadController();
    $controller->delete();
});

// Deal Routes
$router->get('/deals/pipeline', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new DealController();
    $controller->pipeline();
});

$router->post('/deals/store', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new DealController();
    $controller->store();
});

$router->post('/deals/move', function() use ($auth) {
    if (!$auth->check()) { header('Location: /login'); exit; }
    $controller = new DealController();
    $controller->move();
});

$router->dispatch();
