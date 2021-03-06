<?php

/* Security measure */
if ( !defined( 'IN_CMS' ) ) 
    exit();

if ( !Plugin::isEnabled( 'mm_core' ) ) {
    unset( Plugin::$plugins['mm_cache'] );
    Plugin::save();
    Flash::set( 'error', __( '<b>mm_core</b> plugin not found! <br/>
                            Download latest <b>mm_core</b> here: <br/>
                            <a href="http://marekmurawski.pl/en/web/wolf-cms/plugins" target="_blank">http://marekmurawski.pl/en/web/wolf-cms/plugins</a>
                           ' ) );
    exit();
}

$settings['extension']        = 'cache';
$settings['dir']              = 'mm_cache';
$settings['default_lifetime'] = '360';

$exists = Plugin::getSetting( 'dir', 'mm_cache' );
if ( !$exists ) {
    Plugin::setAllSettings( $settings, 'mm_cache' );
} else {
    $settings = Plugin::getAllSettings( 'mm_cache' );
}

$snippetContent = file_get_contents( PLUGINS_ROOT . DS . 'mm_cache' . DS . 'samples' . DS . 'sample_snippet.php' );

$newSnippet                = new Snippet;
$newSnippet->name          = 'mmCacheExamples';
$newSnippet->created_on    = date( 'Y-m-d H:i:s' );
$newSnippet->content       = $snippetContent;
$newSnippet->content_html  = $snippetContent;
$newSnippet->created_by_id = 1;
if ( $newSnippet->save() ) {
    Flash::set( 'info', __( 'A sample snippet "mmCacheExamples" has been created!' ) );
};


try {
    if ( !is_dir( CMS_ROOT . DS . $settings['dir'] ) ) {
        mkdir( CMS_ROOT . DS . $settings['dir'] );
    };
    file_put_contents( CMS_ROOT . DS . $settings['dir'] . DS . '.htaccess', 'DENY FROM ALL' );
    Flash::set( 'success', 'mmCache plugin successfully activated' );
}
catch ( Exception $e ) {
    Flash::set( 'error', 'Error while creating directory and/or .htaccess file!' . '<br/>' . $e->getMessage() );
}
die();
