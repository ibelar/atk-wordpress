<?php
/**
 * Created by abelair.
 * Date: 2017-06-08
 * Time: 11:45 AM
 */

namespace atkwp;
use atkwp\controllers\EnqueueController;
use atkwp\controllers\MetaBoxController;
use atkwp\controllers\PanelController;

class AtkWp
{

	use \atk4\core\InitializerTrait {
		init as _init;
	}
	use \atk4\core\HookTrait;
//	use \atk4\core\AppScopeTrait;
//	use \atk4\core\ContainerTrait;
	// use \atk4\core\TrackableTrait;
	// use \atk4\core\SessionTrait;

	//The  name of the plugin
	public $pluginName;
	// an instance of this plugin
	// public $plugin;

	public $config = [];

	protected $isExecuting;

	//Whether initialized_layout is bypass or not.
	public $isLayoutNeedInitialise = true;

	//The enqueue controller.
	public $enqueueCtrl;
	//The panel controller.
	public $panelCtrl;
	//The Widget controller
	public $widgetCtrl;
	//The Metabox controller
	public $metaBoxCtrl;
	//The dasboard controller
	public $dashboardCtrl;

	public $shortcodeCtrl;

	//the current wp view to output. ( Ex: admin panel, shortcode or metabox)
	public $wpComponent;
	// the database connection for this service.
	public $dbConnection;

	//default config files to read
	public $wpConfigFiles = [
		'config-default',
		'config-wp',
		'config-panel',
		'config-enqueue',
		'config-shortcode',
		'config-widget',
		'config-metabox',
		'config-dashboard'];

	public $plugInPath;
	public $configurationPath;
	public $pathFinder;
	//public $configurationDir = 'configuration';

	public $ajaxMode = false;

	//ATK43 init
	/** When page is determined, it's class instance is created and stored in here */
	public $page_object=null;

	/** Class which is used for static pages */
	public $page_class='Page';

	/** List of pages which are routed into namespace */
	public $namespace_routes = array();

	/** Object for a custom layout, introduced in 4.3 */
	public $layout = null;

	/** will contains the app html output when using wp shortcode */
	public $appHtmlBuffer;

	//the metabox being execute.
	public $metaBox;
	//the shortcode being execute
	public $shortcode;


//	public static function getServiceInstance()
//	{
//		return self;
//	}


	public function __construct($name, $pluginPath)
	{
		$this->pluginName = $name;
		$this->plugInPath = $pluginPath;
		// $this->configurationPath = $pluginPath . 'configurations';
		$this->pathFinder = new \atkwp\helpers\Pathfinder($pluginPath);
		$this->initService();
		$this->init();
		//$this->pathfinder_class = 'WpPathfinder';
		//parent::__construct($name);
	}
	

	public function initService() {
		//$this->app = $this;
		$this->config = $this->loadConfiguration();
		// $this->panelCtrl = $this->add(new PanelController($this->getConfig('panel', [])));
		$this->panelCtrl = new PanelController($this);
		$this->enqueueCtrl = new EnqueueController($this);
		$this->metaBoxCtrl = new MetaBoxController($this);
	}

	public function getDbConnection()
	{
		return $this->dbConnection;
	}

	public function getPluginName()
    {
        return $this->pluginName;
    }

    public function getWpComponentId()
    {
        return $this->wpComponent['id'];
    }

	/**
	 * Plugin Entry point
	 * Wordpress plugin file call this function in order to have
	 * atk4 work under Wordpress.
	 *
	 * Will load panel, metab box, widget and shortcode configuration file;
	 * Setup proper Wp action for each of them;
	 * Setup WP Ajax.
	 *
	 * @throws
	 */
	public function boot()
	{
		try {
			$this->panelCtrl->loadPanels();
//			$this->widgetCtrl->loadWidgets();
			$this->metaBoxCtrl->loadMetaBoxes();
//			$this->shortcodeCtrl->loadShortcodes();
//			$this->dashboardCtrl->loadDashboards();
//			add_action('init', [$this, 'wpInit']);
//			//register ajax action for this plugin
			add_action("wp_ajax_{$this->pluginName}", [$this, 'wpAjaxExecute']);
//			//enable Wp ajax front end action.
//			add_action("wp_ajax_nopriv_{$this->pluginName}", [$this, 'wpAjaxExecute']);

		} catch (Exception $e) {
			$this->caughtException($e);
		}

	}

	/*--------------------- OUTPUT -------------------------------*/

	/**
	 * Output Panel view in Wp.
	 *
	 */
	public function wpAdminExecute()
	{
		global $hook_suffix;
		$this->wpComponent = $this->panelCtrl->getPanelUses($hook_suffix);
		try {
			$app = new AtkWpApp($this);
			$app->initWpLayout($this->wpComponent);
			$app->execute();
		} catch (\atk4\ui\Exception $e) {
			$this->caughtException($e);
		}
	}

