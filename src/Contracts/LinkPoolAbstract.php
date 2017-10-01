<?php

namespace Crawler\Contracts;

use Crawler\UrlTrees;

abstract class LinkPoolAbstract implements LinkPoolInterface
{
    protected $pool;
    protected $host;
    protected $urlTree;

    public function __construct()
    {
        $this->urlTree = new UrlTrees();
    }

    public function pop($limit = 1)
    {
        $urls = [];

        for ($i = 0; $i < $limit; $i++) {
            $url = $this->urlTree->get($this->pool);

            if (empty($url)){
                return $urls;
            }

            $this->urlTree->unsetUrl($this->pool, $url);
            $urls[] = $url;
        }

        return $urls;
    }

    public function add(array $urls)
    {
        foreach ($urls as $url) {
            $this->urlTree->add($this->pool, $url);
        }
        // Get URL host at the first time.
//        if (is_null($this->host)) {
//            $urlParameters = parse_url($links[0]);
//            $this->host = $urlParameters['scheme'] . '://' . $urlParameters['host'];
//        }
//
//        foreach ($links as $url) {
//            if (strpos($url, $this->host) === false){
//                throw new ParameterException('Invalid URL: ' . $url);
//            }
//
//            $this->pool[] = $url;
//        }
    }

    public function isExist($url)
    {
        $this->urlTree->isUrlExisted($this->pool, $url);
    }

    public function getPool()
    {
        return $this->pool;
    }
}