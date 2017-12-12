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
 * Licensed under MIT
 * =====================================================================*/
/**
 * Shortcode component in Wordpress.
 */

namespace atkwp\components;

class ShortcodeComponent extends Component
{
    /*
     * The attribute define in shortcode code that can be use when setting the view.
     * ex: setting a shortcode in a page like this: [myshortcode title="myTitle" class="myClass"]
     * then when view is create the args property will contains array with attribute name and value.
     *   - $args = ['title' => 'myTitle', 'class' => 'myClass']
     */
    public $args = null;
}
