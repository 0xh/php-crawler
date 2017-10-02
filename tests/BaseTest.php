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
}
