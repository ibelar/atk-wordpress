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
 * Dashboard Component in WP.
 *
 * Property $configureMode will tell if the Dashboard
 * component is in configuration mode or not so you can act
 * accordingly in init() method.
 *
 * public function init()
 * {
 *      parent::init();
 *
 *      if ($this->configurationMode) {
 *          doConfigurationStuff();
 *      } else {
 *          doRegularStuff();
 *      }
 * }
 */

namespace atkwp\components;

class DashboardComponent extends Component
{
    /**
     * Whether this dashboard is running under configuration mode or not.
     * This is automatically set by Plugin depending on the dashboard mode.
     *
     * @var bool
     */
    public $configureMode = false;
}
