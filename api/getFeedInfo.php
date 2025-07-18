<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php';
$feedUrl = $_GET['feedUrl'] ?? '';
if (empty($feedUrl)) {
    header('HTTP/1.0 400 Bad Request');
    die(json_encode(['error' => 'No feed URL provided']));
}
$feed = new SimplePie();
$feed->set_feed_url($feedUrl);
$feed->enable_cache(true);
$feed->set_cache_location(__DIR__ . '/../cache');
$feed->init();
if ($feed->error()) {
    header('HTTP/1.0 500 Internal Server Error');
    die(json_encode(['error' => 'Failed to fetch feed: ' . $feed->error()]));
}
echo json_encode([
    'title' => $feed->get_title(),
    'description' => $feed->get_description(),
    'image' => $feed->get_image_url()
]);
