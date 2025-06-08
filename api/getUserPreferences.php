<?php
$dbs = require __DIR__ . '/scripts/db.php';
$userdb = $dbs['userdb'];
$mrssdb = $dbs['mrssdb'];
session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);
ini_set('session.cookie_domain', '.mateishome.page');
session_start();

if (isset($_SESSION['user_id'])) {
    $query = "SELECT * FROM user_preferences WHERE id = ?";
    $stmt = $mrssdb->prepare($query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userPreferences = $result->fetch_assoc();
    if ($userPreferences) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($userPreferences);
    } else {
        header('HTTP/1.0 404 Not Found');
        echo json_encode(['error' => 'User preferences no existy. The user is likely to be an inanimate object who can\'t figure out how the settings page works.']);
    }
} else {
    header('HTTP/1.0 401 Unauthorized');
    echo json_encode(['error' => 'you should probably log in that sounds really cool']);