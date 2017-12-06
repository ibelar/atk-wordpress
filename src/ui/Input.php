<?php
/**
 * Wordpress input field.
 */

namespace atkwp\ui;

use atk4\ui\View;
use atk4\ui\Template;
use atkwp\components\Component;

class Input extends Component
{
    public $id;
    public $value;
    public $wpClass = 'widefat';
    public $defaultTemplate = 'wp-input.html';
    public $type = 'text';
    public $placeholder = null;
    public $fieldName;

    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'        => $this->fieldName,
            'type'        => $this->type,
            'placeholder' => $this->placeholder,
            'id'          => $this->fieldName,
            'value'       => $this->value,
            'class'       => $this->wpClass,
        ]);
    }

    public function renderView()
    {
        $location = ($this->wpClass === 'widefat')? 'in_label' : 'out_label';
        $input = new View(['template' => new Template('{Input}<input class="{$class}" type="{input_type}text{/}" {$input_attributes}/>{/}')]);
        $input->template->setHTML('Input', $this->getInput());
        $input->content = null;

        $this->add($input, $location);

        $this->template->trySet('field_name', $this->fieldName);

        parent::renderView();
    }
}
