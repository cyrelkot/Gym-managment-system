<?php
if (!isset($_GET['token']) || $_GET['token'] !== getenv('OPCACHE_RESET_TOKEN')) {
    http_response_code(403);
    exit('Forbidden');
}
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPcache cleared.';
} else {
    echo 'OPcache not enabled.';
}
