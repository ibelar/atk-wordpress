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
 * Atk base component for Wordpress.
 * Component is an atk view that can be displayed inside WP.
 * The plugin instance running the atk app will be inject within the app.
 * This mean that when using a component, you can get to your plugin instance using
 * $this->app->plugin.
 */

namespace atkwp\components;

use atkwp\AtkWpView;

class Component extends AtkWpView
{
    /**
     * The default template file name for this component.
     *
     * @var string
     */
    public $defaultTemplate = 'component.html';
}
