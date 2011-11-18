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
if($modx->context->get('key') == 'mgr') return true;

/* Set up the add/remove params. Could be configurable in a future release? */
$addParam = 'cl_add';
$removeParam = 'cl_remove';
$addValue = (isset($_GET[$addParam])) ? $_GET[$addParam] : null;
$removeValue = (isset($_GET[$removeParam])) ? $_GET[$removeParam] : null;

/* If neither are set we don't have to do anything */
if(!$addValue || !$removeValue) return true;

/* Get the CookieList class */
$corePath = $modx->getOption('cookielist.core_path',null,$modx->getOption('core_path').'components/cookielist/');
$cookielist = $modx->getService('cookielist','CookieList',$corePath.'model/');

$cookie = $cookielist->cookiename;

$c = $_COOKIE[$cookie];
$cookieName = $cookie."[items]";
$cookieValues = array();
$currentValues = array();
if($c['items']) $currentValues = explode(',', $c['items']);
$value = '';

/**
* Sets a cookie to test cookie support
*/
if(!$c['cookie_check']) {
    setcookie($cookie."[cookie_check]", 1, 0, '', false, false);
}
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
    // @TODO: check for $c['cookie_check']
    // Creates/updates the cookie and its value
    $value = implode(',', $cookieValues);
    setcookie($cookieName, $value, 0, '', false, false);
    
    $modx->sendRedirect($cookielist->url());
}

return '';