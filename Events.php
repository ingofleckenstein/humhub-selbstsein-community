<?php
namespace humhub\modules\selbstseinCommunity;

use humhub\modules\space\models\Space;
use humhub\modules\space\controllers\MembershipController;
use humhub\modules\space\widgets\Members;
use humhub\modules\ui\menu\MenuLink;
use yii\web\ForbiddenHttpException;

class Events
{
    public static function beforeAction($event): void
    {
        $controller = $event->sender;
        if (!$controller instanceof MembershipController) {
            return;
        }
        $space = $controller->contentContainer;
        if ($space instanceof Space && Policy::get($space) !== null
            && in_array($event->action->id, ['members-list', 'search'], true)
            && !Policy::allowed($space)) {
            throw new ForbiddenHttpException('Die Mitgliederliste ist für dich nicht sichtbar.');
        }
    }

    public static function menu($event): void
    {
        $menu = $event->sender;
        if (Policy::admin($menu->space)) {
            $menu->addEntry(new MenuLink([
                'label' => 'Selbstsein Community', 'icon' => 'eye', 'sortOrder' => 150,
                'url' => $menu->space->createUrl('/selbstsein-community/settings/index'),
            ]));
        }
    }

    public static function sidebar($event): void
    {
        $sidebar = $event->sender;
        if (Policy::get($sidebar->space) !== null && Policy::allowed($sidebar->space)) {
            $sidebar->addWidget(widgets\Members::class, ['space' => $sidebar->space], ['sortOrder' => 30]);
        }
    }
}
