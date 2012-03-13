<?php

/**
 * Plugin AS Cache Controller
 *
 * @package frog
 * @subpackage plugins
 * @author Gilles Doge <gde@antistatique.net>, Antistatique.net
 * @version 0.2 SVN: $Id: AsCacheController.php 132 2008-09-12 11:06:16Z gde $
 * */
class MmCacheController extends PluginController {

    public function __construct() {
        AuthUser::load();
        if (!(AuthUser::isLoggedIn())) {
            redirect(get_url('login'));
        }

        if (!AuthUser::hasPermission('admin_view')) {
            redirect(URL_PUBLIC);
        }

        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View('../../plugins/mm_cache/views/sidebar'));
    }

    public function index() {
        $rootDir = CMS_ROOT . DS . Plugin::getSetting('mm_cache_dir', 'mm_cache');
        $extension = Plugin::getSetting('mm_cache_extension', 'mm_cache');
        clearstatcache();
        $bytesTotalValid = 0;
        $bytesTotalExpired = 0;
        $validFilesCount = 0;
        $expiredFilesCount = 0;
        $tnow = time();
        $cacheFiles = array();
        $iterator = new RecursiveDirectoryIterator($rootDir);
        foreach (new RecursiveIteratorIterator($iterator) as $filename => $cur) {

            $age        = intval($tnow - $cur->getMTime());
            $fname      = str_replace($rootDir, '', $filename);
            $fsize      = $cur->getSize();

            $keyname    = substr($fname, 1, -strlen($extension));
            $timeout    = MmCache::getInstance()->getTimeout($keyname);
            $ttl        = intval($timeout - $tnow);
            
            if ($timeout > 0) {
                $bytesTotalValid+=$fsize;
                $validFilesCount++;
                $lifetime   = intval($age+$ttl);
            } else {
                $bytesTotalExpired+=$fsize;
                $expiredFilesCount++;
                $lifetime   = 'expired';
            }

            $cacheFiles[] = array(
                'valid'    => ($timeout > 0),
                'name'     => $fname,
                'fullname' => $filename,
                'size'     => $fsize,
                'updated'  => $cur->getMTime(),
                'age'      => $age,
                'ttl'      => $ttl,
                'lifetime' => $lifetime,
            );
        } // foreach

        $bytesTotalValid = number_format($bytesTotalValid);
        $bytesTotalExpired = number_format($bytesTotalExpired);

        $this->display('mm_cache/views/index', array(
            'cacheFiles' => $cacheFiles,
            'bytesTotalValid' => $bytesTotalValid,
            'bytesTotalExpired' => $bytesTotalExpired,
            'validFilesCount' => $validFilesCount,
            'expiredFilesCount' => $expiredFilesCount,
                )
        );
    }

    /**
     * Action to clear the cache
     * */
    public function clearcache() {
        if (!MmCache::getInstance()->clean('all')) {
            Flash::set('error', __('Cache has not been cleared!'));
        } else {
            Flash::set('success', __('Cache has been cleared!'));
        }

        redirect(get_url('plugin/mm_cache'));
    }

    /**
     * Settings for Tagger to change specific features
     *
     * @since 1.1.0
     *
     */
    public function settings() {
        $settings = Plugin::getAllSettings('mm_cache');
        $this->display('mm_cache/views/settings', $settings);
    }

}

// END public class CacheController