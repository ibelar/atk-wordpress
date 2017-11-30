<?php
/**
 * Atk base component for Wordpress.
 * Component is an atk view that can be displayed inside WP.
 * The plugin instance running the atk app will be inject within the app.
 * This mean that when using a component, you can get to your plugin instance using
 * $this->app->plugin
 *
 */

namespace atkwp\components;


use atkwp\AtkWpView;

class Component extends AtkWpView
{
    public $defaultTemplate = 'component.html';
}