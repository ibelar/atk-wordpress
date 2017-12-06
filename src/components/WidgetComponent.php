<?php
/**
 * Wordpress Widget.
 */

namespace atkwp\components;


use atk4\ui\Exception;
use atkwp\AtkWp;
use atkwp\AtkWpApp;
use atkwp\AtkWpView;

class WidgetComponent extends \WP_Widget
{
    public $id;
    public $plugin;
    public $widgetConfig;

    public function __construct($idBase = null, $name = 'atkDefaultName', $widgetOtions = [], $controlOptions = [])
    {
        parent::__construct($idBase, $name, $widgetOtions, $controlOptions);
    }

    /**
     * Pre initialisation of our widget.
     * Call from the WidgetService on widget registration in WP.
     * Call directly after widget creation.
     *
     * @param $id
     * @param array $config
     * @param AtkWp $plugin
     */
    public function initializeWidget($id, array $config, AtkWp $plugin)
    {
        $this->plugin = $plugin;
        $this->name = $config['title'];
        //make sure our id_base is unique
        $this->id_base = $plugin->name.'_wdg_'.$id;
        //Widget option_name in Option table that will hold the widget instance field value.
        $this->option_name = 'widget_'.$this->id_base;
        $this->widget_options = wp_parse_args($config['widget_ops'], ['classname' => $this->option_name]);
        $this->control_options = wp_parse_args($config['widget_control_ops'], ['id_base' => $this->id_base]);
        // Our widget definition
        $this->widgetConfig = $config;
        //Add the id value to our widget definition.
        $this->widgetConfig['id'] = $id;

    }

    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        $title = apply_filters('widget_title', $this->widgetConfig['title']);
        if (!empty( $title)) {
            echo $args['before_title'].$title.$args['after_title'];
        }

        try {
            $view = $this->onWidget($this->plugin->newAtkAppView('widget.html', $this->widgetConfig['id']), $args, $instance);
            if (!$view) {
                throw new Exception('Method onForm() should return the container view');
            }
            $view->app->execute();
        } catch (Exception $e) {
            $this->plugin->caughtException($e);
        }

        echo $args['after_widget'];
    }

    public function form($instance)
    {
        try {
            $view = $this->onForm($this->plugin->newAtkAppView('widget.html', $this->widgetConfig['id']), $instance);
            if (!$view) {
                throw new Exception('Method onForm() should return the container view');
            }
            $view->app->execute();
        } catch (Exception $e) {
            $this->plugin->caughtException($e);
        }
    }
}