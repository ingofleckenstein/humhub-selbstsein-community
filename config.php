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
        [HeaderControlsMenu::class, HeaderControlsMenu::EVENT_INIT, [Events::class, 'menu']],
        [Sidebar::class, Sidebar::EVENT_INIT, [Events::class, 'sidebar']],
    ],
];
