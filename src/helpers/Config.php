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
 * Simple configuration utilities.
 */

namespace atkwp\helpers;

use atk4\core\ConfigTrait;
use atk4\core\Exception;

class Config
{
    use ConfigTrait {
        readConfig as private;
    }

    /**
     * Contains the path to the configuration files.
     *
     * @var string
     */
    public $configPath;

    /**
     * Default configuration files to read.
     *
     * @var array
     */
    public $wpConfigFiles = [
        'config-default',
        'config-wp',
        'config-panel',
        'config-enqueue',
        'config-shortcode',
        'config-widget',
        'config-metabox',
        'config-dashboard',
    ];

    /**
     * Config constructor.
     *
     * @param string $configPath The path to configuration files.
     */
    public function __construct($configPath)
    {
        $this->configPath = $configPath;
        $this->loadConfiguration();
    }

    /**
     * Load configuration files.
     * @throws Exception
     */
    private function loadConfiguration()
    {
        foreach ($this->wpConfigFiles as $fileName) {

            if (strpos($fileName, '.php') != strlen($fileName) - 4) {
                $fileName .= '.php';
            }

            $filePath = $this->configPath . '/' . $fileName;
            if (file_exists($filePath)) {
                $config = [];
                include $filePath;
                $this->setConfig($config);
            }
        }
    }
}
