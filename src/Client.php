<?php

namespace Crawler;

use Crawler\Contracts\ClientInterface;
use Crawler\Contracts\ParserInterface;
use Crawler\Exceptions\ParameterException;

class Client implements ClientInterface
{
    protected $httpOptions = [];

    public function __construct(array $config = [])
    {
        /**
         * Initialization
         *
         * Register container.
         * load the Core.
         */
        Core::registerContainer();
        app(Core::class)->initialize();
        if (!empty($config)) {
            app(Core::class)->config = $config;
        }
    }

    public function setHttpOptions(array $httpOptions)
    {
        $this->httpOptions = $httpOptions;
    }

    public function request($method, $uri, array $httpOptions = [])
    {
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
    public function crawl(array $urls, ParserInterface $parser, $siteUrl = '')
    {
        if (empty($urls)) {
            throw new ParameterException('Invalid URL list.');
        }

        // Add URLs to the pool;
        app(Core::class)->linkPool->add($urls);

        $siteUrl = getUrlSite($siteUrl);

        app(Core::class)->launch($parser, $siteUrl, $this->httpOptions);
    }

    public function downloadFiles(array $UrlAndPathMap, ParserInterface $parser)
    {
        app(Core::class)->downloadFiles($UrlAndPathMap, $parser, $this->httpOptions);
    }
}