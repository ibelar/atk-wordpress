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

use atk4\ui\Template;
use atk4\ui\View;
use atkwp\components\Component;

class Input extends Component
{
    /**
     * The field id.
     *
     * @var string
     */
    public $id = '';

    /**
     * The field value.
     *
     * @var mixed
     */
    public $value;

    /**
     * The field input css class name.
     *
     * @var string
     */
    public $inputCssClass = 'widefat';

    /**
     * The field default template file.
     *
     * @var string
     */
    public $defaultTemplate = 'wp-input.html';

    /**
     * The field default input html type.
     *
     * @var string
     */
    public $inputType = 'text';

    /**
     * The field input html placeholder attribute value.
     *
     * @var null||string
     */
    public $placeholder = null;

    /**
     * The field name.
     *
     * @var string
     */
    public $fieldName;

    public function getInput()
    {
        $inputProperties = [
            'name'        => $this->fieldName,
            'type'        => $this->inputType,
            'placeholder' => $this->placeholder,
            'id'          => $this->id,
            'value'       => $this->value,
            'class'       => $this->inputCssClass,
        ];

        if ($this->inputType === 'checkbox' && $this->value) {
            $inputProperties['checked'] = 'checked';
        }

        return $this->app->getTag('input', $inputProperties);
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
     * Render field html.
     *
     * @throws \atk4\ui\Exception
     */
    public function renderView()
    {
        switch ($this->inputCssClass) {
            case 'tiny-text':
                $location = 'after_label';
                break;
            case 'checkbox':
                $location = 'before_label';
                break;
            default:
                $location = 'in_label';
        }

        $input = new View(['template' => new Template('{Input}<input class="{$class}" type="{input_type}text{/}" {$input_attributes}/>{/}')]);
        $input->template->setHTML('Input', $this->getInput());
        $input->content = null;

        $this->add($input, $location);

        $this->template->trySet('field_name', $this->fieldName);

        parent::renderView();
    }
}
