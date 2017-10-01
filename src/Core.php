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

    public function launch(ParserInterface $parser, $host = '')
    {
        $urls = app(LinkPool::class)->pop(10);

        /**
         * If there are no more URLs in the pool, return.
         */
        if (empty($urls)){
            return ;
        }

        $this->crawlUrls($urls, $parser, $host);

        $this->launch($parser, $host);
    }

    /**
     * Fetch URLs.
     *
     * @param array $urls
     * @param ParserInterface $parser
     * @param string $host
     */
    protected function crawlUrls(array $urls, ParserInterface $parser, $host = '')
    {
        $promises = $this->request->createPromises($urls, $parser, $host);
        $this->request->traversePromises($promises);
    }
}