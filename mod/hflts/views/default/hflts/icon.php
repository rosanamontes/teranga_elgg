<?php

if ($vars['size'] == 'large') {
    if (elgg_get_plugin_setting('profile_display', 'hflts')) {
		if (!$vars['entity']->karma)	$vars['entity']->karma = "novel";
		if (!$vars['entity']->nValorations) $vars['entity']->nValorations = 0;
?>

        <div class="hflts_profile">
            <div><?php echo elgg_echo('hflts:profile') . ': <span style="color:#F05A23">' . $vars['entity']->karma;?></span></div>
            <div><?php echo elgg_echo('hflts:number') . ' <span style="color:#F05A23">'   . $vars['entity']->nValorations . '</span> ' . elgg_echo('hflts:users');?></div>
        </div>

    <?php } ?>
<?php } ?>