<?php

namespace Crawler;

use Crawler\Contracts\ClientInterface;
use Crawler\Contracts\ParserInterface;
use Crawler\Exceptions\ParameterException;

class Client implements ClientInterface
{
    public static $container;
    public $linkPool;
    public $request;

    public function __construct()
    {
        $this->linkPool = app(LinkPool::class);
        $this->request = app(Request::class);
    }

    public function setHeader(array $header)
    {
        // TODO: Implement setHeader() method.
    }

    public function crawl(array $urls, ParserInterface $parser)
    {
        if (empty($urls)) {
            throw new ParameterException('Invalid URL list');
        }

        // Add URLs to the pool;
        $this->linkPool->add($urls);
        $urls = app(LinkPool::class)->pop(10);

        $this->crawlUrls($urls, $parser);
    }

    protected function crawlUrls(array $urls, ParserInterface $parser)
    {
        $promises = $this->request->createPromises($urls, $parser);
        $this->request->traversePromises($promises);
    }
}