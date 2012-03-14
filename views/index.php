<?php
/* Security measure */
if (!defined('IN_CMS')) {
    exit();
}
?>

<h1><?php echo __('mmCache administration') ?></h1>

    
<form action="<?php echo get_url('plugin/mm_cache/clearcachebyname') ?>" method="POST">
   <p class="clearcacheform buttons">
       <?php echo __("Type string to clear cache entries containing it.") ?>
      <input type="text" name="name" value="<?php echo Flash::get('mmcachesearchname'); ?>" />
      <input type="submit" class="button" name="commit" accesskey="c" value="<?php echo __('Clear cache by name') ?>" />
   </p>
</form>  

<table class="index" cellpadding="0" cellspacing="0" border="0">
    <thead>
        <tr>
            <th class="mmkey"><?php echo __('Cache key') ?></th>
            <th class="mmsize"><?php echo __('File size') ?></th>
            <th class="mmupdated"><?php echo __('Updated time') ?></th>
            <th class="mmage"><?php echo __('Age') ?>&nbsp;/&nbsp;<?php echo __('Lifetime') ?></th>
            <th class="mmactions">&nbsp;</th>
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
                <!--    <a href="<?php echo get_url('plugin/mm_cache') ?>"><img src="/wolf/icons/delete-16.png" alt="Delete" title="Delete"></a> -->
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<pre>

</pre>


  
