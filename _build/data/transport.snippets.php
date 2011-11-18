<?php
$snips = array(
    'addToCookieList' => 'Creates an add to / remove from CookieList link.',
    'getCookieList' => 'Gets a comma delimited list of stored items to the CookieList.',
);

$snippets = array();
$idx = 0;

foreach ($snips as $sn => $sdesc) {
    $idx++;
    $snippets[$idx] = $modx->newObject('modSnippet');
    $snippets[$idx]->fromArray(array(
       'id' => $idx,
       'name' => $sn,
       'description' => $sdesc . ' (Part of CookieList)',
       'snippet' => getSnippetContent($sources['snippets'].$sn.'.snippet.php')
    ));

    $snippetProperties = array();
    $props = include $sources['snippets'].$sn.'.properties.php';
    foreach ($props as $key => $value) {
        if (is_string($value) || is_int($value)) { $type = 'textfield'; }
        elseif (is_bool($value)) { $type = 'combo-boolean'; }
        else { $type = 'textfield'; }
        $snippetProperties[] = array(
            'name' => $key,
            'desc' => 'cookielist.prop_desc.'.$key,
            'type' => $type,
            'options' => '',
            'value' => ($value != null) ? $value : '',
            'lexicon' => 'cookielist:properties'
        );
    }

    if (count($snippetProperties) > 0)
        $snippets[$idx]->setProperties($snippetProperties);
}

return $snippets;

?>
