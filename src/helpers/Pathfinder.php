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
 * Simple utility to find assets and atk template files.
 */

namespace atkwp\helpers;

use atkwp\interfaces\PathInterface;

class Pathfinder implements PathInterface
{
    /**
     * The path to the plugin.php file.
     *
     * @var string
     */
    public $path;

    /**
     * Array containing file location according to it's type.
     *
     * @var array
     */
    public $filesLocation = [];

    /**
     * Default directory containing template file to use.
     *
     * @var string
     */
    public $skin = 'semantic-ui';

    /**
     * Pathfinder constructor.
     *
     * @param string $pluginPath The path to plugin.php file.
     */
    public function __construct($pluginPath)
    {
        $this->path = $pluginPath;
        $this->setFilesLocation($pluginPath, $this->skin);
    }

    /**
     * Return the path where configuration directory is located.
     *
     * @return mixed|string
     */
    public function getConfigurationPath()
    {
        return $this->path.'configurations';
    }

    /**
     * Return the path where assets directory is located.
     *
     * @return mixed|string
     */
    public function getAssetsPath()
    {
        return $this->path.'assets';
    }

    /**
     * Return path for a specific template file.
     *
     * @param string $fileName The name of the template file.
     *
     * @throws \atk4\ui\Exception
     *
     * @return mixed|string
     */
    public function getTemplateLocation($fileName)
    {
        return $this->getFileLocation('template', $fileName);
    }

    /**
     * Set file type with their possible location.
     * The order of fileLocation matter since the first set location
     * will have priority over subsequent location.
     * Here, will first look for template file inside the current plugin folder,
     * the inside this integration folder and finally inside atk4/ui folder.
     *
     * @param string $path
     * @param string $skin
     */
    private function setFilesLocation($path, $skin)
    {
        $this->filesLocation['template']['plugin'] = $path.'templates/';
        $this->filesLocation['template']['atkwp'] = $path.'vendor/atk-wordpress/templates/';
        $this->filesLocation['template']['atkui'] = $path.'vendor/atk4/ui/template/'.$skin.'/';
    }

    /**
     * Return the file path of a specific file type and name.
     *
     * @param string $type
     * @param string $fileName
     *
     * @throws \atk4\ui\Exception
     *
     * @return string
     */
    private function getFileLocation($type, $fileName)
    {
        foreach ($this->filesLocation[$type] as $dir) {
            $path = $dir.$fileName;
            if (is_readable($path)) {
                return $path;
            }
        }

        throw new \atk4\ui\Exception([
            'Unable to get path location for file',
            'file'=> $fileName,
        ]);
    }
}
