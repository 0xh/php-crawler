<?php

namespace Crawler;

use Crawler\Contracts\LinkPoolInterface;
use Crawler\Contracts\ParserInterface;
use Pimple\Container;

/**
 * Class Core
 *
 * @package Crawler
 */
class Core
{
    public static $container;

    /**
     * @var LinkPoolInterface $linkPool
     */
    public $linkPool;

    /**
     * @var LinkPoolInterface $linkPool
     */
    public $fetchedLinkPool;

    /**
     * Launch fetching.
     *
     * @var Request $request
     */
    public $request;

    public static function registerContainer(){
        Core::$container = new Container();
        (new ServiceProvider())->register();
    }

    public function initialize()
    {
        $this->linkPool = app(LinkPool::class);
        $this->fetchedLinkPool = app(FetchedLinkPool::class);
        $this->request = app(Request::class);
    }

    public function launch(ParserInterface $parser, $site = '', array $httpOptions)
    {
        $urls = app(LinkPool::class)->pop(10);

        /**
         * If there are no more URLs in the pool, return.
         */
        if (empty($urls)){
            return ;
        }

        $this->crawlUrls($urls, $parser, $site, $httpOptions);

        $this->launch($parser, $site, $httpOptions);
    }

    /**
     * Fetch URLs.
     *
     * @param array $urls
     * @param ParserInterface $parser
     * @param string $host
     */
    protected function crawlUrls(array $urls, ParserInterface $parser, $site = '', array $httpOptions)
    {
        $promises = $this->request->createPromises($urls, $parser, $site, $httpOptions);
        $this->request->traversePromises($promises);
    }
}