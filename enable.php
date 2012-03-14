<?php

/* Security measure */
//if (!defined('IN_CMS')) {
//    exit();
//}

$settings['extension'] = 'cache';
$settings['dir'] = 'mm_cache';
$settings['default_lifetime'] = '360';

$exists = Plugin::getSetting('dir', 'mm_cache');
if (! $exists) {
    Plugin::setAllSettings($settings, 'mm_cache');
} else {
    $settings = Plugin::getAllSettings('mm_cache');
}

try {
    if (! is_dir(CMS_ROOT . DS . $settings['dir'])) {
    mkdir(CMS_ROOT . DS . $settings['dir']);
    };
    file_put_contents(CMS_ROOT . DS . $settings['dir'] . DS . '.htaccess', 'DENY FROM ALL');
    Flash::set('success', 'mmCache plugin successfully activated');
} catch (Exception $e) {
    Flash::set('error', 'Error while creating directory and/or .htaccess file!' . '<br/>' . $e->getMessage());
}
die();
