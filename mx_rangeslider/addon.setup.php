<?php

$addonJson = json_decode(file_get_contents(__DIR__ . '/addon.json'));

if (!defined('MX_RANGESLIDER_NAME')) {
    define('MX_RANGESLIDER_NAME', $addonJson->name);
    define('MX_RANGESLIDER_VERSION', $addonJson->version);
    define('MX_RANGESLIDER_DOCS', '');
    define('MX_RANGESLIDER_DESCRIPTION', $addonJson->description);
    define('MX_RANGESLIDER_DEBUG', false);
}

return [
    'name' => $addonJson->name,
    'description' => $addonJson->description,
    'version' => $addonJson->version,
    'namespace' => $addonJson->namespace,
    'author' => 'Max Lazar',
    'author_url' => 'https://eecms.dev',
    'settings_exist' => true,
    // Advanced settings
    'fieldtypes'         => array(
        'RangeSliderField' => array(
            'name' => 'Range Slider'
        )
    )
];
