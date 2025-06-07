<?php

$dbs = require_once __DIR__ . '/../../scripts/db.php';
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
    if (!$userPreferences) { // user is not in the database, add them
        $query = "INSERT INTO user_preferences (id) VALUES (?)";
        $stmt = $mrssdb->prepare($query);
        $stmt->bind_param('i', $_SESSION['user_id']);
        $stmt->execute();
    }
}

header('Content-Type: text/css; charset=utf-8');

echo ':root {';
echo '--items-per-page: ' . ($userPreferences['items_per_page'] ?? 5) . ';';

echo '}';
