<?php

error_reporting(E_ALL);

include dirname(__FILE__) . '/../bootstrap/autoload.php';
include 'ParserQuotes.php';

use Crawler\Client;

$startUrl = 'http://quotes.toscrape.com/page/1/';
$cookieFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookieJar.txt';


$cookieJar = new \GuzzleHttp\Cookie\FileCookieJar($cookieFile, true);
$httpOptions = [
    'cookies' => $cookieJar,
];

$client = new Client();

// GET login page.
$response = $client->request('GET', 'http://quotes.toscrape.com/login', $httpOptions);
$selector = "input[name=csrf_token]";
$content = $response->getBody()->__toString();
$crsfValue = htmlqp($content)->find($selector)->attr('value');

// Make a post request to login
$httpOptions['form_params'] = [
    'csrf_token' => $crsfValue,
    'username' => 'test',
    'password' => 'test',
];
$httpOptions['allow_redirects'] = false;

$response = $client->request('POST', 'http://quotes.toscrape.com/login', $httpOptions);

// Check out the result
if ($response->getStatusCode() == 302){
    echo 'Login successfully', "\n";
} else {
    echo 'Login failed', "\n";
}