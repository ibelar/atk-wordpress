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
 * Wordpress input field.
 */

namespace atkwp\ui;

use atk4\ui\FormField\Generic;
use atk4\ui\Template;
use atk4\ui\View;

class WpInput extends Generic
{
    /**
     * The input id attr.
     * When use in a widget,
     * must be set to Wp_Widget::get_field_id().
     *
     * @var string
     */
    public $field_id = null;

    /**
     * The input name attr value.
     * When use in a widget,
     * must be set to Wp_Widget::get_field_name().
     *
     * @var string
     */
    public $field_name = null;

    /**
     * The input value.
     *
     * @var mixed
     */
    public $value;

    /**
     * The input label.
     *
     * @var string
     */
    public $label;

    /**
     * The input css class name.
     *
     * @var string
     */
    public $css = 'widefat';

    /**
     * The default template file.
     *
     * @var string
     */
    public $defaultTemplate = 'wp-input.html';

    /**
     * The input html type.
     *
     * @var string
     */
    public $type = 'text';

    /**
     * The field input html placeholder attribute value.
     *
     * @var null||string
     */
    public $placeholder = null;

    public function init()
    {
        parent::init();
        $this->content = $this->label;
    }

    /**
     * Set input field_name
     * When use in a Widget, must be set to
     * Wp_Widget::get_field_name().
     *
     * @param $name
     */
    public function setFieldName($name)
    {
        $this->field_name = $name;
    }

    /**
     * Set input field_id
     * When use in a Widget, must be set to
     * Wp_Widget::get_field_id().
     *
     * @param string $id
     */
    public function setFieldId($id)
    {
        $this->field_id = $id;
    }

    /**
     * Set Input label.
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->content = $label;
    }

    /**
     * Set field value.
     *
     * @param null $value
     * @param null $junk
     *
     * @return $this|void
     */
    public function set($value = null, $junk = null)
    {
        $this->setValue($value);
    }

    /**
     * Set field value.
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Template input rendering.
     *
     * @return string
     */
    public function getInput()
    {
        $inputProperties = [
            'id'          => $this->field_id,
            'name'        => $this->field_name,
            'type'        => $this->type,
            'placeholder' => $this->placeholder,
            'value'       => $this->value,
            'class'       => $this->css,
        ];

        if ($this->type === 'checkbox' && $this->value) {
            $inputProperties['checked'] = 'checked';
        }

        return $this->app->getTag('input', $inputProperties);
    }

    /**
     * Render field html.
     *
     * @throws \atk4\ui\Exception
     */
    public function renderView()
    {
        switch ($this->css) {
            case 'tiny-text':
                $location = 'after_label';
                break;
            case 'checkbox':
                $location = 'before_label';
                break;
            default:
                $location = 'in_label';
        }

        // Set field name and id if not use in Widget.
        if (!$this->field_name) {
            $this->field_name = $this->short_name;
        }

        if (!$this->field_id) {
            $this->field_id = $this->short_name;
        }

        $input = new View(['template' => new Template('{Input}<input class="{$class}" type="{input_type}text{/}" {$input_attributes}/>{/}')]);
        $input->template->setHTML('Input', $this->getInput());
        $input->content = null;

        $this->add($input, $location);

        $this->template->trySet('input_name', $this->field_id);

        parent::renderView();
    }
}
