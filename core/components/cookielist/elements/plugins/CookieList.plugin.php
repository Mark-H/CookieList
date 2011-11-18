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
if($modx->context->get('key') == 'mgr') return '';

/* Get the CookieList class */
$corePath = $modx->getOption('cookielist.core_path',null,$modx->getOption('core_path').'components/cookielist/');
$cookielist = $modx->getService('cookielist','CookieList',$corePath.'model/');

$cookie = $cookielist->cookiename;
$c = $_COOKIE[$cookie];

/**
* Sets a cookie to test cookie support
*/
if(!$c['cl_check']) {
    setcookie($cookie."[cl_check]", 1, 0, '', false, false);
}

/* Set up the add/remove params. Could be configurable in a future release? */
$addParam = CookieList::addParam;
$removeParam = CookieList::removeParam;
$addValue = (isset($_GET[$addParam])) ? $_GET[$addParam] : null;
$removeValue = (isset($_GET[$removeParam])) ? $_GET[$removeParam] : null;

/* If neither are set we don't have to do anything */
if(!$addValue && !$removeValue) return '';

$cookieName = $cookie."[items]";
$cookieValues = array();
$currentValues = array();
if($c['items']) $currentValues = explode(',', $c['items']);
$value = '';

/**
* Adds an item to the wish list
*/
if($addValue) {
    $cookieValues = array_merge($currentValues, $cookieValues);
    $cookieValues[] = $addValue;
}
/**
* Removes an item for the wish list
*/
if($removeValue) {
    if(in_array($removeValue, $currentValues)) {
        // We want to remove the item
        $position = array_search($removeValue, $currentValues);
        unset($currentValues[$position]);
        $cookieValues = array_values($currentValues);
    }
}
/**
* Check for the cookie support, sets the wish list cookie & redirects
* back to the listing.
*/
if($addValue || $removeValue) {
    if (!$c['cl_check']) {
        $url = $cookielist->url(true);
        $modx->setPlaceholder('cookielist.error',$modx->lexicon('cookielist.err.no_cookies'));
    } else {
        $url = $cookielist->url();
    }
    // Creates/updates the cookie and its value
    $value = implode(',', $cookieValues);
    setcookie($cookieName, $value, 0, '', false, false);
    $modx->sendRedirect($url);
}

return '';