<?php
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
    public $configureMode = false;
}
