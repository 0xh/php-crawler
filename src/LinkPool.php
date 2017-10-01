<?php

namespace Crawler;

use Crawler\Contracts\LinkPoolAbstract;

class LinkPool extends LinkPoolAbstract
{
    public function pop($limit = 1)
    {
        $urls = parent::pop($limit);

        app(Core::class)->fetchedLinkPool->add($urls);

        return $urls;
    }
}