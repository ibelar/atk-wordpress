<?php
/**
 * Created by abelair.
 * Date: 2017-11-20
 * Time: 10:21 AM
 */

namespace atkwp\components;


use atkwp\controllers\MetaFieldController;
use atkwp\interfaces\ComponentCtrlInterface;
use atkwp\interfaces\MetaBoxArgumentsInterface;
use atkwp\interfaces\MetaBoxFieldsInterface;
use atkwp\interfaces\MetaFieldInterface;

class MetaBoxComponent extends Component
{
    public $fieldCtrl;

    /**
     * MetaBoxComponent constructor.
     *
     * Note: You can override this constructor in your plugin file in order to setup your
     * own MetaField controller.
     *
     * @param null $label
     * @param null $class
     * @param MetaFieldInterface|null $fieldCtrl
     */
    public function __construct( $label = null, $class = null, MetaFieldInterface $fieldCtrl = null)
    {
        parent::__construct( $label, $class );

        $this->fieldCtrl = $fieldCtrl;
        if ($this instanceof MetaBoxFieldsInterface) {
            if (!$this->fieldCtrl) {
                $this->fieldCtrl = new MetaFieldController();
            }
            $this->onInitMetaBoxFields($this->fieldCtrl);
        }
    }


    public function addMetaArguments($args)
    {
        if ($this instanceof MetaBoxArgumentsInterface) {
            $this->onMetaBoxArguments($args);
        }
    }


    public function setFieldInput($postId, ComponentCtrlInterface $compCtrl)
    {
        if ($this->fieldCtrl) {
            foreach ($this->fieldCtrl->getFields() as $key => $field) {
                $field->set($compCtrl->getPostMetaData($postId, $field->short_name, true));
            }
        }
    }

    /**
     * Called from the action hook added by the MetaBox service.
     * @param $postId
     */
    public function savePost($postId, ComponentCtrlInterface $compCtrl)
    {
        if ($this->fieldCtrl) {
            foreach ($this->fieldCtrl->getFields() as $key => $field) {
                $compCtrl->savePostMetaData($postId, $field->short_name, $this->onUpdateMetaFieldRawData($key, $_POST[$field->short_name]));
            }
        }
    }
}