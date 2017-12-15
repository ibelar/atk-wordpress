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
 * Plugin Interface.
 * An AtkWp Plugin must implement this interface to work under Wp.
 */

namespace atkwp\interfaces;

interface PluginInterface
{
    /**
     * Wordpress acrivated plugin implementation.
     *
     * @return mixed
     */
    public function activatePlugin();

    /**
     * Worpress deactivated plugin implementation.
     *
     * @return mixed
     */
    public function deactivatePlugin();
}
