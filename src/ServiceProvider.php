<?php

namespace Crawler;

class ServiceProvider
{
    public function register()
    {
        $container = Core::$container;

        $container[Core::class] = function ($c) {
            return new Core();
        };

        $container[LinkPool::class] = function ($c) {
            return new LinkPool();
        };
        $container[FetchedLinkPool::class] = function ($c) {
            return new FetchedLinkPool();
        };
        $container[Request::class] = function ($c) {
            return new Request();
        };
        $container[ProcessControl::class] = function ($c) {
            return new ProcessControl();
        };
    }
}