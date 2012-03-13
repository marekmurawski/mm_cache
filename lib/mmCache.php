<?php

/**
 * File cache plugin for frog CMS
 * 
 * Adapted for universal cache plugin with serialized data storage by
 * Marek Murawski
 * 
 * Copyright (c) 2008, Gilles Doge
 * 
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @author Marek Murawski <http://www.marekmurawski.pl> 
 * @copyright 2012 Marek Murawski
 * 
 * @author Gilles Doge <gde@antistatique.net>
 * @copyright 2008 Gilles Doge
 * @package plugins/cache
 * @version 0.0.1
 */
require_once MM_CACHE_PLUGIN_DIR . DS . 'lib' . DS . 'mmFileCache.php';
require_once MM_CACHE_PLUGIN_DIR . DS . 'lib' . DS . 'mmFragment.php';

/**
 * mmCache singleton
 *
 * @package plugins/mm_cache
 * @author  Marek Murawski <http://www.marekmurawski.pl>
 * 
 */
class mmCache {

    protected static $instance = null;
    protected $cache = null;

    private function __construct() {
        $this->init();
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            $mmclass = __CLASS__;
            self::$instance = new $mmclass();
        }

        return self::$instance;
    }

    public function init($options = array()) {
        $this->cache = new mmFileCache($options);
    }

    /**
     * Sets the cache content.
     *
     * @param <string>      Cache key name
     * @param <mixed>       Data to put in the cache
     * @param <int>         Custom lifetime for cache entry
     * @param <boolean>     True if data should be serialized
     *
     * @return <boolean>    True, if the data get set successfully otherwise false
     */
    public function set($key, $data, $lifetime = null, $serialize = true) {
        try {
            if ($serialize) {
                $data = serialize($data);
            }

            $this->cache->set($this->getCacheKey($key), $data, $lifetime);
        } catch (CacheException $e) {
            return false;
        }

        return true;
    }

    /**
     * Retrieves content in the cache.
     *
     * @param <string>      Internal uniform resource identifier
     * @param <boolean>     True if data should be unserialized
     *
     * @return <string>     The content in the cache
     */
    public function get($key, $unserialize = true) {
        $return = $this->cache->get($this->getCacheKey($key), false);
        if ($unserialize) {
            $return = unserialize($return);
        }

        return $return;
    }

    /**
     * Returns true if there is a cache.
     *
     * @param <string>      Cache key
     *
     * @return <boolean>    true, if there is a cache otherwise false
     */
    public function has($key) {
        return $this->cache->has($this->getCacheKey($key));
    }

    /**
     * Removes cache entry for a given key.
     *
     * @param <string>      Cache key
     *
     * @return <boolean>    true, if successfully deleted cache file
     */
    public function remove($key) {
        return $this->cache->remove($this->getCacheKey($key));
    }

    /**
     * Retrieves the timeout.
     *
     * @param  <string>     Cache key
     *
     * @return <int>        The timeout datetime
     */
    public function getTimeout($key) {
        return $this->cache->getTimeout($this->getCacheKey($key));
    }

    /**
     * Cleans the cache.
     *
     * @param <string>      The clean mode
     *                      'all': remove all keys (default)
     *                      'old': remove all expired keys
     *
     * @return <boolean>    true if no problem
     */
    public function clean($mode = 'all') {
        return $this->cache->clean($mode);
    }

    /**
     * Generates a unique cache key for a string.
     * 
     *
     * @param  <string> The internal cache key
     *
     * @return <string> The cache key
     */
    public function getCacheKey($name) {
        $cacheKey = sprintf('/%s', $name);

        // replace multiple /
        $cacheKey = preg_replace('#/+#', '/', $cacheKey);

        return $cacheKey;
    }

}