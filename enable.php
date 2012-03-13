<?php

/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}

if (!isset(Plugin::getSetting('cache_dir', 'mm_cache'))) {
    $settings = array();
    $settings['extension'] = '.cache';
    $settings['cache_dir'] = 'mm_cache';
    $settings['default_lifetime'] = '360';
    Plugin::setAllSettings($settings, 'mm_cache');
}
