<?php
/**
 * CookieList
 *
 * Plugin to handle the cookie creation of the whishlist items
 *
 * @var modX $modx
 * @var CookieList $cookielist
 *
 * @event OnHandleRequest
 */

/* Do not trigger in the manager */
if ($modx->context->get('key') == 'mgr') {
    return '';
}

/* Get the CookieList class */
$corePath = $modx->getOption('cookielist.core_path', null, $modx->getOption('core_path') . 'components/cookielist/');
$cookielist = $modx->getService('cookielist', 'CookieList', $corePath . 'model/');
if (!$cookielist) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed initializing CookieList in the CookieList plugin');
    return;
}

$cookie = $cookielist->cookiename;
$c = $_COOKIE[$cookie];

$cookiePath = $modx->getOption('session_cookie_path', null, '/');
$cookieDomain = $modx->getOption('session_cookie_domain', null, '');
$cookieSecure = (bool)$modx->getOption('session_cookie_secure', null, false); // default false for BC reasons; CookieList 1.0 specifically hardcoded false
$cookieHttpOnly = (bool)$modx->getOption('session_cookie_httponly', null, false); // default false for BC reasons; CookieList 1.0 specifically hardcoded false

/**
 * Sets a cookie to test cookie support
 */
if (!$c['cl_check']) {
    setcookie($cookie . "[cl_check]", 1, 0, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
}
if ($_GET['cl_error']) {
    $modx->setPlaceholder('cookielist.error', $modx->lexicon('cookielist.err.no_cookies'));
}

/* Set up the add/remove params. Could be configurable in a future release? */
$addParam = CookieList::addParam;
$removeParam = CookieList::removeParam;
$addValue = (isset($_GET[$addParam])) ? $_GET[$addParam] : null;
$removeValue = (isset($_GET[$removeParam])) ? $_GET[$removeParam] : null;

/* If neither are set we don't have to do anything */
if (!$addValue && !$removeValue) {
    return '';
}

$cookieName = $cookie . "[items]";
$cookieValues = array();
$currentValues = array();
if ($c['items']) {
    $currentValues = explode(',', $c['items']);
}
$value = '';

/**
 * Adds an item to the wish list
 */
if ($addValue) {
    $cookieValues = array_merge($currentValues, $cookieValues);
    $cookieValues[] = $addValue;
}

/**
 * Removes an item for the wish list
 */
if ($removeValue && in_array($removeValue, $currentValues)) {
    $position = array_search($removeValue, $currentValues);
    unset($currentValues[$position]);
    $cookieValues = array_values($currentValues);
}

/**
 * Check for the cookie support, sets the wish list cookie & redirects
 * back to the listing.
 */
if ($addValue || $removeValue) {
    $error = !isset($c['cl_check']);
    $url = $cookielist->url($error);
    // Creates/updates the cookie and its value
    $value = implode(',', $cookieValues);
    $duration = time() + $modx->getOption('cookielist.cookie.duration', null, 2592000);
    setcookie($cookieName, $value, $duration, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
    $modx->sendRedirect($url);
}

return '';