<?php

use Dotenv\Dotenv;

require dirname(__DIR__).'/vendor/autoload.php';

$dotenv = Dotenv::create(__DIR__.'/..');
$dotenv->load();