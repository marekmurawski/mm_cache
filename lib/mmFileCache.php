<?php

/**
 * Basic implementation of a file cache system.
 * Inspired from the symfony-project cache system.
 *
 * @package plugins/mm_cache
 * @author Gilles Doge <gde@antistatique.net>
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version SVN: $Id: FileCache.php 111 2008-08-06 18:24:53Z gde $
 * */
class MmFileCache {

    const READ_DATA = 1;
    const READ_TIMEOUT = 2;
    const READ_LAST_MODIFIED = 4;
    const EXTENSION = '.cache';
    const SEPARATOR = ':';

    protected
            $cache_dir = '',
            $extension = '.cache',
            $default_lifetime = 3600,
            $automatic_cleaning_factor = 1000;

    public function __construct($options = array()) {
        $this->init($options);
    }

    /**
     * Initialize File Cache
     *
     * @param array $options (optional):
     *                 - lifetime  : the default lifetime for cache file (in second)
     *                 - cache_dir : path to the cache directory
     *                 - automatic_cleaning_factor : integer to define to the automatic cleaning factor.
     *                                               Set to 0 to disable this functionality
     * @return void
     * */
    public function init($options = array()) {
        if (isset($options['extension'])) {
            $this->extension = $options['extension'];
        }
        if (isset($options['lifetime'])) {
            $this->default_lifetime = $options['lifetime'];
        }
        if (isset($options['automatic_cleaning_factor'])) {
            $this->automatic_cleaning_factor = $options['automatic_cleaning_factor'];
        }

        // default cache directory
        $cache_dir = CMS_ROOT . DS . 'mm_cache';
        if (isset($options['cache_dir'])) {
            $cache_dir = $options['cache_dir'];
        }

        $this->setCacheDir($cache_dir);
    }

    /**
     * Gets the cache content for a given key.
     *
     * @param  string The cache key
     * @param  mixed  The default value is the key does not exist or not valid anymore
     *
     * @return mixed  The data of the cache
     */
    public function get($key, $default = null) {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->read($this->getFilePath($key));
    }

    /**
     * Returns true if there is a cache for the given key.
     *
     * @param  string  The cache key
     *
     * @return Boolean true if the cache exists, false otherwise
     */
    public function has($key) {
        return file_exists($this->getFilePath($key)) && time() < $this->getTimeout($key);
    }

    /**
     * Saves some data in the cache.
     *
     * @param string The cache key
     * @param mixed  The data to put in cache
     * @param int    The lifetime
     *
     * @return Boolean true if no problem
     */
    public function set($key, $data, $lifetime = null) {
        if ($this->automatic_cleaning_factor > 0 && rand(1, $this->automatic_cleaning_factor) == 1) {
            $this->clean('old');
        }

        return $this->write($this->getFilePath($key), $data, time() + $this->getLifetime($lifetime));
    }

    /**
     * Removes a content from the cache.
     *
     * @param string The cache key
     *
     * @return Boolean true if no problem
     */
    public function remove($key) {
        return @unlink($this->getFilePath($key));
    }

