<?php

include dirname(__FILE__) . '/../bootstrap/autoload.php';
include 'ParserQuotes.php';

use Crawler\Client;

$urls = [
    'http://quotes.toscrape.com/page/1/',
    'http://quotes.toscrape.com/page/2/',
];

$parser = new ParserQuotes();
$client = new Client();
$client->crawl($urls, $parser);