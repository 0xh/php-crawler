<?php

namespace Tests;

use Crawler\Core;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    public static function setUpBeforeClass()
    {
        Core::registerContainer();
        app(Core::class)->initialize();
    }

    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
