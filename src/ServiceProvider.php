<?php

namespace Crawler;

class ServiceProvider
{
    public function register()
    {
        $container = Client::$container;
        $container[LinkPool::class] = function ($c){
            return new LinkPool();
        };
        $container[FetchedLinkPool::class] = function ($c){
            return new FetchedLinkPool();
        };
        $container[Request::class] = function ($c){
            return new Request();
        };
    }
}