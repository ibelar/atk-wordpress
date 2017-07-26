<?php
/**
 * Created by abelair.
 * Date: 2017-06-14
 * Time: 5:23 PM
 */

namespace atkwp\helpers;


class WpUtil 
{
	static $jQueryVar = 'jQuery';
	static $jQueryBundle = 'wp-atk4-bundle-jquery.min';

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

	public static function getWpOption($option)
	{
		return get_option($option);
	}

	public static function getJQueryVar()
	{
		return self::$jQueryVar;
	}

	public static function getJQueryBundle()
	{
		return self::$jQueryBundle;
	}
}