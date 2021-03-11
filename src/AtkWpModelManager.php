<?php

namespace atkwp;

use atkwp\helpers\WpUtil;
use atkwp\models\Model;

class AtkWpModelManager
{
    /**
     * @var Model[]
     */
    private $models = [];
    /**
     * @var AtkWp
     */
    private $atkwp;

    public function __construct(AtkWp $atkwp)
    {
        $this->atkwp = $atkwp;
    }

    public function addModel(string $fqcn_model)
    {
        $name = strtolower((new \ReflectionClass($fqcn_model))->getShortName());
        $this->models[$fqcn_model] = new $fqcn_model(
            $this->atkwp->getDbConnection(),
            $this->getTableName($name)
        );
    }

    private function getTableName(string $name)
    {
        return strtolower(
            sprintf("%s_%s_%s",
                WpUtil::getDbPrefix(),
                $this->atkwp->getPluginName(),
                $name,
            )
        );
    }

    public function upgradeDb()
    {
        // process models statement for dbDelta
        foreach ($this->models as $model) {
            // check if the model allow upgrade
            if ($model->isEnabledDbDelta()) {

                $stmt = $statement = sprintf(
                    "CREATE TABLE `%s` (" . PHP_EOL . "%s" . PHP_EOL . ")" . PHP_EOL . "COLLATE {%s}",
                    $model->table,
                    $model->getSQLSchema(),
                    WpUtil::getDbCharsetCollate()
                );

                dbDelta($stmt);
            }
        }
    }

    public function getModel(string $model_fqcn)
    {
        return clone $this->models[$model_fqcn];
    }
}