<?php

namespace Crawler\Contracts;

use Psr\Http\Message\ResponseInterface;

abstract class ParserAbstract implements ParserInterface
{
    /**
     * @var ResponseInterface $response
     */
    protected $response;

    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function getBody()
    {
        return $this->response->getBody();
    }

    public function getStatusCode()
    {
        return $this->response->getStatusCode();
    }

    abstract public function parse();

    public function grabNewLinksForWholeSiteFetch($host, ResponseInterface $response)
    {

    }
}