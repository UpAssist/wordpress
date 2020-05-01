<?php
require_once __DIR__ . '/vendor/autoload.php';

$theme = [];
$theme['name'] = 'upassist';
$theme['version'] = '1.0.0';
$theme['stylesheets'] = [
    [
        'handle' => 'main',
        'file' => 'assets/styles/main.css',
        'depends_on' => null,
        'version' => $theme['version'],
        'media' => 'all'
    ]
];
$theme['headerScripts'] = [];
$theme['bodyScripts'] = [];

$upassist = new \UpAssist\WordPress\Theme($theme['name'], 'nl_NL', null, $theme['stylesheets']);
