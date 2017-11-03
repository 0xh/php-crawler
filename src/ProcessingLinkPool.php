<?php

namespace Crawler;

use Crawler\Contracts\LinkPoolAbstract;

class ProcessingLinkPool extends LinkPoolAbstract
{
    public function pop($limit = 1)
    {
        $urls = parent::get($limit);

        app(Core::class)->fetchedLinkPool->add($urls);

        return $urls;
    }
}