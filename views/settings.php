<?php
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}
?>
<h1><?php echo __('mmCache Plugin Settings'); ?></h1>

<form method="post">
    <fieldset style="padding: 0.5em;">
        <legend style="padding: 0em 0.5em 0em 0.5em; font-weight: bold;"><?php echo __('General settings'); ?></legend>
        <table class="fieldset" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="label"><label for="dir"><?php echo __('Cache directory'); ?>: </label></td>
                <td class="field">
                    <input type="text" value="<?php echo $dir ?>" name="dir" id="dir" />
                </td>
                <td class="help"><?php echo __('Set the root directory for cache files. Specify path relative to CMS_ROOT (eg. "cache"). Plugin will try to create the directory with 0777 permissions.'); ?></td>
            </tr>
            <tr>
                <td class="label"><label for="extension"><?php echo __('Cache files extension'); ?>: </label></td>
                <td class="field">
                    <input type="text" value="<?php echo $extension ?>" name="extension" id="extension" />
                </td>
                <td class="help"><?php echo __('Set the extension for cache files (eg. ".cache", ".php")'); ?></td>
            </tr>
            <tr>
                <td class="label"><label for="default_lifetime"><?php echo __('Default timeout'); ?>: </label></td>
                <td class="field">
                    <input type="text" value="<?php echo $default_timeout ?>" name="default_lifetime" id="default_lifetime" />
                </td>
                <td class="help"><?php echo __('Default cache entry timeout in seconds. (eg. 1 day = 86400 seconds). This will be used as a default value for cache and fragments.'); ?></td>
            </tr>
        </table>
    </fieldset>
    <p class="buttons">
        <input class="button" name="commit" type="submit" accesskey="s" value="<?php echo __('Save'); ?>" />
    </p>
</form>
