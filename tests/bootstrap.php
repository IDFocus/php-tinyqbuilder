<?php
error_reporting(E_ALL);
set_error_handler(static function ($level, $message, $file, $line) {
    if ($level & error_reporting()) {
        throw new ErrorException($message, 0, $level, $file, $line);
    }
    return false;
});
require dirname(__DIR__) . '/vendor/autoload.php';
