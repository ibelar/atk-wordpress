<?php
/**
 * Created by abelair.
 * Date: 2017-06-09
 * Time: 1:04 PM.
 */

namespace atkwp;

use atk4\ui\App;
use atk4\ui\Persistence\UI;
use atk4\ui\Template;

class AtkWpApp extends App
{
    use \atk4\core\SessionTrait;

    //The pluggin running this app
    public $plugin;

    //The html produce by this app
    public $wpHtml;

    public $skin = 'semantic-ui';

    public function init()
    {
        parent::init();
    }

    public function __construct(AtkWp $plugin)
    {
        $this->plugin = $plugin;
        if (!isset($this->ui_persistence)) {
            $this->ui_persistence = new UI();
        }
    }

    public function initWpLayout($component)
    {
        $class = '\\'.$component['uses'];
        $this->wpHtml = new AtkWpView(['defaultTemplate' => 'layout.html', 'name' => $this->plugin->getPluginName()]);
        $this->wpHtml->app = $this;
        $this->wpHtml->init();

        return $this->wpHtml->add(new $class());
    }

    /**
     * Runs app and echo rendered template.
     */
    public function execute($isAjax = false)
    {
        //$this->run_called = true;
        $this->hook('beforeRender');
        $this->is_rendering = true;
        //$this->html->template->set('title', $this->title);
        $this->wpHtml->renderAll();
        $this->wpHtml->template->appendHTML('HEAD', $this->getJsReady($this->wpHtml));
        //$this->wpHtml->getJS(/*$isAjax*/);
        //$this->wpHtml->template->appendHTML('HEAD', $this->wpHtml->getJS());
        $this->is_rendering = false;
        $this->hook('beforeOutput');
        echo $this->wpHtml->template->render();
    }

    public function getDbConnection()
    {
        return $this->plugin->getDbConnection();
    }

    /**
     * Will perform a preemptive output and terminate. Do not use this
     * directly, instead call it form Callback, jsCallback or similar
     * other classes.
     *
     * @param string $output
     */
    public function terminate($output = null)
    {
        echo $output;
        $this->run_called = true; // prevent shutdown function from triggering.
        exit;
    }

    public function url($page = [], $hasRequestUri = false)
    {
        // $url = admin_url('admin-ajax.php');
        $sticky = $this->sticky_get_arguments;
        $result = [];

        if ($this->page === null) {
            //$this->page = basename($this->getRequestURI(), '.php');
            $this->page = 'admin-ajax';
        }

        if ($this->page === 'admin-ajax') {
            $sticky['action'] = $this->plugin->getPluginName();
            $sticky['atkwp'] = $this->plugin->getWpComponentId();
        }

        if (is_string($page)) {
            return $page;
        }

        if (!isset($page[0])) {
            $page[0] = $this->page;

            if (is_array($sticky) && !empty($sticky)) {
                foreach ($sticky as $key => $val) {
                    if ($val === true) {
                        if (isset($_GET[$key])) {
                            $val = $_GET[$key];
                        } else {
                            continue;
                        }
                    }
                    if (!isset($result[$key])) {
                        $result[$key] = $val;
                    }
                }
            }
        }

        foreach ($page as $arg => $val) {
            if ($arg === 0) {
                continue;
            }

            if ($val === null || $val === false) {
                unset($result[$arg]);
            } else {
                $result[$arg] = $val;
            }
        }

        $page = $page[0];

        $url = $page ? $page.'.php' : '';

        $args = http_build_query($result);

        if ($args) {
            $url = $url.'?'.$args;
        }

        return $url;
    }

    public function getJsReady($app_view)
    {
        $actions = [];

        foreach ($app_view->_js_actions as $eventActions) {
            foreach ($eventActions as $action) {
                $actions[] = $action;
            }
        }

        if (!$actions) {
            return '';
        }

        $actions['indent'] = '';
        $ready = new \atk4\ui\jsFunction(['$'], $actions);

        return "<script>jQuery(document).ready({$ready->jsRender()})</script>";
    }

    /**
     * Load template.
     *
     * @param string $name
     *
     * @return Template
     */
    public function loadTemplate($name)
    {
        $template = new Template();
        $template->app = $this;

        return $template->load($this->plugin->getTemplateLocation($name));
    }
}
