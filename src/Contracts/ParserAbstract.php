<?php

namespace Crawler\Contracts;

use Crawler\Core;
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

    public function grabNewUrlsForWholeSiteFetch($siteUrl)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($this->getBody()->__toString());
        $links = $dom->getElementsByTagName('a');

        $host = getUrlHost($siteUrl);
        $urls = [];
        foreach ($links as $link) {
            $url = $link->getAttribute('href');

            if ($url[0] === '/') {
                $urls[] = $siteUrl . $url;
                continue;
            }

            if (hasSameHost($host, $url)){
                $urls[] = $siteUrl . $url;
            }
        }

        foreach ($urls as $key => $url){
            if (app(Core::class)->fetchedLinkPool->isExist($url)){
                unset($urls[$key]);
            }
        }

        app(Core::class)->linkPool->add($urls);
    }
}