	/**
	 * Output ajax call in Wp.
	 * This is an overall catch ajax request for Wordpress admin and front.
	 */
	public function wpAjaxExecute()
	{
		$this->ajaxMode = true;
		$this->wpComponent = $this->panelCtrl->getPanelUses($_REQUEST['atkwp'], false);
		if (isset($_GET['atkshortcode'])) {
			$this->stickyGet('atkshortcode');
		}
		try {
            //check_ajax_referer($this->pluginName);
            $app = new AtkWpApp($this);
            $app->page = 'admin-ajax';
            $app->initWpLayout($this->wpComponent);
            $app->execute($this->ajaxMode);
        } catch (\atk4\ui\Exception $e) {
		    $this->caughtException($e);
        }
	}

    /**
     * Output metabox view in Wp.
     *
     * Differnet metabox view may be output within the same admin page,
     * it is necessary to reset the content after main is execute.
     *
     * @$post    \WP_Post //Contains the current post information
     * @$param   Array   //Argument passed into the metabox, contains argument set in config file.
     */
    public function wpMetaBoxExecute(\WP_Post $post, array $param)
    {
        //set the view to output.
        $this->wpComponent = $this->metaBoxCtrl->getMetaBoxByKey($param['id']);//$this->metaBox;
        //$this->panel['id']    = $param['id'];
        //Make our post info available for our view.
        $this->metaBoxCtrl->setCurrentMetaBox($post, $param['args']);
        $app = new AtkWpApp($this);
        $app->initWpLayout($this->wpComponent);
        $app->execute();
//        $this->isLayoutNeedInitialise = false;
//        $this->metaBoxCtrl->metaDisplayCount ++;
//        $this->main();
//        $this->resetContent();
    }

	/**
	 * Manually set configuration option.
	 *
	 * @param array $config
	 * @param mixed $val
	 */
	public function setConfig($config = array(), $val = UNDEFINED)
	{
		if ($val !== UNDEFINED) {
			return $this->setConfig(array($config => $val));
		}
		$this->config = array_merge($this->config ?: array(), $config ?: array());
	}

	/**
	 * Load config if necessary and look up corresponding setting.
	 *
	 * @param string $path
	 * @param mixed $default_value
	 *
	 * @return string
	 */
	public function getConfig($path, $default_value = UNDEFINED)
	{
		/*
		 * For given path such as 'dsn' or 'logger/log_dir' returns
		 * corresponding config value. Throws ExceptionNotConfigured if not set.
		 *
		 * To find out if config is set, do this:
		 *
		 * $var_is_set = true;
		 * try { $app->getConfig($path); } catch ExceptionNotConfigured($e) { $var_is_set=false; }
		 */
		$parts = explode('/', $path);
		$current_position = $this->config;
		foreach ($parts as $part) {
			if (!array_key_exists($part, $current_position)) {
				if ($default_value !== UNDEFINED) {
					return $default_value;
				}
				throw $this->exception('Configuration parameter is missing in config.php', 'NotConfigured')
				           ->addMoreInfo('config_files_loaded', $this->config_files_loaded)
				           ->addMoreInfo('missign_line', " \$config['".implode("']['", explode('/', $path))."']");
			} else {
				$current_position = $current_position[$part];
			}
		}

		return $current_position;
	}

	private function loadConfiguration() {
		$loadedConfig = [];
		foreach ($this->wpConfigFiles as $fileName) {
			$config = [];
			if (strpos( $fileName, '.php') != strlen($fileName) - 4) {
				$fileName .= '.php';
			}
			$filePath = $this->pathFinder->getConfigurationPath() . '/' . $fileName;

			if (file_exists($filePath)) {
				include $filePath;
			}
			$loadedConfig = array_merge($loadedConfig, $config);
		}
		return $loadedConfig;
	}

	public function setDbConnection()
	{
		//$this->app->setConfig('dsn', 'mysql://'.DB_USER.':'.DB_PASSWORD.'@'.DB_HOST.'/'.DB_NAME)
		// mysql:host=hostname;dbname=ssldb
		$dsn = 'mysql:host=' . DB_HOST .';dbname='.DB_NAME;
		$this->dbConnection = new \atk4\data\Persistence_SQL($dsn,DB_USER, DB_PASSWORD);
	}

	/**
	 * Catch exception.
	 *
	 * @param mixed $exception
	 */
	public function caughtException($exception)
	{
		$l = new \atk4\ui\App();
		$l->initLayout('Centered');
		if ($exception instanceof \atk4\core\Exception) {
			$l->layout->template->setHTML('Content', $exception->getHTML());
		} elseif ($exception instanceof \Error) {
			$l->layout->add(new View(['ui'=> 'message', get_class($exception).': '.
			                                            $exception->getMessage().' (in '.$exception->getFile().':'.$exception->getLine().')',
				'error', ]));
			$l->layout->add(new Text())->set(nl2br($exception->getTraceAsString()));
		} else {
			$l->layout->add(new View(['ui'=>'message', get_class($exception).': '.$exception->getMessage(), 'error']));
		}
		$l->layout->template->tryDel('Header');
		$l->run();
		$this->run_called = true;
	}
}