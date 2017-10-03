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
    public function setHttpOptions(array $httpOptions);

    public function request($method, $uri, array $httpOptions = []);

    /**
     * Start processing.
     *
     * @param array $urls
     * @return void
     */
    public function crawl(array $urls, ParserInterface $parser, $site = '');
}