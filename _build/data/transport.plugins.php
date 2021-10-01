<?php
$plugins = array();

/* create the plugin object */
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id',1);
$plugins[0]->set('name','CookieList');
$plugins[0]->set('description','Processes requests that add/remove items to the cookie list. Also checks if cookies are enabled by setting a test cookie which is checked when adding/removing from the list.');
$plugins[0]->set('plugincode', getSnippetContent($sources['plugins'] . 'CookieList.plugin.php'));
$plugins[0]->set('category', 0);

$events = array();

$events['OnHandleRequest']= $modx->newObject('modPluginEvent');
$events['OnHandleRequest']->fromArray([
    'event' => 'OnHandleRequest',
    'priority' => 0,
    'propertyset' => 0,
],'',true,true);

if (is_array($events) && !empty($events)) {
    $plugins[0]->addMany($events);
    $modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events for CookieList.'); flush();
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events for CookieList!');
}
unset($events);

return $plugins;

?>