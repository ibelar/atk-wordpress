<?php

/**
 * Created by abelair.
 * Date: 2017-06-12
 * Time: 11:00 AM
 */
namespace atkwp\helpers;

class Pathfinder
{
	public $jsLocation = [];
	public $filesLocation = [];
	public $configurationDir = 'configurations';
	public $configurationPath;

	public function __construct(string $pluginPath)
	{
		$this->setLocations($pluginPath);
	}

	public function setLocations(string $path, string $skin = 'semantic-ui')
	{
		$this->setConfigurationPath($path);
		$this->setFilesLocation($path, $skin);

	}


	public function setConfigurationPath(string $path)
	{
		$this->configurationPath = $path . $this->configurationDir;
	}

	public function getConfigurationPath()
	{
		return $this->configurationPath;
	}

	public function setFilesLocation(string $path, string $skin)
	{
		$this->filesLocation['template']['plugin'] = $path . 'templates/';
		$this->filesLocation['template']['atkwp']  = $path . 'vendor/atk-wordpress/templates/';
		$this->filesLocation['template']['atkui']  = $path . 'vendor/atk4/ui/template/'. $skin . '/';

		$this->filesLocation['js']['plugin']  = $path . 'js/';
		$this->filesLocation['js']['atkwp']   = $path . 'vendor/atk-wordpress/js/lib/';
		$this->filesLocation['js']['atkui']   = $path . 'vendor/atk4/ui/public/';
	}

	public function getTemplateLocation(string $fileName)
	{
		return $this->getFileLocation('template', $fileName);
	}

	public function getJsLocation(string $fileName, bool $needRelative = false)
	{
		$path =  $this->getFileLocation('js', $fileName);
		if ($needRelative) {
			$path = '/' . substr($path, strlen(get_home_path()));
		}
		return $path;
	}

	public function getFileLocation(string $type, string $fileName)
	{
		foreach ($this->filesLocation[$type] as $dir) {
			$path = $dir . $fileName;
			if (is_readable($path)) {
				return $path;
			}
		}
		throw new \atk4\ui\Exception([
			'Unable to get path location for file',
			'file'=> $templateFile
		]);
	}
}