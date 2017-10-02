<?php

namespace Tests;

use Crawler\Core;
use Crawler\FetchedLinkPool;
use Crawler\LinkPool;

class LinkPoolTest extends BaseTest
{
    /**
     * @var LinkPool $service ;
     */
    public $service;
    public $urls = [
        'http://www.example.com',
        'http://www.example.com/page/',
        'http://www.example.com/page/1',
        'http://www.example.com/page/2',
    ];

    public function setUp()
    {
        parent::setUp();
        $this->service = app(LinkPool::class);
    }

    public function testPop()
    {
        $this->service->add([$this->urls[0]]);
        $urls = $this->service->pop(1);
        self::assertEquals([$this->urls[0]], $urls);

        $pool = $this->service->getPool();
        self::assertEquals([], $pool);

        $fetchedUrls = app(FetchedLinkPool::class)->pop();
        self::assertEquals([$this->urls[0]], $fetchedUrls);

        $pool = app(FetchedLinkPool::class)->getPool();
        self::assertEquals([], $pool);
    }

    /**
     * ::@covers isExist
     */
    public function testIsExist(){
        $this->service->add([$this->urls[0]]);

        $isExist = $this->service->isExist($this->urls[0]);
        self::assertTrue($isExist);

        $isExist = $this->service->isExist('something.else');
        self::assertFalse($isExist);
    }
}