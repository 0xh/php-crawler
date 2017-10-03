<?php


namespace Crawler;


use Crawler\Contracts\ParserInterface;
use Psr\Http\Message\ResponseInterface;
use Monolog\Logger;

class Request
{
    private $userAgent = "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.143 Safari/537.36";

    private $httpClient;
    private $header;
    private $log;


    public function __construct()
    {
        $this->httpClient = new \GuzzleHttp\Client();

        $this->log = new Logger('crawler');
    }

    public function setHeader(array $header)
    {
        $this->header = $header;
        return $this;
    }

    /**
     * Get
     *
     * @param string $baseUri Base URI like "http://www.example.com"
     * @param string $uri URI path like "/test/example/"
     * @param array $params Parameters to build query string
     * @param array $headers Headers array
     * @param array $otherOptions Other options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function get($url, array $httpOptions = [])
    {
        return $this->request('GET', $url, $httpOptions);
    }

    private function request($method, $uri, array $httpOptions = [])
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

        if (!isset($httpOptions['timeout'])) {
            $httpOptions['timeout'] = 30;
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
}