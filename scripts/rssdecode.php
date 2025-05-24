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
      <pubDate>Fri, 16 May 2025 09:00:00 +0000</pubDate>
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
    $array = json_decode($json, true); // turn the xml into an associative array because php is the best programming language ever
    $this->feed = $array;
    foreach ($this->feed['channel']['item'] as &$item) {
      if (isset($item['pubDate'])) {
        $item['pubDate'] = strtotime($item['pubDate']);
      }
    }
    if (isset($this->feed['channel']['lastBuildDate'])) {
      $this->feed['channel']['lastBuildDate'] = strtotime($this->feed['channel']['lastBuildDate']);
    }
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

$rssFeed = new RssFeed($rss);
var_dump($rssFeed->getFeed());
var_dump($rssFeed->getChannelInfo()['title'] ?? 'Unknown');
var_dump($rssFeed->getItemFromID(0));