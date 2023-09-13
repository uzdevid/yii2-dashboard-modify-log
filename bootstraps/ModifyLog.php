<?php

namespace uzdevid\dashboard\modify\log\bootstraps;

use uzdevid\dashboard\events\ModuleEvent;
use uzdevid\dashboard\modify\log\controllers\ModifyLogController;
use uzdevid\dashboard\modules\system\Module;
use yii\base\BootstrapInterface;

class ModifyLog implements BootstrapInterface {
    public function bootstrap($app) {
        $app->on(Module::EVENT_AFTER_INIT, function (ModuleEvent $event) {
            $event->module->controllerMap['modify-log'] = ModifyLogController::class;
        });
    }
}