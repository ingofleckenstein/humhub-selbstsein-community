<?php
use humhub\modules\selbstseinCommunity\Events;
use humhub\modules\space\widgets\HeaderControlsMenu;
use humhub\modules\space\widgets\Sidebar;
use yii\base\Controller;

return [
    'id' => 'selbstsein-community',
    'class' => humhub\modules\selbstseinCommunity\Module::class,
    'namespace' => 'humhub\\modules\\selbstseinCommunity',
    'events' => [
        [Controller::class, Controller::EVENT_BEFORE_ACTION, [Events::class, 'beforeAction']],
        [\yii\base\Controller::class, \yii\base\Controller::EVENT_AFTER_ACTION, [Events::class, 'afterAction']],
        [\yii\base\View::class, \yii\base\View::EVENT_AFTER_RENDER, [Events::class, 'advancedField']],
        [Sidebar::class, Sidebar::EVENT_INIT, [Events::class, 'sidebar']],
    ],
];
