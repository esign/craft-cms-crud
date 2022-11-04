<?php
/**
 * Module for Craft CMS 4.x
 *
 * @link      esign.eu
 * @copyright Copyright (c) 2022 esign
 */

namespace modules\module;

use Craft;
use Yii;
use yii\base\Module;

/**
 * Class Module
 *
 * @author    dieter vanhove
 * @package   Module
 * @since     1.0.0
 *
 */
class ExampleModule extends Module
{
    public static $instance;

    public function init()
    {
        // Set instance of this module
        self::$instance = $this;

        parent::init();

        $this->registerAliasses();
        $this->registerControllers();
    }

    public function registerAliasses()
    {
        Yii::setAlias($this->formatYiiAlias('@' . __NAMESPACE__), '@root/modules/{module}');
    }

    public function formatYiiAlias($alias)
    {
        return str_replace('\\', '/', $alias);
    }

    public function registerControllers()
    {
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'modules\\module\\console\\controllers';
        } else {
            $this->controllerNamespace = 'modules\\module\\controllers';
        }
    }
}
