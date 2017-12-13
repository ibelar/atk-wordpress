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
 * Creates Metaboxes in Wordpress post admin page.
 */

namespace atkwp\components;

use atk4\ui\Exception;
use atkwp\controllers\MetaFieldController;
use atkwp\interfaces\ComponentCtrlInterface;
use atkwp\interfaces\MetaBoxFieldsInterface;
use atkwp\interfaces\MetaFieldInterface;

class MetaBoxComponent extends Component
{
    /**
     * The controller need to add input field in metabox.
     *
     * @var MetaFieldInterface
     */
    public $fieldCtrl;

    /**
     * The arguments set in config-metabox file.
     *
     * @var null
     */
    public $args = null;

    /**
     * MetaBoxComponent constructor.
     *
     * Note: You can override this constructor in your plugin file in order to setup your
     * own MetaField controller.
     *
     * @param null                    $label
     * @param null                    $class
     * @param MetaFieldInterface|null $fieldCtrl
     *
     * @throws Exception
     */
    public function __construct($label = null, $class = null, MetaFieldInterface $fieldCtrl = null)
    {
        parent::__construct($label, $class);

        $this->fieldCtrl = $fieldCtrl;
        if ($this instanceof MetaBoxFieldsInterface) {
            if (!$this->fieldCtrl) {
                $this->fieldCtrl = new MetaFieldController();
            }
            $this->onInitMetaBoxFields($this->fieldCtrl);
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
     *
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
