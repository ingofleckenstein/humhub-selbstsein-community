<?php
namespace humhub\modules\selbstseinCommunity\controllers;

use humhub\modules\content\components\ContentContainerController;
use humhub\modules\space\models\Space;
use humhub\modules\selbstseinCommunity\Policy;
use humhub\modules\user\widgets\UserListBox;
use yii\web\ForbiddenHttpException;
use yii\base\DynamicModel;
use Yii;

class SettingsController extends ContentContainerController
{
    public $validContentContainerClasses = [Space::class];

    public function actionIndex()
    {
        $space = $this->contentContainer;
        if (!Policy::admin($space)) {
            throw new ForbiddenHttpException();
        }
        $model = new DynamicModel(['visibility' => Policy::get($space) ?? 'admins']);
        $model->addRule('visibility', 'required')->addRule('visibility', 'in', ['range' => array_keys(Policy::OPTIONS)]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $space->settings->set('selbstseinMemberVisibility', $model->visibility);
            // Keep Core widgets and join/leave activity creation disabled for every audience.
            $space->settings->set('hideMembers', true);
            return $this->redirect($space->createUrl('/selbstsein-community/settings/index', ['saved' => 1]));
        }
        return $this->render('index', ['model' => $model, 'configured' => Policy::get($space) !== null]);
    }

    public function actionMembers()
    {
        $space = $this->contentContainer;
        if (Policy::get($space) === null || !Policy::allowed($space)) {
            throw new ForbiddenHttpException();
        }
        return $this->renderAjaxContent(UserListBox::widget([
            'query' => $space->getMemberListService()->getQuery(), 'title' => 'Mitglieder',
        ]));
    }
}
