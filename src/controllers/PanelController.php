<?php
/**
 * Created by abelair.
 * Date: 2017-06-08
 * Time: 2:58 PM
 */

namespace atkwp\controllers;

/* =====================================================================
 * Atk4-wp => An Agile Toolkit PHP framework interface for WordPress.
 *
 * This interface enable the use of the Agile Toolkit framework within a WordPress site.
 *
 * Please note that atk or atk4 mentioned in comments refer to Agile Toolkit or Agile Toolkit version 4.
 * More information on Agile Toolkit: http://www.agiletoolkit.org
 *
 * Author: Alain Belair
 * Licensed under MIT
 * =====================================================================*/
use atkwp\AtkWp;

/**
 * This controller is responsible for loading and registering
 * all panels set in config file.
 *
 * Panel can be defined as:
 *      panel for Wp admin section page (config-panel)
 *      shortcode (config-shortcode)
 *      metaBox   (config-metabox)
 *
 * Panel are atk views that are echo within Wordpress section. Echo is done directly, using panel and metabox,
 * or via html string using shortcode.
 *
 * Every panels defined will be registered in $panels array.
 *
 * Panels are register in order for ajax call to know how to load proper atk view to display.
 * On Wp ajax request, the catch all ajax function will load proper panel registered and display it.
 */

class PanelController
{

	private $pluginService;
	// Panels that need to be registered within the app (Admin or Front)
	protected $panels = [];

	// Front panels that can be accessed within a theme.
	//protected $frontPanels = [];

	public function init()
	{
		$this->_init();
	}

	public function __construct(AtkWp $service)
	{
		$this->pluginService = $service;
		$this->panels = $this->pluginService->getConfig('panel', []);
	}

	/**
	 * Return panels array.
	 * @return array
	 */
	public function getPanels()
	{
		return $this->panels;
	}

	/**
	 * @return array
	 */
	/*	public function getFrontPanels() {
			return $this->frontPanels;
		}*/

	/**
	 * Set a panel using a key
	 * @param $key
	 * @param $panel
	 *
	 * @internal param array $panels
	 */
	public function setPanels($key, $panel)
	{
		$this->panels[$key] = $panel;
	}

	public function getPanelSlugByKey($key)
	{
		return $this->panels[$key]['slug'];
	}

	/**
	 * TODO remove and use getPanelSlugByKey instead.
	 * @param $key
	 *
	 * @return mixed
	 */
	public function getPanelParentSlugByKey($key)
	{
		return $this->panels[$key]['slug'];
	}

	public function getPanelClassByKey($key)
	{
		return $this->panels[$key]['uses'];
	}

	public function getPanelUses($panelId = null, $isHook = true)
	{
		$response = array();
		if (!isset($panelId))
			throw $this->exception('Cannot identify panel.');
		if ($isHook) {
			foreach ($this->panels as $key => $panel) {
				if ($panel['hook'] === $panelId) {
					$response['class'] = $panel['uses'];
					$response['id']    = $key;
				}
			}
		} else {
			foreach ($this->panels as $key => $panel) {
				if ( $key === $panelId ) {
					$response['class'] = $panel['uses'];
					$response['id']    = $key;
				}
			}
		}
		return $response;
	}

	public function getFrontPanelUses($page_slug)
	{
		foreach ($this->panels as $key => $panel) {
			if ($panel['page_slug'] === $page_slug) {
				$response['class'] = $panel['uses'];
				$response['id']    = $key;
			}
		}
		return $response;
	}


	public function registerPanelHook($key, $hook)
	{
		$this->panels[$key]['hook'] = $hook;
	}



	/**
	 * Check configuration file for define panel and load them within this app.
	 * @throws App_CLI
	 * @throws BaseException
	 */
	public function loadPanels()
	{
		//start by loading our main panel
		if ($panels = $this->getPanelsByType('panel')) {
			foreach ($panels as $key => $panel) {
				$this->registerPanel($key, $panel);
			}
		}
		//load wp sub panel
		if ($panels = $this->getPanelsByType( 'wp-sub-panel')) {
			$this->registerWpSubPanel($panels);
		}
	}


