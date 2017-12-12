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
 * Licensed under MIT
 * =====================================================================*/
/**
 * The agile toolkit application needed to add and output Wp component views.
 */

namespace atkwp;

use atk4\ui\App;
use atk4\ui\Exception;
use atk4\ui\Persistence\UI;
use atk4\ui\Template;
use atkwp\helpers\WpUtil;

class AtkWpApp extends App
{
    use \atk4\core\SessionTrait;

    //The pluggin running this app
    public $plugin;

    //The html produce by this app
    public $wpHtml;

    public $max_name_length = 20;

    public $skin = 'semantic-ui';

    public function init()
    {
        parent::init();
    }

    public function __construct(AtkWp $plugin = null)
    {
        $this->plugin = $plugin;
        if (!isset($this->ui_persistence)) {
            $this->ui_persistence = new UI();
        }
    }

    public function initWpLayout($view, $layout, $name)
    {
        //$class = '\\'.$component['uses'];
        $this->wpHtml = new AtkWpView(['defaultTemplate' => $layout, 'name' => $name]);
        $this->wpHtml->app = $this;
        $this->wpHtml->init();

        return $this->wpHtml->add($view);
    }

    /**
     * Runs app and echo rendered template.
     *
     * @param bool $isAjax
     *
     * @throws \atk4\core\Exception
     */
    public function execute($isAjax = false)
    {
        echo $this->render($isAjax);
    }

    /**
     * Take care of rendering views.
     *
     * @param $isAjax
     *
     * @throws \atk4\core\Exception
     *
     * @return mixed
     */
    public function render($isAjax)
    {
        $this->hook('beforeRender');
        $this->is_rendering = true;
        $this->wpHtml->renderAll();
        $this->wpHtml->template->appendHTML('HEAD', $this->getJsReady($this->wpHtml));
        $this->is_rendering = false;
        $this->hook('beforeOutput');

        return $this->wpHtml->template->render();
    }

    /**
     * Return the db connection run by this plugin.
     *
     * @return mixed
     */
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
        $result = [];

        $this->page = 'admin-ajax';
        if (!WpUtil::isAdmin()) {
            $this->page = WpUtil::getBaseAdminUrl().'admin-ajax';
        }
        $sticky = $this->sticky_get_arguments;
        $sticky['action'] = $this->plugin->getPluginName();
        $sticky['atkwp'] = $this->plugin->getWpComponentId();

        if ($this->plugin->getComponentCount() > 0) {
            $sticky['atkwp-count'] = $this->plugin->getComponentCount();
        }

        if ($this->plugin->config->getConfig('plugin/use_nounce')) {
            $sticky['_ajax_nonce'] = helpers\WpUtil::createWpNounce($this->plugin->getPluginName());
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
     * Load template file.
     *
     * @param string $name
     *
     * @throws Exception
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
