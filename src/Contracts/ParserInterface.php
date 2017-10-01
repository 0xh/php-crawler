<?php

namespace Crawler\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ParserInterface
{
    public function setResponse(ResponseInterface $response);

    public function getStatusCode();

    /**
     * Parse HTTP response.
     *
     * @return mixed
     */
    public function parse();

    public function grabNewUrlsForWholeSiteFetch($siteUrl);
}