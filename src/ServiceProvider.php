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

        $container[ProcessingLinkPool::class] = function ($c) {
            return new ProcessingLinkPool();
        };
        $container[LinkPool::class] = $container->factory(function ($c) {
            return new LinkPool();
        });
        $container[Request::class] = function ($c) {
            return new Request();
        };
        $container[ProcessControl::class] = function ($c) {
            return new ProcessControl();
        };
    }
}