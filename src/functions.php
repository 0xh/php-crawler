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

if (!function_exists('app')){
    function app($serviceName){
        return \Crawler\Client::$container[$serviceName];
    }
}