	/**
	 * prepare a panel to be used wihtin a theme page.
	 * Will also load atkjs file if panel require's it.
	 * @param $panel
	 */
	public function preSetPanel($panel)
	{
		if ($panel['atkjs']) {
			$this->pluginService->enqueueCtrl->enqueueAtkJsInFront();
		}
	}

	/**
	 * This will register the panel within Wordpress by setting the proper action hook.
	 * Will take the uses attribute to load (add) the proper panel class.
	 * Will also add sub panel define for a panel
	 *  TODO add exception when uses is not defines.
	 *
	 * @param $key
	 * @param $panel
	 *
	 * @throws AbstractObject
	 * @throws BaseException
	 */
	public function registerPanel($key, $panel)
	{
		//check if panel has sub panel
		$subPanels = $this->getSubPanels($key);
		add_action('admin_menu', function () use ($key, $panel, $subPanels) {

			$this->createPanelMenu($key, $panel);
			if ( isset($subPanels) && ! empty($subPanels)) {
				foreach ($subPanels as $key => $subPanel) {
					$this->createSubPanelMenu($key, $subPanel);
				}
			}

		} );

		// set panel of our app.
		/*$this->setPanels( $key, $panel );
		if ( isset( $subPanels ) && ! empty( $subPanels ) ) {
			foreach ( $subPanels as $key => $subPanel ) {
				$this->setPanels( $key, $subPanel );
			}
		}*/
	}

	public function registerWpSubPanel($wpPanels)
	{
		add_action('admin_menu', function () use ($wpPanels) {
			foreach ($wpPanels as $key => $wpPanel) {
				$this->createSubPanelMenu($key, $wpPanel);
			}
		});
	}

	/**
	 * This is the actual implementation of the panel.
	 * Registring the add_menu_page action in Wordpress that will render the html for the panel.
	 * When this panel menu is selected, It will run the function $this->app->wpExecute();
	 *
	 * @param $key
	 * @param $panel
	 */
	public function createPanelMenu($key, $panel)
	{
		$iconUrl = null;
		if (isset($panel['icon']) && !empty($panel['icon'])) {
			$iconUrl = $this->pluginService->locatePublicUrl($panel['icon']);
		}
		$hook = add_menu_page($panel['page'],
			$panel['menu'],
			$panel['capabilities'],
			$panel['slug'],
			[$this->pluginService, 'wpAdminExecute'],
			$iconUrl,
			$panel['position']
		);
		$this->registerPanelHook($key, $hook);

	}

	public function createSubPanelMenu($key, $panel)
	{

		$hook = add_submenu_page($this->getPanelParent($panel) ,
			$panel['page'],
			$panel['menu'],
			$panel['capabilities'],
			$panel['slug'],
			[$this->pluginService, 'wpAdminExecute']
		);
		$this->registerPanelHook($key, $hook);
	}

	public function isAtkPanel($hook)
	{
		$panels = $this->getPanels();
		foreach ($panels as $panel) {
			if( $panel['hook'] === $hook )
				return true;
		}
		return false;
	}

	public function getAtkPanel($hook)
	{
		$panels = $this->getPanels();
		foreach ($panels as $panel) {
			if( $panel['hook'] === $hook )
				return $panel;
		}
		return null;
	}

	private function getPanelParent($panel)
	{
		if ($panel['type'] === 'sub-panel') {
			$parentSlug = $this->getPanelParentSlugByKey($panel['parent']);
		}
		if ($panel['type'] === 'wp-sub-panel') {
			$parentSlug = $panel['parent'];
		}
		return $parentSlug;
	}

	private function getPanelsByType($type)
	{
		return array_filter($this->panels, function($panel) use ($type) {
			if ($panel['type'] === $type) return $panel;
		});
	}

	/**
	 * Get sub panel related to a panel.
	 * @param $panelKey
	 *
	 * @return array
	 */
	private function getSubPanels($panelKey)
	{
		$relatedPanels = array();
		$subPanels = $this->getPanelsByType('sub-panel');
		if ($subPanels) {
			foreach ($subPanels as $key => $subPanel) {
				if ($subPanel['parent'] === $panelKey) {
					$relatedPanels[$key] = $subPanel;
				}
			}
		}
		return $relatedPanels;
	}
}