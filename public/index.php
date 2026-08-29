<?php
// public/index.php - Main Entry Point

require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Auth.php';

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
        echo "Invalid credentials";
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

$router->dispatch();
