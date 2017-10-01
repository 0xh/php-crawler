<?php
if (is_file($autoload = __DIR__ . '/../vendor/autoload.php')) {
    require_once $autoload;
} elseif (is_file($autoload = __DIR__ . '/../../../autoload.php')) {
    require_once $autoload;
} else {
    echo "File autoload.php is missing, please update the composer.\n";
    exit(2);
}
unset($autoload);
