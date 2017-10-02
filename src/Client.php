<?php

namespace Crawler;

use Crawler\Contracts\ClientInterface;
use Crawler\Contracts\ParserInterface;
use Crawler\Exceptions\ParameterException;

class Client implements ClientInterface
{
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

    public function setHeader(array $header)
    {
        // TODO: Implement setHeader() method.
    }

    public function crawl(array $urls, ParserInterface $parser, $site = '')
    {
        if (empty($urls)) {
            throw new ParameterException('Invalid URL list.');
        }

        // Add URLs to the pool;
        app(Core::class)->linkPool->add($urls);

        $site = getUrlSite($site);

        app(Core::class)->launch($parser, $site);
    }
}