<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php'; // SO YOU'RE TELLING ME THAT THERE WAS A COMPOSER PACKAGE FOR SIMPLEPIE THE WHOLE TIME?!
$url = urldecode($_GET['url'] ?? '');
$fromItem = $_GET['fromItem'] ?? 0;
if (empty($url)) {
    header('HTTP/1.0 400 Bad Request');
    die('No RSS URL provided');
}
$feed = new SimplePie();
$feed->set_feed_url($url);
$feed->set_cache_location(__DIR__ . '/../cache');
$feed->set_cache_duration(3600); //1 hour
$feed->init();
echo '<a href="' . htmlspecialchars($feed->get_permalink()) . '"><h1>' . htmlspecialchars($feed->get_title()) . '</h1></a>';
