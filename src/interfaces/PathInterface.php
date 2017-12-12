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
 * Path locator interface.
 * The AtkWp Plugin constructor need a path locator implementing this interface.
 */

namespace atkwp\interfaces;

interface PathInterface
{
    /**
     * @param $templateFileName The template file to lookup.
     *
     * @return mixed string containing the full path to the template file.
     */
    public function getTemplateLocation($templateFileName);

    /**
     * Get the configuration file path.
     *
     * @return mixed string containing full path to configuration file.
     */
    public function getConfigurationPath();

    /**
     * Get the assets directory path.
     *
     * @return mixed
     */
    public function getAssetsPath();
}
