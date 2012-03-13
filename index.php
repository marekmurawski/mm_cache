<?php
/**
 * Cache plugin for WolfCMS
 *
 * @author Gilles Doge <gde@antistatique.net>
 * @version 0.1 - SVN: $Id: index.php 144 2008-09-30 09:15:30Z gde $
 */
define('MM_CACHE_PLUGIN_DIR', realpath(CORE_ROOT).DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'mm_cache');

require_once MM_CACHE_PLUGIN_DIR.DIRECTORY_SEPARATOR.'lib/mmCache.php';

Plugin::setInfos(array(
	'id'          => 'mm_cache',
	'title'       => 'mmFragment and Data Cache',
	'description' => 'Provides universal file cache and output buffer caching functionality',
	'version'     => '0.0.1',
	'license'     => 'MIT',
	'author'      => 'Marek Murawski',
	'website'     => 'http://marekmurawski.pl/',
	//'update_url'  => 'http://dev.antistatique.net/frog/plugin-versions.xml'
));


// Configure mmCache
$dir = trim(Plugin::getSetting('dir', 'mm_cache'),'/\\'); // trim slashes from start and end of dir
$cdl = intval(Plugin::getSetting('default_lifetime', 'mm_cache'));
$cex = ltrim(Plugin::getSetting('extension', 'mm_cache'),'.');
MmCache::getInstance()->init(array(
   'cache_dir' => CMS_ROOT.DS.$dir,
   'lifetime'  => $cdl,
   'extension' => '.'.$cex,
));

if (defined('CMS_BACKEND')) { // BACKEND PART

    AutoLoader::addFolder(dirname(__FILE__).'/lib');
    
    Observer::observe('page_edit_after_save', 'mm_cache_clear');

    Plugin::addController('mm_cache', ' mmCache'); //small little space to avoid tab first-letter-capitalization ;)

    function mm_cache_clear() {
    //  mmCache::getInstance()->
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