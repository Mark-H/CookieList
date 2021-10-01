<?php
/**
 * getCookieList
 *
 * @var $modx modX
 * @var CookieList $cookielist
 *
 * Return the cookielist items, if any, as comma separated list
 */

/* Load the cookielist class */
$corePath = $modx->getOption('cookielist.core_path', null, $modx->getOption('core_path') . 'components/cookielist/');
$cookielist = $modx->getService('cookielist', 'CookieList', $corePath . 'model/');
if (!$cookielist) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed initializing CookieList in the getCookieList snippet');
    return '';
}

$cookie = $cookielist->cookiename;
$c = $_COOKIE[$cookie];

if (isset($c) && isset($c['items'])) {
    return $c['items'];
}

return '';
