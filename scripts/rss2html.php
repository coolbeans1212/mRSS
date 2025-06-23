<?php
header('Content-Type: application; charset=utf-8');
require_once __DIR__ . '/../vendor/autoload.php'; // SO YOU'RE TELLING ME THAT THERE WAS A COMPOSER PACKAGE FOR SIMPLEPIE THE WHOLE TIME?!
$url = urldecode($_GET['url'] ?? '');
$fromPage = $_GET['fromPage'] ?? 1;
$pageLength = $_GET['pageLength'] ?? 5;
$timezone = $_GET['timezone'] ?? 'Europe/London';
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
echo '<a href="' . htmlspecialchars($feed->get_permalink()) . '"><h1>' . htmlspecialchars($feed->get_title() ?? $feed->get_permalink()) . ' (Demo feed)' . '</h1></a>';
echo '<div class="rss-items-container">';
$id = 0;
foreach ($feed->get_items(($fromPage - 1) * $pageLength, $pageLength) as $item) {
    $id++;
    echo '<div class="rss-item" id="' . $id . '">';
    echo '<h2 class="rss-item-title"><a href="' . $item->get_permalink() . '">' . $item->get_title() . '</a></h2>';
    echo '<span class="rss-item-description">' . $item->get_description() . '</span>';
    echo '<div class="rss-item-footer">';
    echo '<hr>';
    $date = new DateTime($item->get_date('c'), new DateTimeZone($timezone));
    $date->setTimezone(new DateTimeZone($timezone));
    echo '<p class="rss-item-date">' . $date->format('D j F Y h:i:s A e');
    if ($item->get_authors()) {
      echo ', by ';
      foreach ($item->get_authors() as $author) {
          echo htmlspecialchars($author->get_name()) . ' ';
      }
    }
    echo '</div></div>';
}
echo '</div>';
?>
<div class="page-navigation">
<?php
// Page navigation
$totalItems = $feed->get_item_quantity();
$totalPages = ceil($totalItems / $pageLength);
$minimumPageToDisplay = $fromPage > 5 ? $fromPage - 5 : 1;
$maximumPageToDisplay = $fromPage + 5 < $totalPages ? $fromPage + 5 : $totalPages;
for ($page = $minimumPageToDisplay; $page <= $maximumPageToDisplay; $page++) {
    if ($page == $fromPage) {
        echo '<span class="current-page">' . $page . '</span>';
    } else {
        echo '<span class="different-page" id="' . $page . '">' . $page . '</span>';
    }
}
?>
</div>
