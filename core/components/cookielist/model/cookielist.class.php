<?php
/**
 * CookieList
 *
 * Copyright 2011 by Mark Hamstra <hello@markhamstra.com>
 *
 * This file is part of CookieList, a real estate property listings component
 * for MODX Revolution.
 *
 * CookieList is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * CookieList is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * CookieList; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 *
*/

class CookieList {
    public $modx;
    public $config = array();
    private $chunks = array();

    public $cookiename;
    const addParam = 'cl_add';
    const removeParam = 'cl_remove';

    /**
     * Main CookieList constructor for setting up configuration etc.
     *
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
 
        $basePath = $this->modx->getOption('cookielist.core_path',$config,$this->modx->getOption('core_path').'components/cookielist/');
        $assetsUrl = $this->modx->getOption('cookielist.assets_url',$config,$this->modx->getOption('assets_url').'components/cookielist/');
        $assetsPath = $this->modx->getOption('cookielist.assets_path',$config,$this->modx->getOption('assets_path').'components/cookielist/');
        $this->config = array_merge(array(
            'base_bath' => $basePath,
            'core_path' => $basePath,
            'model_path' => $basePath.'model/',
            'processors_path' => $basePath.'processors/',
            'elements_path' => $basePath.'elements/',
            'assets_path' => $assetsPath,
            'js_url' => $assetsUrl.'js/',
            'css_url' => $assetsUrl.'css/',
            'assets_url' => $assetsUrl,
            'connector_url' => $assetsUrl.'connector.php',
        ),$config);

        $this->modx->lexicon->load('cookielist:default');

        $this->cookiename = $modx->getOption('cookielist.cookie.name') ? $modx->getOption('cookielist.cookie.name') : 'cookieList';
    }

    /**
     * Optional context specific initialization.
     *
     * @param string $ctx Context name
     * @return bool
     */
    public function initialize($ctx = 'web') {
        switch ($ctx) {
            case 'mgr':
            break;
        }
        return true;
    }

    /**
    * Gets a Chunk and caches it; also falls back to file-based templates
    * for easier debugging.
    *
    * @author Shaun McCormick
    * @access public
    * @param string $name The name of the Chunk
    * @param array $properties The properties for the Chunk
    * @return string The processed content of the Chunk
    */
    public function getChunk($name,$properties = array()) {
        $chunk = null;
        if (!isset($this->chunks[$name])) {
            $chunk = $this->modx->getObject('modChunk',array('name' => $name),true);
            if (empty($chunk)) {
                $chunk = $this->_getTplChunk($name);
                if ($chunk == false) return false;
            }
            $this->chunks[$name] = $chunk->getContent();
        } else {
            $o = $this->chunks[$name];
            $chunk = $this->modx->newObject('modChunk');
            $chunk->setContent($o);
        }
        $chunk->setCacheable(false);
        return $chunk->process($properties);
    }

    /**
    * Returns a modChunk object from a template file.
    *
    * @author Shaun McCormick
    * @access private
    * @param string $name The name of the Chunk. Will parse to name.chunk.tpl
    * @param string $postFix The postfix to append to the name
    * @return modChunk/boolean Returns the modChunk object if found, otherwise
    * false.
    */
    private function _getTplChunk($name,$postFix = '.tpl') {
        $chunk = false;
        $f = $this->config['elements_path'].'chunks/'.$name.$postFix;
        if (file_exists($f)) {
            $o = file_get_contents($f);
            /* @var modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk');
            $chunk->set('name',$name);
            $chunk->setContent($o);
        }
        return $chunk;
    }

    public function strleft($s1, $s2) {
        return substr($s1, 0, strpos($s1, $s2));
    }

    /**
     * @param $uri
     * @param bool $error
     * @return string - the $_GET queries without the ones created by addToCookieList
     */
    public function cleanParams($uri, $error = false) {
		$uri = str_replace(
			array(
				CookieList::addParam.'='.$_GET[CookieList::addParam],
				CookieList::removeParam.'='.$_GET[CookieList::removeParam],
				'cl_error=1'
			),
			'',
			$uri
		);
		$uri = str_replace(array('&&','?&'),array('&','?'),$uri);
		$uri = trim($uri,'?&');
        if ($error) {
            if (strstr($uri,'?')) $uri .= '&cl_error=1';
            else $uri .= '?cl_error=1';
        }
        return $uri;
    }

    /**
     * @param bool $error
     * @return string - url to sendRedirect to (referer)
     */
    public function url($error = false) {
        $s = empty($_SERVER['HTTPS']) ? '' : 's';
        $protocol = $this->strleft(strtolower($_SERVER['SERVER_PROTOCOL']), '/').$s;
        $port = $_SERVER['SERVER_PORT'] == '80' ? '' : ':'.$_SERVER['SERVER_PORT'];
        $params = $this->cleanParams($_SERVER['REQUEST_URI'],$error);
        return $protocol.'://'.$_SERVER['SERVER_NAME'].$port.$params;
    }

}
