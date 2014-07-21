<?php

// PHP 5.3.0 minimum
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    die('This software requires PHP 5.3.0 minimum.');
}

// Check if folders are writeable
if (! is_writable(__DIR__.'/../app/brains')) {
    die('The directory "app/brains" must be writeable by your web server user.');
}
