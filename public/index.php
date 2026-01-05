<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_GET['__ping']) && $_GET['__ping'] === '1') {
    header('Content-Type: text/plain');
    echo 'front-ok';
    exit;
}

require_once __DIR__ . '/../app/config/Router.php';

$router = new Router();
$router->get('/', 'Home', 'index');
$router->get('/filter', 'Home', 'filter');
$router->get('/search', 'Home', 'search');
$router->get('/search/suggestions', 'Home', 'searchSuggestions');
$router->get('/top-reviews', 'Home', 'topReviews');
$router->get('/top-commenters', 'Home', 'topCommenters');
$router->get('/about', 'Home', 'about');
$router->get('/contact', 'Home', 'contact');
$router->get('/ping', 'Home', 'ping');
$router->post('/game/{id}/submitRate', 'Game', 'submitRate');
$router->get('/game/{id}/rate', 'Game', 'rate');
$router->get('/game/{id}', 'Game', 'show');
$router->post('/game/{id}/review', 'Game', 'review');
$router->post('/review/vote', 'Game', 'vote');
$router->get('/debug', 'Home', 'ping');

$router->get('/login', 'Auth', 'login');
$router->post('/login', 'Auth', 'loginProcess');
$router->get('/signup', 'Auth', 'signup');
$router->post('/signup', 'Auth', 'signupProcess');
$router->get('/logout', 'Auth', 'logout');

$router->get('/dashboard', 'User', 'dashboard');
$router->get('/profile', 'User', 'profile');
$router->post('/profile', 'User', 'updateProfile');
$router->post('/user/follow', 'User', 'follow');
$router->post('/user/unfollow', 'User', 'unfollow');
$router->get('/followed-reviews', 'User', 'followedReviews');
$router->get('/followers', 'User', 'followers');

$router->get('/notifications', 'Notifications', 'index');
$router->post('/notifications/mark-read', 'Notifications', 'markRead');
$router->post('/notifications/mark-all-read', 'Notifications', 'markAllRead');
$router->get('/notifications/get-unread-count', 'Notifications', 'getUnreadCount');
$router->post('/notifications/delete', 'Notifications', 'delete');

$router->get('/chat', 'Chat', 'index');
$router->get('/chat/get-conversation', 'Chat', 'getConversation');
$router->get('/chat/get-new-messages', 'Chat', 'getNewMessages');
$router->post('/chat/send-message', 'Chat', 'sendMessage');
$router->get('/chat/get-unread-count', 'Chat', 'getUnreadCount');

$router->post('/spam/check', 'Spam', 'check');

$router->get('/polls', 'Community', 'polls');
$router->get('/poll/{id}/vote', 'Community', 'votePoll');
$router->post('/poll/{id}/vote', 'Community', 'votePoll');
$router->get('/poll/{id}/results', 'Community', 'pollResults');
$router->get('/suggestion', 'Community', 'suggestion');
$router->post('/suggestion', 'Community', 'suggestion');
$router->post('/suggestion/submit', 'Community', 'submitSuggestion');
$router->get('/report_bug', 'Community', 'reportBug');
$router->post('/report_bug', 'Community', 'reportBug');
$router->get('/bugs', 'Community', 'viewBugs');
$router->get('/gameplays', 'Community', 'gameplays');
$router->post('/gameplays/upload', 'Community', 'uploadGameplay');
$router->get('/gameplay/{id}', 'Community', 'viewGameplay');
$router->post('/gameplay/{id}/delete', 'Community', 'deleteGameplay');

$router->get('/admin/dashboard', 'Admin', 'dashboard');
$router->get('/admin/add-game', 'Admin', 'addGame');
$router->post('/admin/add-game', 'Admin', 'addGame');
$router->get('/admin/edit-game/{id}', 'Admin', 'editGame');
$router->post('/admin/edit-game/{id}', 'Admin', 'editGame');
$router->post('/admin/delete-game/{id}', 'Admin', 'deleteGame');

$output = $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
if (is_string($output)) {
    echo $output;
}
?>