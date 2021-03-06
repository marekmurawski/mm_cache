<?php
/**
 * mmCache
 * 
 * Cache plugin for WolfCMS
 *
 * based on works of:
 * 
 * @author Marek Murawski <http://marekmurawski.pl>
 * @author Gilles Doge <gde@antistatique.net>
 * @version 0.0.1
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL 
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
 * IN THE SOFTWARE.
 */

define('MM_CACHE_PLUGIN_DIR', realpath(CORE_ROOT).DS.'plugins'.DS.'mm_cache');

require_once MM_CACHE_PLUGIN_DIR.DS.'lib'.DS.'mmCache.php';

Plugin::setInfos(array(
	'id'          => 'mm_cache',
	'title'       => 'mmFragment and Data Cache',
	'description' => 'Provides universal file cache and output buffer caching functionality',
	'version'     => '0.0.1',
	'license'     => 'GPL',
	'author'      => 'Marek Murawski',
	'website'     => 'http://marekmurawski.pl/',
	//'update_url'  => 'http://dev.antistatique.net/frog/plugin-versions.xml'
));


if (Plugin::isEnabled('mm_cache')) {
	$mm_cache_settings = Plugin::getAllSettings('mm_cache');

	MmCache::getInstance()->init(array(
	'cache_dir' => CMS_ROOT.DS.$mm_cache_settings['dir'],
	'lifetime'  => $mm_cache_settings['default_lifetime'],
	'extension' => '.'.$mm_cache_settings['extension'],
	));


if (defined('CMS_BACKEND')) { // BACKEND PART
	Plugin::addJavascript('mm_cache', 'js/prettify.js');
	
    
    AutoLoader::addFolder(dirname(__FILE__).'/lib');    
    Observer::observe('page_edit_after_save', 'mm_cache_clear');
    Plugin::addController('mm_cache', ' mmCache'); //small little space to avoid tab first-letter-capitalization ;)

    function mm_cache_clear() {
     MmCache::getInstance()->clean('all');
    }
    
    function mm_trim_key($string,$length,$start,$end)
    {
        if ($length<strlen($string)+3) {
         $a = substr($string, 0, $start);
         $b = substr($string, -intval($end));
         return $a . '...' . $b;
        }
        else
        return $string;
        
    }

} else { // FRONTEND PART BELOW

} // end of FRONTEND PART

} // end of if Plugin::isEnabled