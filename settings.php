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
    $query = "SELECT * FROM users WHERE id = ?";
    $stmt = $userdb->prepare($query);
    $stmt->bind_param('i', $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $userInfo = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php require_once __DIR__ . '/scripts/createHeadSection.php';
        createHeadSection(); ?>
    </head>
    <body>
        <header>
            <?php require_once __DIR__ . '/segments/header.php'; ?>
        </header>
    <main>
    <div class="rss-feeds-listing">
        <div class="row">
            <img src="" alt="General settings icon" class="feed-icon">
            <h2>General</h2>
        </div>
        <div class="row">
            <img src="/assets/images/layout.png" alt="Layout settings icon" class="feed-icon">
            <h2>Layout</h2>
        </div>
        <div class="row">
            <img src="" alt="Customisation settings icon" class="feed-icon">
            <h2>Customisation</h2>
        </div>
        <div class="row">
            <img src="" alt="Account settings icon" class="feed-icon">
            <h2>Account</h2>
        </div>
    </div>
    </main>
