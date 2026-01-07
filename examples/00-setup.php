<?php

require __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../config.php')) {
    require __DIR__ . '/../config.php';
} else {
    require __DIR__ . '/../config.sample.php';
}
