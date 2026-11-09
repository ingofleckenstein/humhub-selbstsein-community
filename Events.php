<?php
namespace humhub\modules\selbstseinCommunity;

use humhub\modules\space\models\Space;
use humhub\modules\space\controllers\MembershipController;
use humhub\modules\space\widgets\Members;
use humhub\modules\ui\menu\MenuLink;
use yii\web\ForbiddenHttpException;

class Events
{
    private static array $pending = [];

    public static function beforeAction($event): void
    {
        $controller = $event->sender;
        if ($controller instanceof \humhub\modules\space\modules\manage\controllers\DefaultController
            && $event->action->id === 'advanced' && \Yii::$app->request->isPost) {
            $space = $controller->contentContainer;
            $value = \Yii::$app->request->post('selbstseinMemberVisibility');
            if ($value !== null && $value !== '') {
                if (!Policy::admin($space) || !is_string($value) || !isset(Policy::OPTIONS[$value])) {
                    throw new \yii\web\BadRequestHttpException('Ungültige Mitglieder-Sichtbarkeit.');
                }
                $post = \Yii::$app->request->post();
                $post['AdvancedSettings']['hideMembers'] = '1';
                \Yii::$app->request->setBodyParams($post);
                self::$pending[spl_object_id($controller)] = [$space, $value];
            }
        }
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

    public static function afterAction($event): void
    {
        $id = spl_object_id($event->sender);
        if (!isset(self::$pending[$id])) { return; }
        [$space, $value] = self::$pending[$id];
        unset(self::$pending[$id]);
        if ($event->result instanceof \yii\web\Response && $event->result->isRedirection
            && !$space->getAdvancedSettings()->hasErrors()) {
            $space->settings->set('selbstseinMemberVisibility', $value);
        }
    }

    public static function advancedField($event): void
    {
        $model = $event->params['model'] ?? null;
        if (!$model instanceof \humhub\modules\space\models\AdvancedSettings
            || basename($event->viewFile) !== 'advanced.php') { return; }
        $value = Policy::get($model->space);
        $options = Policy::OPTIONS;
        if ($value === null) {
            $options = ['' => 'Bisherige Einstellung beibehalten'] + $options;
        }
        $field = \humhub\helpers\Html::tag('div',
            \humhub\helpers\Html::label('Mitgliederliste sichtbar für', 'selbstsein-member-visibility')
            . \humhub\helpers\Html::dropDownList('selbstseinMemberVisibility', $value ?? '', $options,
                ['id' => 'selbstsein-member-visibility', 'class' => 'form-control'])
            . \humhub\helpers\Html::tag('p', 'Space-Admins und Community-Admins haben immer Zugriff. Personen ohne Anmeldung erhalten keinen Zugriff.', ['class' => 'help-block']),
            ['class' => 'form-group']);
        $event->output = preg_replace('~<div\b[^>]*class="[^"]*field-advancedsettings-hidemembers[^"]*"[^>]*>.*?</div>~s', $field, $event->output, 1);
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
