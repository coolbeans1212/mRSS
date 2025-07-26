<?php
// hi
session_start();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Status: 405 Method Not Allowed');
    echo '<img src="/assets/images/IMG_1172.jpg" alt="literally me"><br>what do you think you are doing';
    exit;
}
if (!isset($_SESSION['user_id'])) {
    header('Status: 401 Unauthorised'); // unauthorised with an s because BRITAIN!!!!!
    echo '<img src="/assets/images/IMG_1172.jpg" alt="literally me"><br>you are not logged in, please log in first';
    exit;
}
$db = include __DIR__ . '/../scripts/db.php';
$db = $db['mrssdb'];
$sql = "INSERT INTO user_saved_feeds (user_id, url, title, description, image_url) VALUES (?, ?, ?, ?, ?)";
$stmt = $db->prepare($sql);
$stmt->bind_param(
    'issss',
    $_SESSION['user_id'],
    $_POST['feed-url'],
    $_POST['feed-title'],
    $_POST['feed-description'],
    $_POST['feed-image']
);
if ($stmt->execute()) {
    header('Location: /index.php');
} else {
    header('Status: 500 Internal Server Error');
    echo '<img src="/assets/images/IMG_1172.jpg" alt="literally me"><br>my bad gng </3 something no worky <br> more information: ' . $stmt->error;
}