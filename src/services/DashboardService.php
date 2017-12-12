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
 * Responsible for creating and registering all WP action
 * needed for dashboard.
 */

namespace atkwp\services;

use atkwp\interfaces\ComponentCtrlInterface;

class DashboardService
{
    /**
     * The component controller responsible of initiating this service.
     *
     * @var ComponentCtrlInterface
     */
    private $ctrl;

    /**
     * The executable need to output dashboard component within Wp.
     *
     * @var callable
     */
    protected $executable;

    /**
     * The dashboards registered within this services.
     *
     * @var array
     */
    public $dashboards = [];

    /**
     * DashboardService constructor.
     *
     * @param ComponentCtrlInterface $ctrl
     * @param array                  $dashboards
     * @param callable               $callable
     */
    public function __construct(ComponentCtrlInterface $ctrl, array $dashboards, $callable)
    {
        $this->ctrl = $ctrl;
        $this->executable = $callable;
        $this->setDashboards($dashboards);
        $this->registerDashboards();

        add_action('admin_init', function () {
            $this->ctrl->registerComponents('dashboard', $this->getDashboards());
        });
    }

    /**
     * Return all dashboards.
     *
     * @return array
     */
    public function getDashboards()
    {
        return $this->dashboards;
    }

    /**
     * Perform some initialisation to our dashboard prior to register them.
     *
     * @param array $dashboards
     */
    public function setDashboards($dashboards)
    {
        //add id key to our panels
        foreach ($dashboards as $key => $dashboard) {
            $dashboards[$key]['id'] = $key;
        }
        $this->dashboards = $dashboards;
    }

    /**
     * Register each dashboard.
     */
    public function registerDashboards()
    {
        if (isset($this->dashboards)) {
            foreach ($this->dashboards as $key => $dashboard) {
                $this->registerDashboard($key, $dashboard);
            }
        }
    }

    /**
     * The actual dashboard registration in Wp.
     *
     * @param string $key
     * @param array  $dashboard
     */
    public function registerDashboard($key, $dashboard)
    {
        //create dashboard using closure function.
        add_action('wp_dashboard_setup', function () use ($key, $dashboard) {
            $configureCallback = null;
            if ($dashboard['configureMode']) {
                $configureCallback = function () use ($key, $dashboard) {
                    call_user_func_array($this->executable, [$key, $dashboard, true]);
                };
            }

            wp_add_dashboard_widget($key,
                $dashboard['title'],
                $this->executable,
                $configureCallback
            );
        });
        $this->dashboards[$key] = $dashboard;
    }
}
