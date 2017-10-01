<?php

namespace Crawler\Contracts;

use Psr\Http\Message\ResponseInterface;

interface ParserInterface
{
    public function setResponse(ResponseInterface $response);

    public function getStatusCode();

    public function parse();

    public function grabNewLinksForWholeSiteFetch($host, ResponseInterface $response);
}