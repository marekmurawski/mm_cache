<?php
/*
 * Funky Cache plugin for Wolf CMS. <http://www.wolfcms.org>
 * 
 * Copyright (C) 2012 Martijn van der Kleijn <martijn.niji@gmail.com>
 * Copyright (c) 2008-2009 Mika Tuupola
 *
 * This file is part of the Funky Cache plugin for Wolf CMS. It is licensed
 * under the MIT license.
 * 
 * For details, see:  http://www.opensource.org/licenses/mit-license.php
 */

?>
<script>
	$(document).ready(function() {prettyPrint();});
</script>

 <p class="button"><a href="<?php echo get_url('plugin/mm_cache'); ?>"><img src="<?php echo ICONS_URI; ?>/file-folder-32.png" align="middle" alt="folder icon" /><?php echo __('List cache entries'); ?></a></p>
 
 <p class="button"><a href="<?php echo get_url('plugin/mm_cache/clearcacheold') ?>"><img src="/wolf/icons/delete-32.png" align="middle" alt="page icon" />Clear expired entries</a></p>
 
 <p class="button"><a href="<?php echo get_url('plugin/mm_cache/clearcacheall') ?>"><img src="/wolf/icons/delete-32.png" align="middle" alt="page icon" />Clear all entries</a></p>

 <p class="button"><a href="<?php echo get_url('plugin/mm_cache/settings'); ?>"><img src="<?php echo ICONS_URI; ?>/settings-32.png" align="middle" alt="settings icon" /><?php echo __('Settings'); ?></a></p>

 <p class="button"><a href="<?php echo get_url('plugin/mm_cache/documentation'); ?>"><img src="<?php echo ICONS_URI; ?>/file-unknown-32.png" align="middle" alt="page icon" /><?php echo __('Documentation'); ?></a></p>
<div class="box">
    <h2><?php echo __('About mmCache'); ?></h2>
    <p>
        <?php echo __('mmCache is a plugin useful for caching various fragments of page and/or data. It uses file storage to cache time-expensive tasks for future use.'); ?>
    </p>
    <p>
        <?php echo __('This plugin provides 2 basic functionalities. Caching output fragments and caching general variables/objects.'); ?>
    </p>
    <h3>
        <?php echo __('Caching Fragments'); ?>
    </h3>
    <p>
        <?php echo __('It is done by wrapping computationally expensive fragment of code in if clause:'); ?>
    </p>
    <pre class="prettyprint">
if (! mmFragment::load('cacheKey')) {
    // do something slow here
    // eg. render multilevel menu
    mmFragment::save(120) 
    // save fragment for 120 seconds
    // after rendering it
}
    </pre>

</div>