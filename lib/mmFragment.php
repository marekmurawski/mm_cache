<?php

/**
 * mmFragment Class
 * 
 * Inspired by Kohana Fragment helper.
 * 
 * View fragment caching. This is primarily used to cache small parts of a view
 * that rarely change. For instance, you may want to cache the footer of your
 * template because it has very little dynamic content. Or you could cache a
 * user profile page and delete the fragment when the user updates.
 *
 * For obvious reasons, fragment caching should not be applied to any
 * content that contains forms.
 *
 * @author     Marek Murawski <http://marekmurawski.pl>
 * @copyright  (c) 2009-2010 Kohana Team
 * @license    http://kohanaframework.org/license
 */
class mmFragment {

    /**
     * @var  array  list of buffer => cache key
     */
    protected static $_caches = array();

    /**
     * Generate the cache key name for a fragment.
     *
     * @param   string   fragment name
     */
    protected static function _cache_key($name) {
        return '.mmFrag/' . $name;
    }

    /**
     * Load a fragment from cache and display it. Multiple fragments can
     * be nested with different life times.
     *
     *     if ( ! Fragment::load('footer')) {
     *         // Anything that is echo'ed here will be saved
     *         Fragment::save(30); // 30 seconds lifetime for a fragment
     *     }
     *
     * @param <string>   Fragment name
     * @param <integer>  Fragment cache lifetime
     * 
     * @return <boolean>
     */
    public static function load($name) {
        // Get the cache key name
        $cache_key = mmFragment::_cache_key($name);

        if ($fragment = MmCache::getInstance()->get($cache_key)) {
            // Display the cached fragment now
            echo $fragment;
            return TRUE;
        } else {
            // Start the output buffer
            ob_start();
            // Store the cache key by the buffer level
            mmFragment::$_caches[ob_get_level()] = $cache_key;
            return FALSE;
        }
    }

    /**
     * Saves the currently open fragment in the cache.
     *
     * @param <integer>     Current fragment lifetime
     * 
     * @return <void>       
     * 
     */
    public static function save($lifetime = null) {
        
        // Get default lifetime if not specified
        if (!isset($lifetime)) $lifetime = Plugin::getSetting('default_lifetime', 'mm_cache');
        
        // Get the buffer level        
        $level = ob_get_level();

        if (isset(mmFragment::$_caches[$level])) {
            // Get the cache key based on the level
            $cache_key = mmFragment::$_caches[$level];

            // Delete the cache key, we don't need it anymore
            unset(mmFragment::$_caches[$level]);

            // Get the output buffer and display it at the same time
            $fragment = ob_get_flush();

            // Cache the fragment
            MmCache::getInstance()->set($cache_key, $fragment, $lifetime);
        }
    }

    /**
     * Retrieves the timeout.
     *
     * @param  <string>     Fragment name
     *
     * @return <int>        The timeout datetime
     */    
    public static function getTimeout($name) {
        return mmCache::getInstance()->getTimeout(mmFragment::_cache_key($name));
    }

}

// End Fragment
