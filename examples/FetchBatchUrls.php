<?php

include dirname(__FILE__) . '/../bootstrap/autoload.php';
include 'Parser.php';

use Crawler\Client;

$urls = [
    'http://quotes.toscrape.com/page/1/',
    'http://quotes.toscrape.com/page/2/',
];

$parser = new Parser();
$client = new Client();
$client->crawl($urls, $parser);