<?php

namespace Crawler;

class UrlTrees
{
    public static $treeEnd = [
        'value' => 0,
        'subs' => [],
    ];
    public static $treeBranch = [
        'subs' => [],
    ];

    public function unsetUrl(&$pool, $url)
    {
        $urlPaths = $this->urlToPathList($url);
        $this->unsetUrlCircularly($pool, $urlPaths);
    }

    /**
     * Create a URL trapezoid array.
     *
     */
    public function urlToPathList($url)
    {
        $urlComponents = $this->parseUrl($url);
        $pathUrls = [];

        $path = [];
        foreach ($urlComponents as $urlComponent) {
            $path[] = $urlComponent;
            $pathUrls[] = $path;
        }

        return array_reverse($pathUrls);
    }

    public function unsetUrlCircularly(&$pool, array $urlPaths)
    {
        $firstUrlPath = array_shift($urlPaths);
        $this->unsetNode($pool, $firstUrlPath, true);

        foreach ($urlPaths as $urlPath) {
            $this->unsetNode($pool, $urlPath);
        }

        if (empty($pool['subs'])){
            $pool = [];
        }
    }

    /**
     * Unset node.
     *
     * @param $pool
     * @param array $urlPath
     * @param bool $endNode whether going to remove the end node.
     * @return mixed
     */
    public function unsetNode(&$pool, array $urlPath, $endNode = false)
    {
        $lastValue = array_pop($urlPath);

        $cursor = &$pool;
        foreach ($urlPath as $segment) {
            if (!isset($cursor['subs'][$segment])) {
                return $cursor;
            }

            $cursor = &$cursor['subs'][$segment];
        }

        if ($endNode) {
            if (isset($cursor['subs'][$lastValue]['value'])) {
                unset($cursor['subs'][$lastValue]['value']);
            }
        }

        if (!isset($cursor['subs'][$lastValue]['value']) &&
            empty($cursor['subs'][$lastValue]['subs'])
        ) {
            unset($cursor['subs'][$lastValue]);
        }
    }

    /**
     * Complement and split URL into array
     *
     * @param string $url
     * @return array
     */
    protected function parseUrl($url)
    {
        $urlComponents = parse_url($url);

        $data = [];
        foreach ($urlComponents as $key => $urlComponent) {
            if ($key == 'scheme') {
                $data[] = $urlComponent . '://';
            } else if ($key == 'path') {
                $temp = $this->parseUrlPath($urlComponent);
                $data = array_merge($data, $temp);
            } else {
                $data[] = $urlComponent;
            }

        }

        return array_values($data);
    }


//    $test->add($pool, 'http://quotes.toscrape.com/page/1/');
//    $test->add($pool, 'http://quotes.toscrape.com/page/2/');
//    $test->add($pool, 'http://quotes.toscrape.com/homepage')
    protected function parseUrlPath($path)
    {
        $data = [];
        $status = true;
        while ($status == true) {
            $slashPose = strpos($path, '/');

            // If not matched slash
            if ($slashPose === false) {
                $status = false;
                if (strlen($path) > 0) {
                    $data[] = $path;
                }
                continue;
            }

            $data[] = substr($path, 0, $slashPose + 1);
            $path = substr($path, $slashPose + 1);
        }

        return $data;
    }

    /**
     * Add a URL to the pool.
     *
     * @param array $pool
     * @param string $url
     * @return bool
     */
    public function add(&$pool, $url)
    {
        $urlComponents = $this->parseUrl($url);

        $cursor = &$pool;
        $componentCount = count($urlComponents);

        //If URL is invalid.
        if ($componentCount < 1) {
            return false;
        }

        foreach ($urlComponents as $key => $urlComponent) {

            // if it is the end component.
            if ($key == $componentCount - 1) {
                $cursor['subs'][$urlComponent]['value'] = 0;
                continue;
            }

            if (!isset($cursor['subs'][$urlComponent])) {
                $cursor['subs'][$urlComponent] = self::$treeBranch;
            }
            $cursor = &$cursor['subs'][$urlComponent];
        }

        return true;
    }

    public function get(&$pool)
    {
        $urlTracer = $this->tracerUrl($pool);
        return implode('', $urlTracer);
    }

    /**
     * Get the path stack of the URL by packing a URL in the pool.
     *
     * @param array $pool
     * @return array
     */
    public function tracerUrl(array &$pool)
    {
        if (empty($pool)){
            return [];
        }

        if (isset($pool['value'])) {
            return [];
        }

        if (isset($pool['subs'])) {
            return $this->tracerUrl($pool['subs']);
        }

        $currentKey = key($pool);
        $stack[] = $currentKey;

        $temp = $this->tracerUrl($pool[$currentKey]);
        if (empty($temp)) {
            return $stack;
        }
        return array_merge($stack, $temp);
    }

    /**
     * Get the URL structure in a pool.
     * @param $url
     * @return array
     */
    protected function expandUrlToArray($url)
    {
        $urlComponents = $this->parseUrl($url);
        $path = [];

        $cursor = &$path;
        $count = count($urlComponents);

        for ($i = 0; $i < $count; $i++) {
            $cursor['subs'][$urlComponents[$i]] = [];
            $cursor = &$cursor['subs'][$urlComponents[$i]];
        }

        $cursor = self::$treeEnd;

        return $path;
    }

    protected function urlToPath($url)
    {
        $urlComponents = $this->parseUrl($url);
        $path = [];
        $count = count($urlComponents);

        for ($i = 0; $i < $count; $i++) {
            $path[] = 'subs';
            $path[] = $urlComponents[$i];
        }

        return $path;
    }

    public function isUrlExisted(&$pool, $url)
    {
        $urlPath = $this->urlToPath($url);
        $cursor = &$pool;

        foreach ($urlPath as $segment) {
            if (!isset($cursor[$segment])) {
                return false;
            }

            $cursor = &$cursor[$segment];
        }

        if (!isset($cursor['value']) || $cursor['value'] != 0) {
            return false;
        }

        return true;
    }

}