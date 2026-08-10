<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>
<p>

    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', PLUGIN_LOCALE); ?></label>

    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />

</p>



<p>

    <label for="<?php echo $this->get_field_id('api_key'); ?>"><?php _e('API-Key:', PLUGIN_LOCALE); ?></label>

    <input class="widefat" id="<?php echo $this->get_field_id('api_key'); ?>" name="<?php echo $this->get_field_name('api_key'); ?>" type="text" value="<?php echo $api_key; ?>" />

    <a href="http://steamcommunity.com/dev/apikey" target="_blank">http://steamcommunity.com/dev/apikey</a>

</p>



<p>

    <label for="<?php echo $this->get_field_id('steam_id'); ?>"><?php _e('SteamID64:', PLUGIN_LOCALE); ?></label>

    <input class="widefat" id="<?php echo $this->get_field_id('steam_id'); ?>" name="<?php echo $this->get_field_name('steam_id'); ?>" type="text" value="<?php echo $steam_id; ?>" />

    <a href="http://steamidconverter.com" target="_blank">http://steamidconverter.com</a>

</p>



<p>

    <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Show # of games:', PLUGIN_LOCALE); ?></label>

    <input class="widefat" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" />

</p>

<p>

    <label for="<?php echo $this->get_field_id('cache_interval'); ?>"><?php _e('cache refresh interval (s):', PLUGIN_LOCALE); ?></label>

    <input class="widefat" id="<?php echo $this->get_field_id('cache_interval'); ?>" name="<?php echo $this->get_field_name('cache_interval'); ?>" type="text" value="<?php echo $cache_interval; ?>" />

</p>