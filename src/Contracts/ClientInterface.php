<?php

namespace Crawler\Contracts;

interface ClientInterface
{
    /**
     * Set HTTP headers.
     *
     * @param array $header
     * @return ClientInterface
     */
    public function setHeader(array $header);

    /**
     * Start processing.
     *
     * @param array $urls
     * @return void
     */
    public function crawl(array $urls, ParserInterface $parser, $site = '');
}