<?php
/**
 * Created by abelair.
 * Date: 2017-06-14
 * Time: 5:23 PM.
 */

namespace atkwp\helpers;

class WpUtil
{
    public static $jQueryVar = 'jQuery';

    public static function getDbPrefix()
    {
        global $wpdb;

        return $wpdb->prefix;
    }

    public static function getPageId()
    {
        global $post;
        if (is_home()) {
            return 'home';
        } else {
            return $post->ID;
        }
    }

    public static function isAdmin()
    {
        return is_admin();
    }

    public static function createWpNounce($name)
    {
        return wp_create_nonce($name);
    }

    public static function getBaseAdminUrl()
    {
        return admin_url();
    }

    public static function getPluginUrl($path, $plugin)
    {
        return plugins_url($path, $plugin);
    }

    public static function getWpOption($option)
    {
        return get_option($option);
    }

    public static function getJQueryVar()
    {
        return self::$jQueryVar;
    }
}
