<?php

require dirname(__DIR__).'/vendor/autoload.php';

define('SAAZE_PATH', __DIR__ . '/_data');
define('SAAZE_ENTRIES_PER_PAGE', 10);

if (file_exists(SAAZE_PATH . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable(SAAZE_PATH);
    $dotenv->load();
}
