<?php
header('Content-Type: application; charset=utf-8');
$rss = urldecode($_GET['content']);
$rss = '<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
  <channel>
    <title>PHP Lover News</title>
    <link>https://example.com</link>
    <description>The freshest updates from the temple of PHP</description>
    <language>en-us</language>
    <lastBuildDate>Sat, 17 May 2025 12:00:00 +0000</lastBuildDate>

    <item>
      <title>PHP 8.3 released - It\'s Glorious!</title>
      <link>https://example.com/php-8-3-released</link>
      <description>PHP 8.3 is out and it\'s melting servers with elegance.</description>
      <pubDate>Wed, 14 May 2025 09:00:00 +0000</pubDate>
      <guid>https://example.com/php-8-3-released</guid>
    </item>

    <item>
      <title>Why PHP is still the best language</title>
      <link>https://example.com/php-is-best</link>
      <description>Scientific proof that PHP is superior to all languages, even C++ (barely).</description>
      <pubDate>Thu, 15 May 2025 18:00:00 +0000</pubDate>
      <guid>https://example.com/php-is-best</guid>
    </item>
  </channel>
</rss>';
class RssFeed { // PHP + OOP = poop (LOL) // who came up with this joke? // it was you you're literally writing this comment right meow
  public $feed;
  public function __construct($feed) {
    $xml = simplexml_load_string($feed);
    if ($xml === false) {
      header('HTTP/1.1 400 Bad Request');
      echo 'Invalid XML';
      exit;
    }
    $json = json_encode($xml);
    $array = json_decode($json, true);
    $this->feed = $array;
    foreach ($this->feed['channel']['item'] as &$item) {
      if (isset($item['pubDate'])) {
        $item['pubDate'] = strtotime($item['pubDate']);
      }
    }
    unset($item); // break reference (we dont need it anymore)
    if (isset($this->feed['channel']['lastBuildDate'])) {
      $this->feed['channel']['lastBuildDate'] = strtotime($this->feed['channel']['lastBuildDate']);
    }
    // sort items by pubDate descending
    usort($this->feed['channel']['item'], function($a, $b) {
      return $b['pubDate'] <=> $a['pubDate'];
    });
  }
  public function getFeed() {
    return $this->feed;
  }

  public function getChannelInfo() { //title, link, description, language, lastBuildDate are the standard channel elements
    $channelInfo = $this->feed['channel'];
    unset($channelInfo['item']);
    return $channelInfo;
  }

  public function getAmountOfItems() {
    return count($this->feed['channel']['item']);
  }

  public function getItemFromID($index) {
    if (is_int($index) && $index >= 0 && $index < $this->getAmountOfItems()) {
      return $this->feed['channel']['item'][$index];
    } else {
      return null;
    }
  }
}

class CreateHTML {
  public static function createItem($item) {
    $html = '<div class="rss-item">';
    $html .= '<div class="rss-item-content">';
    $html .= '<h3>' . htmlspecialchars($item['title']) . '</h3>';
    $html .= '<p>' . htmlspecialchars($item['description']) . '</p>';
    $html .= '</div>';
    $html .= '<hr>';
    $html .= '<div class="rss-item-footer">';
    $html .= '<span>Published on: <span>' . date('Y-m-d', $item['pubDate']) . '</span></span> &bull; ';
    $html .= '<a href="' . htmlspecialchars($item['link']) . '">Read more</a>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
  }

  public static function createPage($pageLength, $pageNumber) {
    if (RssFeed::getAmountOfitems() < $pageLength * $pageNumber) {
      return 'No items found for this page.';
    }
  }
}

$rssFeed = new RssFeed($rss);
echo CreateHTML::createItem($rssFeed->getItemFromID(0)); // Display the first item as an example