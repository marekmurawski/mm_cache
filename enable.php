<?php
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}

$settings = array();
$settings['extension'] = '.cache';
$settings['cache_dir'] = 'mm_cache';
$settings['default_lifetime'] = '360';
        
Plugin::setAllSettings($settings, 'mm_cache');
