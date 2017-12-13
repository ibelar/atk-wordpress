<?php
/* =====================================================================
 * atk-wordpress => Wordpress interface for Agile Toolkit Framework.
 *
 * This interface enable the use of the Agile Toolkit framework within a WordPress site.
 *
 * Please note that when atk is mentioned it generally refer to Agile Toolkit.
 * More information on Agile Toolkit: http://www.agiletoolkit.org
 *
 * Author: Alain Belair
 * https://github.com/ibelar
 * Licensed under MIT
 * =====================================================================*/
/**
 * Simple utility class.
 * Contains static function that usually map to Wp global function.
 */

namespace atkwp\helpers;

class WpUtil
{
    /**
     * The jQuery var name.
     *
     * @var string
     */
    public static $jQueryVar = 'jQuery';

    /**
     * Return the Wp database prefix name.
     *
     * @return mixed
     */
    public static function getDbPrefix()
    {
        global $wpdb;

        return $wpdb->prefix;
    }

    /**
     * Return Wp page id.
     *
     * @return int|string
     */
    public static function getPageId()
    {
        global $post;
        if (is_home()) {
            return 'home';
        } else {
            return $post->ID;
        }
    }

    /**
     * Return wether the plugin is running under admin mode or not.
     *
     * @return bool
     */
    public static function isAdmin()
    {
        return is_admin();
    }

    /**
     * Create the Wp nounce value for ajax security.
     *
     * @param $name
     *
     * @return string
     */
    public static function createWpNounce($name)
    {
        return wp_create_nonce($name);
    }

    /**
     * Return the base url of the admin page.
     *
     * @return string|void
     */
    public static function getBaseAdminUrl()
    {
        return admin_url();
    }

    /**
     * Return the absolute url of a plugin file.
     *
     * @param string $path
     * @param string $plugin
     *
     * @return string
     */
    public static function getPluginUrl($path, $plugin)
    {
        return plugins_url($path, $plugin);
    }

    /**
     * Return options from Wp.
     *
     * @param $option
     *
     * @return mixed|void
     */
    public static function getWpOption($option)
    {
        return get_option($option);
    }

    /**
     * Return the jQuery property value.
     *
     * @return string
     */
    public static function getJQueryVar()
    {
        return self::$jQueryVar;
    }
}
