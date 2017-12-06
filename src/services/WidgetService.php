<?php
/**
 * Setup Wordpress to use Widget with atk.
 */

namespace atkwp\services;

use atkwp\interfaces\ComponentCtrlInterface;

class WidgetService
{
    private $ctrl;
    private $plugin;

    public function __construct(ComponentCtrlInterface $ctrl, $widgets, $plugin)
    {
        $this->ctrl = $ctrl;
        $this->plugin = $plugin;
        $this->registerWidgets($widgets);
    }

    /**
     * Get widgets defined in config-widget file
     * and register them.
     *
     * @param array $widgets the widgets to register.
     */
    public function registerWidgets($widgets = null)
    {
        if (isset($widgets)) {
            foreach ($widgets as $key => $widget) {
                $this->registerWidget($key, $widget);
            }
        }
    }

    /**
     * Register each widget within Wordpress.
     * Once register perform initialisation on them in order
     * for Wordpress widget class to work with atk.
     *
     * @param $id
     * @param $widget
     */
    public function registerWidget($id, $widget)
    {
        add_action('widgets_init', function () use ($id, $widget) {
            global $wp_widget_factory;
            register_widget($widget['uses']);
            //get latest create widget in widget factory
            $wdg = end($wp_widget_factory->widgets);
            // pre init latest widget.
            $wdg->initializeWidget($id, $widget, $this->plugin);
        });
    }
}
