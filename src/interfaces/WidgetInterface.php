<?php
/**
 * Widget component interfaces.
 */

namespace atkwp\interfaces;

use atk4\ui\View;
use atkwp\AtkWpView;

interface WidgetInterface
{
    /**
     * Called by the Widget component class on WP_Widget::widget() method.
     * This method will be called prior to echo the html for this view, allowing
     * developer to add other element to this view.
     *
     * @param AtkWpView $view The atk view
     * @param $instance
     *
     * @return AtkWpView $view The view to echo in widget() method.
     */
    public function onWidget(View $view, $instance);

    /**
     * Called by the Widget component class on WP_Widget::form() method.
     * This method will be called prior to echo the html for this view, allowing
     * developer to add field input to the view.
     *
     * @param AtkWpView $view
     * @param $instance
     *
     * @return AtkWpView $view The view to echo in form() method.
     */
    public function onForm(View $view, $instance);
}