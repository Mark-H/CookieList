<?php
/**
 * @var modX $modx
 * @var array $scriptProperties
 * @var CookieList $cookielist
 */

/* Load the cookielist class */
$corePath = $modx->getOption('cookielist.core_path', null, $modx->getOption('core_path') . 'components/cookielist/');
$cookielist = $modx->getService('cookielist', 'CookieList', $corePath . 'model/');
if (!$cookielist) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Failed initializing CookieList in the addToCookieList snippet');
    return '';
}

/* Set up the properties to be used */
$value = $modx->getOption('value', $scriptProperties);
$tpl = $modx->getOption('tpl', $scriptProperties, 'cl.addToCookieList');
$addText = $modx->getOption('addText', $scriptProperties);
$removeText = $modx->getOption('removeText', $scriptProperties);
if (empty($value)) {
    $value = $modx->resource->id;
}
if (empty($addText)) {
    $addText = $modx->lexicon('cookielist.add_text');
}
if (empty($removeText)) {
    $removeText = $modx->lexicon('cookielist.remove_text');
}
$cookie = $cookielist->cookiename;
$c = $_COOKIE[$cookie];

$cookieValues = explode(',', $c['items']);
if (in_array($value, $cookieValues)) {
    // We already have this item in the wishlist
    $label = $removeText;
    $params = [
        CookieList::removeParam => $value,
    ];
} else {
    // Item is not yet in the list
    $label = $addText;
    $params = [
        CookieList::addParam => $value,
    ];
}

$split = '?';
if (strstr($_SERVER['REQUEST_URI'], '?')) {
    $split = '&';
}
$link = $_SERVER['REQUEST_URI'] . $split . http_build_query($params);

return $cookielist->getChunk($tpl, ['link' => $link, 'value' => $value, 'label' => $label]);