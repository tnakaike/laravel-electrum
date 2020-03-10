<?php

$host = getenv('ELECTRUM_DAEMON_HOST', true) ?: '127.0.0.1';
$user = getenv('ELECTRUM_DAEMON_USER', true) ?: 'electrum';
$password = getenv('ELECTRUM_DAEMON_PASSWORD', true) ?: '';

$electrumhost = 'http://'.$user.':'.$password.'@'.$host;

return [
    'host'          => $electrumhost,
    'port'          => '7777',
    'web_interface' => [
        'enabled'    => true,
        'currency'   => 'USD',
        'middleware' => ['web', 'auth'],
        'prefix'     => 'electrum',
    ],
];
