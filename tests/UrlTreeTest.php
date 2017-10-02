<?php

namespace Tests;

use Crawler\UrlTree;

/**
 * @coversDefaultClass \Crawler\UrlTree
 */
class UrlTreeTest extends BaseTest
{
    public $pool = [];

    /**
     * @var UrlTree $service ;
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
        $this->pool = [];
        $this->service = new UrlTree();
    }

    /**
     * @covers ::add
     * @covers ::get
     */
    public function testAdd()
    {
        $this->service->add($this->pool, $this->urls[0]);
        $url = $this->service->get($this->pool);
        self::assertEquals($this->urls[0], $url);
        $this->service->add($this->pool, $this->urls[1]);
        self::assertEquals($this->urls[0], $url);
    }

    /**
     * @covers ::urlToPathList
     */
    public function testUrlToPathList()
    {
        $urlPathList = $this->service->urlToPathList($this->urls[1]);
        $firstOne = ['http://',
            'www.example.com',
            '/',
            'page/',
        ];
        $lastOne = ['http://'];

        self::assertTrue($firstOne === $urlPathList[0]);
        self::assertTrue($lastOne === $urlPathList[3]);
    }

    /**
     * @covers ::tracerUrl
     */
    public function testTracerUrl()
    {
        $urlStack = $this->service->tracerUrl($this->pool);
        self::assertEquals($urlStack, []);

        $this->service->add($this->pool, $this->urls[0]);
        $urlStack = $this->service->tracerUrl($this->pool);

        $stack = [
            'http://',
            'www.example.com',
        ];
        self::assertEquals($stack, $urlStack);
    }
}
