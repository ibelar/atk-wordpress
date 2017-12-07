<?php
/**
 * Widget component interfaces.
 * You may still extends WidgetComponent without using this interface, however
 * doing so will result in using the \Wp_Widget class as usual, i.e. you
 * will have to overide the appropriate \Wp_Widget class method as in a normal
 * plugin development.
 */

namespace atkwp\interfaces;

use atk4\ui\Exception;
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
     * @param View  $view
     * @param array $instance
     *
     * @throws Exception
     *
     * @return AtkWpView $view The view to echo in form() method.
     */
    public function onForm(View $view, $instance);

    /**
     * Called by the Widget component class on WP_Widget::update() method.
     * This method is called prior to update the instance value in db.
     *
     * @param array $newInstance The instance array with new value from user.
     * @param array $oldInstance The instance with previous saved db value.
     *
     * @return array The instance with value to save in db.
     */
    public function onUpdate($newInstance, $oldInstance);
}
