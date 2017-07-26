<?php
/**
 * Created by abelair.
 * Date: 2017-06-09
 * Time: 2:07 PM
 */

namespace atkwp\controllers;


use atkwp\AtkWp;

class EnqueueController
{

	private $pluginService;
	protected $jQueryUiComponents = [];

	//bundle all atk4 js file together and use jquery var instead of '$'
	protected $atkJsFiles = [
		'https://cdnjs.cloudflare.com/ajax/libs/jquery-serialize-object/2.5.0/jquery.serialize-object.min.js',
		'https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.min.js',
		'https://cdn.rawgit.com/mdehoog/Semantic-UI-Calendar/0.0.8/dist/calendar.min.js',
		'atk4JS.min.js'
	];

	//the css file to load.
	protected $atkCssFiles = ['https://cdnjs.cloudflare.com/ajax/libs/semantic-ui/2.2.10/semantic.css',
        'https://cdn.rawgit.com/mdehoog/Semantic-UI-Calendar/0.0.8/dist/calendar.css'];

	public function __construct(AtkWp $service)
	{
		$this->pluginService = $service;
		$this->init();
	}

	public function init()
	{

		if (is_admin()) {
			add_action('admin_enqueue_scripts', [$this, 'enqueueAdminFiles']);
		} else {
			add_action('wp_enqueue_scripts', [$this, 'enqueueFrontFiles']);
		}
	}

	/**
	 * @return array
	 */
	public function getAtkJsFiles()
	{
		return $this->atkJsFiles;
	}

	public function registerAtkJsFiles($files)
	{
		//register files.
		foreach ($files as $file) {
			//atkjs file need wp-init as a dependency.
			wp_register_script($file, $this->pluginService->locateURL('js', $file.'.js'), ['jquery']);
		}
	}


	public function enqueueAdminFiles($hook, $forceEnqueue = false)
	{

		//Check if this is an atk panel.
		// and enqueue atk file
		$panel = $this->getAtkPanel($hook);
		if (isset($panel) || $forceEnqueue) {
			//check if panel require specific js file.
			if (isset ($panel['js'])) {
				$this->atkJsFiles = array_merge($this->atkJsFiles, $panel['js']);
			}
			if (@$userJsFiles = $this->pluginService->getConfig('enqueue/admin/js', null)) {
				$this->atkJsFiles = array_merge($this->atkJsFiles, $userJsFiles);
			}
			$this->enqueueFiles($this->atkJsFiles, 'js');

			if (isset($panel['js-inc'])) {
				$this->enqueueJsInclude($panel['js-inc']);
			}

			if (isset ($panel['css'])) {
				$this->atkCssFiles = array_merge($this->atkCssFiles, $panel['css']);
			}

			if (@$userCssFiles = $this->pluginService->getConfig('enqueue/admin/css', null)) {
				$this->atkCssFiles = array_merge($this->atkCssFiles, $userCssFiles);
			}
			$this->enqueueFiles($this->atkCssFiles, 'css');

			if (isset($panel['css-inc'])) {
				$this->enqueueCssInclude($panel['css-inc']);
			}
		}

	}

	public function enqueueAtkJsInFront()
	{
		$this->enqueueFiles($this->atkJsFiles, 'js');
		$this->enqueueFiles($this->atkCssFiles, 'css');
	}

	public function enqueueFrontFiles()
	{
		//$this->registerAtkJsFiles($this->atkJsFiles);
		if (@$frontJsFiles = $this->pluginService->getConfig('enqueue/front/js', null)) {
			$this->enqueueFiles($frontJsFiles, 'js');
		}
		if (@$frontCssFiles = $this->pluginService->getConfig('enqueue/front/css', null)) {
			$this->enqueueFiles($frontCssFiles, 'css');
		}
	}

	public function enqueueFiles($files, $type, $required = null)
	{
		if (!isset($required))
			$required = ['jquery'];
		try {
			if ($type === 'js') {
				foreach ($files as $file) {
					if (strpos($file, 'http') === 0) {
						$source = $file;
					} else {
						$source = $this->pluginService->pathFinder->getJsLocation($file, true);
					}
					//load in footer with jquery ui file.
					wp_enqueue_script($file, $source, $required, false, true);
				}
			} else {
				foreach ($files as $file) {
					if (strpos($file, 'http') === 0) {
						$source = $file;
					} else {

					}
					wp_enqueue_style( $file, $source, $file );
				}
			}
		} catch (Exception $e) {
			// Handles output of the exception
			$this->pluginService->caughtException($e);
		}

	}


	public function enqueueJQueryUi()
	{
		foreach ($this->jQueryUiComponents as $component) {
			wp_enqueue_script('jquery-ui-'.$component);
		}
	}

	public function enqueueJsInclude($files)
	{
		foreach ($files as $file) {
			wp_enqueue_script($file);
		}
	}

	public function enqueueCssInclude($files)
	{
		foreach ($files as $file) {
			wp_enqueue_style($file);
		}
	}

	public function isAtkPanel($hook)
	{
		return $this->pluginService->panelCtrl->isAtkPanel($hook);
	}

	public function getAtkPanel($hook)
	{
		return $this->pluginService->panelCtrl->getAtkPanel($hook);
	}
}