<?php
/**
 * Wordpress input field.
 */

namespace atkwp\ui;

use atk4\ui\Template;
use atk4\ui\View;
use atkwp\components\Component;

class Input extends Component
{
    public $id = '';
    public $value;
    public $inputCssClass = 'widefat';
    public $defaultTemplate = 'wp-input.html';
    public $inputType = 'text';
    public $placeholder = null;
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
