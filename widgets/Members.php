<?php
namespace humhub\modules\selbstseinCommunity\widgets;

use humhub\modules\selbstseinCommunity\Policy;
use humhub\helpers\Html;

class Members extends \yii\base\Widget
{
    public $space;

    public function run()
    {
        if (!Policy::allowed($this->space)) {
            return '';
        }
        return Html::tag('div', Html::tag('div', 'Mitglieder', ['class' => 'panel-heading'])
            . Html::tag('div', Html::a('Mitgliederliste ansehen', $this->space->createUrl('/selbstsein-community/settings/members'), [
                'data-action-click' => 'ui.modal.load',
                'data-action-url' => $this->space->createUrl('/selbstsein-community/settings/members'),
            ]), ['class' => 'panel-body']), ['class' => 'panel panel-default']);
    }
}
