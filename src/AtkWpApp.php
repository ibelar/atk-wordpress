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

    /**
     * The plugin running this app.
     *
     * @var AtkWp
     */
    public $plugin;

    /**
     * The html produce by this app.
     *
     * @var string
     */
    public $wpHtml;

    /**
     * The maximum number of letter of atk element name.
     *
     * @var int
     */
    public $max_name_length = 60;

    /**
     * The default directory name of atk template.
     *
     * @var string
     */
    public $skin = 'semantic-ui';

    /**
     * atk view initialisation.
     */
    public function init()
    {
        parent::init();
    }

    /**
     * AtkWpApp constructor.
     *
     * @param AtkWp|null $plugin
     * @param UI|null    $uiPersistance
     */
    public function __construct(AtkWp $plugin = null)
    {
        $this->plugin = $plugin;
        if (!isset($uiPersistance)) {
            $this->ui_persistence = new UI();
        } else {
            $this->ui_persistence = $uiPersistance;
        }
    }

    /**
     * The layout initialisation for each Wp component.
     *
     * @param $view
     * @param $layout
     * @param $name
     *
     * @throws Exception
     *
     * @return \atk4\ui\View The Wp component being output.
     */
    public function initWpLayout($view, $layout, $name)
    {
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

    /**
     * Return url.
     *
     * @param array $page
     * @param bool  $hasRequestUri
     * @param array $extraArgs
     *
     * @return array|null|string
     * TODO fix url for jsREdirect and other action like menu.
     */
    public function url($page = [], $hasRequestUri = false, $extraArgs = [])
    {
        $result = $extraArgs;

        if (is_string($page)) {
            return $page;
        }

        $this->page = 'admin-ajax';

        //if running front end set url for ajax.
        if (!WpUtil::isAdmin()) {
            $this->page = WpUtil::getBaseAdminUrl().'admin-ajax';
        }
        $sticky = $this->sticky_get_arguments;
        $sticky['action'] = $this->plugin->getPluginName();
        $sticky['atkwp'] = $this->plugin->getWpComponentId();

        if ($this->plugin->getComponentCount() > 0) {
            $sticky['atkwp-count'] = $this->plugin->getComponentCount();
        }

        if ($this->plugin->config->getConfig('plugin/use_nounce', false)) {
            $sticky['_ajax_nonce'] = helpers\WpUtil::createWpNounce($this->plugin->getPluginName());
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

    /**
     * Return javascript action.
     *
     * @param $app_view
     *
     * @throws Exception
     *
     * @return string
     */
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
