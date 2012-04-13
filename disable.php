<?php
require_once 'lib/mmCache.php';

// clear the cache
//MmCache::getInstance()->clean('all');

    Flash::set('success', 'mmCache plugin successfully deactivated');
    if (Snippet::findByName('mmCacheExamples')->delete()) {Flash::set('info', __('A sample snippet "mmCacheExamples" has been deleted!'));};