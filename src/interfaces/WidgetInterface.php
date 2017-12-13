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
 * Widget component interfaces.
 * WidgetComponent must implement this interface in order to use atk view inside it.
 * WidgetComponent can be use without implementing this interface, if so, they are simply regular Wp_Widget class.
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
