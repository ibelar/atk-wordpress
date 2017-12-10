<?php
/**
 * Responsible for creating and registering all WP action
 * needed for dashboard.
 */

namespace atkwp\services;

use atkwp\interfaces\ComponentCtrlInterface;

class DashboardService
{
    private $ctrl;
    protected $executable;

    public $dashboards = [];


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
        $configureCallback = null;
        //create metaBoxes using closure function.
        add_action('wp_dashboard_setup', function () use ($key, $dashboard) {

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
