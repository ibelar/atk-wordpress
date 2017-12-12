<?php
/**
 * Shortcode component in Wordpress.
 */

namespace atkwp\components;

class ShortcodeComponent extends Component
{
    /*
     * The attribute define in your shortcode code that can be use when setting the view.
     * ex: setting a shortcode in a page like so: [myshortcode title="myTitle" class="myClass"]
     * then when view is create the args property will contains array with your attribute name and value.
     *   - $args = ['title' => 'myTitle', 'class' => 'myClass']
     */
    public $args = null;
}
