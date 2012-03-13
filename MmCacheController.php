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
        $rootDir = CMS_ROOT . DS . Plugin::getSetting('dir', 'mm_cache');
        $extension = Plugin::getSetting('extension', 'mm_cache');
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
    public function clearcacheall() {
        if (!MmCache::getInstance()->clean('all')) {
            Flash::set('error', __('All cache entries have NOT been cleared!'));
        } else {
            Flash::set('success', __('All cache entries have been cleared!'));
        }

        redirect(get_url('plugin/mm_cache'));
    }
    
    /**
     * Action to clear the cache
     * */
    public function clearcacheold() {
        if (!MmCache::getInstance()->clean('old')) {
            Flash::set('error', __('Expired cache entries have NOT been cleared!'));
        } else {
            Flash::set('success', __('Expired cache entries have been cleared!'));
        }
        redirect(get_url('plugin/mm_cache'));
    }

    /**
     * Settings for mmCache to change specific features
     */
    public function settings() {
        $settings = Plugin::getAllSettings('mm_cache');
        $this->display('mm_cache/views/settings', $settings);
    }

}

// END public class CacheController