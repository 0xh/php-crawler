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
    public function get($url, array $headers = [], array $otherOptions = [])
    {
        return $this->request('GET', $url, $headers, $otherOptions);
    }

    private function request($method, $uri, array $headers = [], array $otherOptions = [])
    {
        if (!isset($headers['User-Agent'])) {
            $headers += ['User-Agent' => $this->userAgent];
        }

        $httpOptions = [
            'headers' => $headers,
        ];
        $httpOptions += $otherOptions;
        $httpOptions['allow_redirects'] = true;
        if (!isset($httpOptions['timeout'])) {
            $httpOptions['timeout'] = 10;
        }

        return $this->httpClient->request($method, $uri, $httpOptions);
    }

    public function traversePromises($promises, $concurrency = 10)
    {
        (new \GuzzleHttp\Promise\EachPromise($promises, ['concurrency' => $concurrency]))
            ->promise()
            ->wait();
    }

    public function createPromises($urls, ParserInterface $parser, $site = '')
    {
        foreach ($urls as $url) {
            $this->log->info('Fetching '. $url);

            yield $this->httpClient->requestAsync('GET', $url)
                ->then(function (ResponseInterface $response) use ($url, $parser, $site) {
                    $parser->setResponse($response);

                    if ($site !== ''){
                        $parser->grabNewUrlsForWholeSiteFetch($site);
                    }

                    return $parser->parse();
                });
        }
    }
}