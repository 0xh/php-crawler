<?php

namespace Crawler;

use Crawler\Contracts\LinkPoolInterface;
use Crawler\Contracts\ParserInterface;

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

    public function launch(ParserInterface $parser, $site = '')
    {
        $urls = app(LinkPool::class)->pop(10);

        /**
         * If there are no more URLs in the pool, return.
         */
        if (empty($urls)){
            return ;
        }

        $this->crawlUrls($urls, $parser, $site);

        $this->launch($parser, $site);
    }

    /**
     * Fetch URLs.
     *
     * @param array $urls
     * @param ParserInterface $parser
     * @param string $host
     */
    protected function crawlUrls(array $urls, ParserInterface $parser, $site = '')
    {
        $promises = $this->request->createPromises($urls, $parser, $site);
        $this->request->traversePromises($promises);
    }
}