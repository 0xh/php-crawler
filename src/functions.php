<?php

if (!function_exists('dd')) {
    function dd($params)
    {
        if (!is_array($params) && !is_object($params)) {
            var_dump($params);
        }
        print_r($params);
        die();
    }
}

if (!function_exists('app')) {
    function app($serviceName)
    {
        return \Crawler\Core::$container[$serviceName];
    }
}

if (!function_exists('getUrlHost')) {
    function getUrlHost($url)
    {
        $components = parse_url($url);

        if (isset($components['host'])) {
            return $components['host'];
        }

        return $url;
    }
}

if (!function_exists('getUrlSite')) {
    function getUrlSite($url)
    {
        $components = parse_url($url);

        if (isset($components['scheme']) && isset($components['host'])) {
            return $components['scheme'] . '://' . $components['host'];
        }

        return $url;
    }
}

if (!function_exists('hasSameHost')) {
    function hasSameHost($host, $url)
    {
        $urlHost = getUrlHost($url);
        return $host === $urlHost;
    }
}