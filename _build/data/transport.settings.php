<?php
$s = array(
    'cookie.duration' => 2592000,
    'cookie.name' => 'cookieList',
);

$settings = array();

foreach ($s as $key => $value) {
    if (is_string($value) || is_int($value)) { $type = 'textfield'; }
    elseif (is_bool($value)) { $type = 'combo-boolean'; }
    else { $type = 'textfield'; }

    $area = 'default';
    $settings['cookielist.'.$key] = $modx->newObject('modSystemSetting');
    $settings['cookielist.'.$key]->set('key', 'cookielist.'.$key);
    $settings['cookielist.'.$key]->fromArray(array(
        'value' => $value,
        'xtype' => $type,
        'namespace' => 'cookielist',
        'area' => $area
    ));
}

return $settings;