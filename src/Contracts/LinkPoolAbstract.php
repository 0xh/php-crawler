<?php

namespace Crawler\Contracts;

use Crawler\UrlTree;

abstract class LinkPoolAbstract implements LinkPoolInterface
{
    protected $pool;
    protected $host;
    protected $urlTree;

    public function __construct()
    {
        $this->urlTree = new UrlTree();
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