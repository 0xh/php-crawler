<?php


namespace Crawler;


use Crawler\Contracts\ParserInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;

class Request
{
    private $userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36";

    private $log;


    public function __construct()
    {
        $this->httpClient = new \GuzzleHttp\Client();

        $this->log = new Logger('crawler');
    }

    /**
     * Request a URL.
     *
     * @param $method
     * @param $uri
     * @param array $httpOptions
     * @return mixed|ResponseInterface
     */
    public function request($method, $uri, array $httpOptions = [])
    {
        $httpOptions = $this->buildHttpOptions($httpOptions);
        return $this->httpClient->request($method, $uri, $httpOptions);
    }

    public function traversePromises($promises, $concurrency = 10)
    {
        (new \GuzzleHttp\Promise\EachPromise($promises, ['concurrency' => $concurrency]))
            ->promise()
            ->wait();
    }

    public function buildHttpOptions(array $httpOptions = [])
    {
        if (!isset($httpOptions['headers']['User-Agent'])) {
            $httpOptions['headers']['User-Agent'] = $this->userAgent;
        }

        if (!isset($httpOptions['allow_redirects'])) {
            $httpOptions['allow_redirects'] = true;
        }

        if (!isset($httpOptions['connect_timeout'])) {
            $httpOptions['connect_timeout'] = 30;
        }

        return $httpOptions;
    }

    public function createPromises($urls,
                                   ParserInterface $parser,
                                   $site = '',
                                   array $httpOptions = [])
    {
        $httpOptions = $this->buildHttpOptions($httpOptions);

        foreach ($urls as $url) {
            $this->log->info('Fetching ' . $url);

            yield $this->httpClient->requestAsync('GET', $url, $httpOptions)
                ->then(function (ResponseInterface $response) use ($url, $parser, $site) {

                    $parser->setResponse($response);

                    if ($site !== '') {
                        $parser->grabNewUrlsForWholeSiteFetch($site);
                    }

                    return $parser->parse();
                }, function ($exception) use ($url, $parser) {
                    $parser->handleFailedRequest($exception, $url);
                });
        }
    }

    public function createDownloadPromises($UrlAndPathMap,
                                           ParserInterface $parser,
                                           array $httpOptions = [])
    {
        $httpOptions = $this->buildHttpOptions($httpOptions);

        foreach ($UrlAndPathMap as $urlAndPath) {
            $url = $urlAndPath['url'];
            $filePath = $urlAndPath['filePath'];
            $resource = fopen($filePath, 'w');
            $httpOptions['sink'] = $resource;

            $this->log->info('Downloading ' . $url);
            yield $this->httpClient->requestAsync('GET', $url, $httpOptions)
                ->then(function (ResponseInterface $response) use ($url, $parser){
                    $parser->setResponse($response);
                    $parser->handleDownloadSuccessfullyRequest($url);
                }, function ($exception) use ($url, $parser) {
                    $parser->handleDownloadFailedRequest($exception, $url);
                });
        }
    }
}