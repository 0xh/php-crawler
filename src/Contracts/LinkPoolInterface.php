<?php

namespace Crawler\Contracts;

interface LinkPoolInterface
{
    /**
     * Add links to the link pool
     *
     * @param array $links
     * @return bool
     */
    public function add(array $urls);

    /**
     * Get several links and mark them as used in the link pool.
     *
     * @return array
     */
    public function pop($limit = 1);

    /**
     * Determine if a link already exists in the link pool.
     *
     * @param string $link
     * @return bool
     */
    public function isExist($url);

    /**
     * Get the pool
     *
     * @return mixed
     */
    public function getPool();
}