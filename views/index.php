<?php
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}
?>

<h1><?php echo __('Cache administration') ?></h1>
<table class="index" cellpadding="0" cellspacing="0" border="0">
    <thead>
        <tr>
            <th class="key"><?php echo __('Cache key') ?></th>
            <th class="size"><?php echo __('File size') ?></th>
            <th class="date"><?php echo __('Updated time') ?></th>
            <th class="date"><?php echo __('Age') ?>&nbsp;/&nbsp;<?php echo __('Lifetime') ?></th>
            <th class="oldtime">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($cacheFiles as $file): ?>
            <tr class="odd">
                <?php
                $fdate = date("y/m/d", $file['updated']);
                $ftime = date("H:i:s", $file['updated']);
                if ($file['valid']) {$barWidth = round(($file['age']/$file['lifetime'])*160);}
                else {$barWidth = 160;}
                ?>
                <td><code><?php echo mm_trim_key($file['name'],70,30,17); ?></code></td>
                <td><code><?php echo $file['size'] ?></code></td>
                <td><code><?php echo $fdate . '<br/>' . $ftime ?></code></td>
                <td><div><?php echo $file['age'] ?> s.<div style="float: right;"><?php echo $file['lifetime'] ?></div><div><div id="mmprogresscontainer"><div id="mmprogressbar" style="width: <?php echo $barWidth ?>px"></div></div></td>
                <td>
                    <a href="http://multicentrum-jawor.pl/admin/plugin/file_manager/delete/images" onclick="return confirm('Czy na pewno chcesz usunąć? images?');"><img src="/wolf/icons/delete-16.png" alt="skasuj ikonę pliku" title="Usuń plik"></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<pre>

</pre>


    <p class="buttons">
        <a href="<?php echo get_url('plugin/mm_cache/clearcacheold') ?>">Clear expired entries</a>
        <a href="<?php echo get_url('plugin/mm_cache/clearcacheall') ?>">Clear all entries</a>
    </p>
