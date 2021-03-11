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
 * Base model.
 */

namespace atkwp\models;

use atkwp\helpers\WpUtil;

abstract class Model extends \atk4\data\Model
{
    public $wp_table;

    protected $dbdelta_enabled = true;

    public function init(): void
    {
        if (!empty($this->wp_table)) {
            $this->table = WpUtil::getDbPrefix().$this->wp_table;
        }

        parent::init();
    }

    /**
     * Used during installation of plugin, if Model schema need to be processed via dbDelta.
     *
     * @return bool
     */
    public function isEnabledDbDelta(): bool
    {
        return $this->dbdelta_enabled;
    }

    /**
     * Return internal declaration of SQL Schema.
     *
     * Ex : return "
     * `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
     * `type` VARCHAR(255) NOT NULL DEFAULT '',
     * `imported` int(11) NOT NULL DEFAULT 0,
     * `date` DATE NOT NULL,
     * PRIMARY KEY  (`id`)
     * "
     *
     * @return string
     */
    abstract public function getSQLSchema(): string;
}
