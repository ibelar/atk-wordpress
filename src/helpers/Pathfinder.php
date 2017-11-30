<?php

/**
 * Created by abelair.
 * Date: 2017-06-12
 * Time: 11:00 AM
 */
namespace atkwp\helpers;

use atkwp\interfaces\PathInterface;

class Pathfinder implements PathInterface
{
    //The path to this plugin.
    public $path;

    //Array matching path according to file type.
	public $filesLocation = [];

	//default skin for atk-ui
	public $skin = 'semantic-ui';

	public function __construct($pluginPath)
	{
	    $this->path = $pluginPath;
        $this->setFilesLocation($pluginPath, $this->skin);
	}

    public function getConfigurationPath()
    {
        return $this->path . 'configurations';
    }

    public function getAssetsPath()
    {
        return $this->path . 'assets';
    }

    public function getTemplateLocation($fileName)
    {
        return $this->getFileLocation('template', $fileName);
    }

	private function setFilesLocation($path, $skin)
	{
	    //When looking for a template file, will look into plugin dir first, then atkwp and finally atkui.
		$this->filesLocation['template']['plugin'] = $path . 'templates/';
		$this->filesLocation['template']['atkwp']  = $path . 'vendor/atk-wordpress/templates/';
		$this->filesLocation['template']['atkui']  = $path . 'vendor/atk4/ui/template/'. $skin . '/';
	}

	private function getFileLocation($type, $fileName)
	{
		foreach ($this->filesLocation[$type] as $dir) {
			$path = $dir . $fileName;
			if (is_readable($path)) {
				return $path;
			}
		}
		throw new \atk4\ui\Exception([
			'Unable to get path location for file',
			'file'=> $fileName
		]);
	}
}