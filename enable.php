<?php
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}

$settings = array();
$settings['mm_cache_extension'] = '.kesz';
$settings['mm_cache_dir'] = 'mm_cache';
$settings['mm_cache_default_lifetime'] = '300';
        
Plugin::setAllSettings($settings, 'mm_cache');
