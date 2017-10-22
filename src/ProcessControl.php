<?php

namespace Crawler;

class ProcessControl
{
    public static $running = true;

    public function __construct()
    {
        if (!function_exists('pcntl_signal')) {
            printf("PHP extension pcntl is required.", PHP_EOL);
            exit(1);
        }

        // SIGINT == CONTROL+C
        pcntl_signal(SIGINT, array($this, 'signalHandler'));
    }

    public function signalHandler($signalNumber)
    {
        self::$running = false;
        printf("Terminating server…%s", PHP_EOL);
    }

    public static function terminate($message = '')
    {
        if (empty($message)) {
            $message = 'Script terminated successfully';
        }

        printf("$message%s", PHP_EOL);
        exit();
    }
}