    /**
     * Cleans the cache.
     *
     * @param  string  The clean mode
     *                 'all': remove all keys (default)
     *                 'old': remove all expired keys
     *
     * @return Boolean true if no problem
     */
    public function clean($mode = 'all') {
        if (!is_dir($this->cache_dir)) {
            return true;
        }

        $result = true;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cache_dir)) as $file) {
            if (!endsWith($file, '.htaccess') && !endsWith($file, '.')) {
                if ('all' == $mode || time() > $this->read($file, self::READ_TIMEOUT)) {
                    if (is_dir($file)) {
                        $res = @rmdir($file);
                    } else {
                        $res = @unlink($file);
                    }
                    $result = $result && $res;
                }
            }
        }
        return $result;
    }

    /**
     * Cleans the cache by searching substring in cache filenames.
     *
     * @param  string  The clean mode
     *
     * @return Boolean true if no problem
     */
    public function cleanByName($name = NULL) {
        //echo $name . '<br/>';
        //echo 'elo';

        if (!is_dir($this->cache_dir)) {
            return false;
        }
        $pos = strlen(CMS_ROOT . DS . Plugin::getSetting('dir', 'mm_cache'));
        $count = 0;
        $result = true;
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cache_dir)) as $file) {
                if (is_dir($file)) {
                    $res = @rmdir($file);
                } else {
                    $key = substr($file, $pos);
                    if (strpos($key, $name)) {
                        $res = @unlink($file);
                        $count++;
                        echo $file . '<br/>'; $res = true;
                    }
                }
                //$result = $result && $res;
        }
        //return $result;
        //die();
        if ($count > 0) return $count; else return false;

    }

    /**
     * Returns the timeout for the given key.
     *
     * @param string The cache key
     *
     * @return int The timeout time
     */
    public function getTimeout($key) {
        $path = $this->getFilePath($key);

        if (!file_exists($path)) {
            return 0;
        }

        $timeout = $this->read($path, self::READ_TIMEOUT);

        return $timeout < time() ? 0 : $timeout;
    }

    /**
     * Returns the last modification date of the given key.
     *
     * @param string The cache key
     *
     * @return int The last modified time
     */
    public function getLastModified($key) {
        $path = $this->getFilePath($key);

        if (!file_exists($path) || $this->read($path, self::READ_TIMEOUT) < time()) {
            return 0;
        }

        return $this->read($path, self::READ_LAST_MODIFIED);
    }

    /**
     * Computes lifetime.
     *
     * @param  integer Lifetime in seconds
     *
     * @return integer Lifetime in seconds
     */
    public function getLifetime($lifetime = null) {
        return is_null($lifetime) ? $this->default_lifetime : $lifetime;
    }

    /**
     * set the cache directory and create it if not exist
     *
     * @param string path the the cache directory
     *
     * @return void
     * */
    public function setCacheDir($cache_dir) {
        // remove last DIRECTORY_SEPARATOR
        if (DIRECTORY_SEPARATOR == substr($cache_dir, -1)) {
            $cache_dir = substr($cache_dir, 0, -1);
        }

        // create cache dir if needed
        if (!is_dir($cache_dir)) {
            $current_umask = umask(0000);
            @mkdir($cache_dir, 0777, true);
            umask($current_umask);
        }

        $this->cache_dir = $cache_dir;
    }

    /**
     * Converts a cache key to a full path.
     *
     * @param string  The cache key
     *
     * @return string The full path to the cache file
     */
    protected function getFilePath($key) {
        return $this->cache_dir . DIRECTORY_SEPARATOR . str_replace(self::SEPARATOR, DIRECTORY_SEPARATOR, $key) . $this->extension;
    }

    /**
     * Reads the cache file and returns the content.
     *
     * @param string The file path
     * @param mixed  The type of data you want to be returned
     *               FileCache::READ_DATA: The cache content
     *               FileCache::READ_TIMEOUT: The timeout
     *               FileCache::READ_LAST_MODIFIED: The last modification timestamp
     *
     * @return string The content of the cache file.
     *
     * @throws CacheException
     */
    protected function read($path, $type = self::READ_DATA) {
        if (!$fp = @fopen($path, 'rb')) {
            throw new CacheException(sprintf('Unable to read cache file "%s".', $path));
        }

        @flock($fp, LOCK_SH);
        clearstatcache(); // because the filesize can be cached by PHP itself...
        $length = @filesize($path);
        //$mqr = get_magic_quotes_runtime();
        //set_magic_quotes_runtime(0);
        switch ($type) {
            case self::READ_TIMEOUT:
                $data = $length ? intval(@fread($fp, 12)) : 0;
                break;
            case self::READ_LAST_MODIFIED:
                @fseek($fp, 12);
                $data = $length ? intval(@fread($fp, 12)) : 0;
                break;
            case self::READ_DATA:
                if ($length) {
                    @fseek($fp, 24);
                    $data = @fread($fp, $length - 24);
                } else {
                    $data = '';
                }
                break;
            default:
                throw new Exception(sprintf('Unknown type "%s".', $type));
        }
        //set_magic_quotes_runtime($mqr);
        @flock($fp, LOCK_UN);
        @fclose($fp);

        return $data;
    }

    /**
     * Writes the given data in the cache file.
     *
     * @param  string  The file path
     * @param  string  The data to put in cache
     * @param  integer The timeout timestamp
     *
     * @return boolean true if ok, otherwise false
     *
     * @throws CacheException
     */
    protected function write($path, $data, $timeout) {
        $current_umask = umask();
        umask(0000);

        if (!is_dir(dirname($path))) {
            // create directory structure if needed
            mkdir(dirname($path), 0777, true);
        }

        if (!$fp = @fopen($path, 'wb')) {
            throw new CacheException(sprintf('Unable to write cache file "%s".', $path));
        }

        @flock($fp, LOCK_EX);
        @fwrite($fp, str_pad($timeout, 12, 0, STR_PAD_LEFT));
        @fwrite($fp, str_pad(time(), 12, 0, STR_PAD_LEFT));
        @fwrite($fp, $data);
        @flock($fp, LOCK_UN);
        @fclose($fp);

        // change file mode
        chmod($path, 0666);

        umask($current_umask);

        return true;
    }

}

// Define a cache exception
class MmCacheException extends Exception {

}