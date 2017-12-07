<?php
/**
 * The actual plugin class implementation for WP.
 */

namespace atkwp;

use atk4\data\Persistence_SQL;
use atk4\ui\Exception;
use atk4\ui\Text;
use atk4\ui\View;
use atkwp\helpers\Config;
use atkwp\interfaces\ComponentCtrlInterface;
use atkwp\interfaces\PathInterface;

class AtkWp
{
    //The  name of the plugin
    public $pluginName;

    //The plugin component controller
    public $componentCtrl;

    protected $isExecuting;

    //Whether initialized_layout is bypass or not.
    public $isLayoutNeedInitialise = true;

    //wp default layout template.
    public $defaultLayout = 'layout.html';

    //the current wp view to output. ( Ex: admin panel, shortcode or metabox)
    public $wpComponent;

    //the database connection for this plugin.
    public $dbConnection;

    //plugin path locator for template file.
    public $pathFinder;

    //plugin configuration
    public $config;

    /**
     * AtkWp constructor.
     *
     * @param string                 $pluginName The name of this plugin.
     * @param PathInterface          $pathFinder The pathFinder object for retrieving atk template file under WP.
     * @param ComponentCtrlInterface $ctrl       The ctrl object responsible to initialize all WP components.
     */
    public function __construct($pluginName, PathInterface $pathFinder, ComponentCtrlInterface $ctrl)
    {
        $this->pluginName = $pluginName;
        $this->pathFinder = $pathFinder;
        $this->componentCtrl = $ctrl;
        $this->config = new Config($this->pathFinder->getConfigurationPath());
        $this->init();
    }

    public function getPluginName()
    {
        return $this->pluginName;
    }

    public function getConfig($name, $default = null)
    {
        return $this->config->getConfig($name, $default);
    }

    public function setConfig($config = [], $defautl = UNDEFINED)
    {
        $this->config->setConfig($config, $defautl);
    }

    public function getTemplateLocation($fileName)
    {
        return $this->pathFinder->getTemplateLocation($fileName);
    }

    public function getDbConnection()
    {
        return $this->dbConnection;
    }

    public function setDbConnection()
    {
        $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME;
        $this->dbConnection = new Persistence_SQL($dsn, DB_USER, DB_PASSWORD);
    }

    public function getWpComponentId()
    {
        return $this->wpComponent['id'];
    }

    /**
     * Plugin Entry point
     * Wordpress plugin file call this function in order to have
     * atk work under Wordpress.
     *
     * Will load panel, metab box, widget and shortcode configuration file;
     * Setup proper Wp action for each of them;
     * Setup WP Ajax.
     *
     * @param string $filePath the path to wp plugin file.
     */
    public function boot($filePath)
    {
        //setup plugin activation / deactivation hook.
        register_activation_hook($filePath, [$this, 'activatePlugin']);
        register_deactivation_hook($filePath, [$this, 'deactivatePlugin']);

        //setup component services.
        $this->componentCtrl->initializeComponents($this);

        //register ajax action for this plugin
        add_action("wp_ajax_{$this->getPluginName()}", [$this, 'wpAjaxExecute']);
    }

    /**
     * Plugin Initialize function.
     */
    public function init()
    {
    }

    /*--------------------- OUTPUT -------------------------------*/

    /**
     * Output Panel view in Wp.
     */
    public function wpAdminExecute()
    {
        global $hook_suffix;
        $this->wpComponent = $this->componentCtrl->getComponentByType('panel', $hook_suffix, 'hook');

        try {
            $app = new AtkWpApp($this);
            $app->initWpLayout($this->wpComponent, $this->defaultLayout, $this->pluginName);
            $app->execute();
        } catch (Exception $e) {
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
        $this->wpComponent = $this->componentCtrl->getComponentByKey($_REQUEST['atkwp']);
        if (isset($_GET['atkshortcode'])) {
            //$this->stickyGet('atkshortcode');
        }

        try {
            //check_ajax_referer($this->pluginName);
            $app = new AtkWpApp($this);
            $app->page = 'admin-ajax';
            $app->initWpLayout($this->wpComponent, $this->defaultLayout, $this->pluginName);
            $app->execute($this->ajaxMode);
        } catch (Exception $e) {
            $this->caughtException($e);
        }
    }

    /**
     * Output metabox view in Wp.
     *
     * @param \WP_Post $post  The wordpress post.
     * @param array    $param The param set in metabox configuration.
     *
     * @throws Exception
     */
    public function wpMetaBoxExecute(\WP_Post $post, array $param)
    {
        //set the view to output.
        $this->wpComponent = $this->componentCtrl->getComponentByType('metaBox', $param['id']);

        try {
            $app = new AtkWpApp($this);
            $metaBox = $app->initWpLayout($this->wpComponent, $this->defaultLayout, $this->pluginName);
            $metaBox->addMetaArguments($param['args']);
            $metaBox->setFieldInput($post->ID, $this->componentCtrl);
            $app->execute();
        } catch (Exception $e) {
            $this->caughtException($e);
        }
    }

    /**
     * Create a new Atk View.
     * This view is fully initialize with an atk application.
     * WidgetComponent use this to create a view for Widget.
     *  - You can echo this view using $view->app->execute().
     *
     * @param string $template the template to use with this view.
     * @param string $name     the name of the application.
     *
     * @return \atk4\ui\View
     */
    public function newAtkAppView($template, $name)
    {
        $this->wpComponent['uses'] = 'atk4\ui\View';
        $app = new AtkWpApp($this);

        return $app->initWpLayout($this->wpComponent, $template, $name);
    }

    /**
     * Catch exception.
     *
     * @param $exception
     *
     * @throws Exception
     */
    public function caughtException($exception)
    {
        $view = $this->newAtkAppView('layout.html', $this->pluginName);
        if ($exception instanceof \atk4\core\Exception) {
            $view->template->setHTML('Content', $exception->getHTML());
        } elseif ($exception instanceof \Error) {
            $view->add(new View([
                'ui'=> 'message',
                get_class($exception).': '.$exception->getMessage().' (in '.$exception->getFile().':'.$exception->getLine().')',
                'error',
                ])
            );
            $view->add(new Text())->set(nl2br($exception->getTraceAsString()));
        } else {
            $view->add(new View(['ui'=>'message', get_class($exception).': '.$exception->getMessage(), 'error']));
        }
        $view->template->tryDel('Header');
        $view->app->execute();
    }
}
