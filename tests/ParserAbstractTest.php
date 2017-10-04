<?php

namespace Tests;
use Crawler\Contracts\ParserAbstract;

/**
 * @coversDefaultClass \Crawler\Contracts\ParserAbstract
 */
class ParserAbstractTest extends BaseTest
{
    /**
     * ::@covers removeAssertUrls
     */
    public function testRemoveAssertUrls()
    {
        $urls = [
            'http://www.example.com',
            'http://www.example.com/page/1/assert.jpg',
            'http://www.example.com/page/2/assert.pdf',
        ];

        $parser = $this->getMockBuilder(ParserAbstract::class)
            ->getMockForAbstractClass();
        $data = $this->invokeMethod($parser, 'removeAssertUrls', [$urls]);

        $this->assertTrue($data == [$urls[0]]);
    }
}