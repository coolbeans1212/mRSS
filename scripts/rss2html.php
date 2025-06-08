<?php
header('Content-Type: application; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php'; // SO YOU'RE TELLING ME THAT THERE WAS A COMPOSER PACKAGE FOR SIMPLEPIE THE WHOLE TIME?!
$url = urldecode($_GET['url'] ?? '');
$fromPage = $_GET['fromPage'] ?? 1;
$pageLength = $_GET['pageLength'] ?? 5;
if (empty($url)) {
    header('HTTP/1.0 400 Bad Request');
    die('No RSS URL provided');
}
$feed = new SimplePie();
$feed->set_feed_url($url);
if ($_GET['cache'] ?? 'true' == 'false') {
    $feed->enable_cache(false);
} else {
    $feed->enable_cache(true);
    $feed->set_cache_location(__DIR__ . '/../cache');
    $feed->set_cache_duration(1800); // only cache for 30 minutes
}
$feed->init();
echo '<a href="' . htmlspecialchars($feed->get_permalink()) . '"><h1 class="margin-20px">' . htmlspecialchars($feed->get_title() ?? $feed->get_permalink()) . ' (Demo feed)' . '</h1></a>';
echo '<div class="rss-items-container">';
foreach ($feed->get_items(($fromPage - 1) * $pageLength, $pageLength) as $item) {
    echo '<div class="rss-item">';
    echo '<h2 class="rss-item-title"><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></h2>';
    echo '<p class="rss-item-description">' . $item->get_description() . '</p>';
    echo '<div class="rss-item-footer">';
    echo '<hr>';
    echo '<p class="rss-item-date">' . $item->get_date('D j F Y h:i:s A e');
    if ($item->get_authors()) {
      echo ', by ';
      foreach ($item->get_authors() as $author) {
          echo htmlspecialchars($author->get_name()) . ' ';
      }
    }
    echo '</div></div>';
}
echo '</div>';
