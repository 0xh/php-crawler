<?php

namespace Crawler;

use Crawler\Contracts\ClientInterface;
use Crawler\Contracts\ParserInterface;
use Crawler\Exceptions\ParameterException;

class Client implements ClientInterface
{
    protected $httpOptions = [];

    public function __construct()
    {
        /**
         * Initialization
         *
         * Register container.
         * load the Core.
         */
        Core::registerContainer();
        app(Core::class)->initialize();
    }

    public function setHttpOptions(array $httpOptions)
    {
        $this->httpOptions = $httpOptions;
    }

    public function request($method, $uri, array $httpOptions = []){
        return app(Core::class)->request->request($method, $uri, $httpOptions);
    }

    /**
     * Crawl URLs.
     *
     * @param array $urls
     * @param ParserInterface $parser
     * @param string $site
     * @throws ParameterException
     */
    public function crawl(array $urls, ParserInterface $parser, $site = '')
    {
        if (empty($urls)) {
            throw new ParameterException('Invalid URL list.');
        }

        // Add URLs to the pool;
        app(Core::class)->linkPool->add($urls);

        $site = getUrlSite($site);

        app(Core::class)->launch($parser, $site, $this->httpOptions);
    }
}