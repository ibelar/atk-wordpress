<?php
/**
 * Created by abelair.
 * Date: 2017-11-23
 * Time: 1:50 PM.
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
     * @return mixed
     */
    public function getAssetsPath();
}
