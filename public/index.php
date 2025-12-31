<?php
// Set error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Temporary debug fast-paths (safe to keep; only run when query present)
if (isset($_GET['__ping']) && $_GET['__ping'] === '1') {
    header('Content-Type: text/plain');
    echo 'front-ok';
    exit;
}

// Include the router (use absolute path for reliability)
require_once __DIR__ . '/../app/config/Router.php';

// Create router instance
$router = new Router();

// Define routes
$router->get('/', 'Home', 'index');
$router->get('/filter', 'Home', 'filter');
$router->get('/ping', 'Home', 'ping');
$router->post('/game/{id}/submitRate', 'Game', 'submitRate');
$router->get('/game/{id}/rate', 'Game', 'rate');
$router->get('/game/{id}', 'Game', 'show');
$router->post('/game/{id}/review', 'Game', 'review');
$router->post('/review/vote', 'Game', 'vote');
$router->get('/debug', 'Home', 'ping');

// Auth routes
$router->get('/login', 'Auth', 'login');
$router->post('/login', 'Auth', 'loginProcess');
$router->get('/signup', 'Auth', 'signup');
$router->post('/signup', 'Auth', 'signupProcess');
$router->get('/logout', 'Auth', 'logout');

// User routes
$router->get('/dashboard', 'User', 'dashboard');
$router->get('/profile', 'User', 'profile');
$router->post('/profile', 'User', 'updateProfile');

// Admin routes
$router->get('/admin/dashboard', 'Admin', 'dashboard');
$router->get('/admin/add-game', 'Admin', 'addGame');
$router->post('/admin/add-game', 'Admin', 'addGame');
$router->get('/admin/edit-game/{id}', 'Admin', 'editGame');
$router->post('/admin/edit-game/{id}', 'Admin', 'editGame');
$router->post('/admin/delete-game/{id}', 'Admin', 'deleteGame');

// Dispatch the request and output any returned content
$output = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
if (is_string($output)) {
    echo $output;
}
?>



