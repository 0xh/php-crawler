<?php

error_reporting(E_ALL);

include dirname(__FILE__) . '/../bootstrap/autoload.php';
include 'ParserQuotes.php';

use Crawler\Client;

$startUrl = 'http://quotes.toscrape.com/page/1/';

$parser = new ParserQuotes();
$client = new Client();
$client->crawl([$startUrl], $parser, $startUrl);