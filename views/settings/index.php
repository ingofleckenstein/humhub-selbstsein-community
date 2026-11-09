<?php
use humhub\widgets\form\ActiveForm;
use humhub\helpers\Html;
use humhub\modules\selbstseinCommunity\Policy;
?>
<div class="panel panel-default">
<div class="panel-heading">Selbstsein Community</div>
<div class="panel-body">
<h4>Sichtbarkeit der Space-Mitglieder</h4>
<p>Wer darf die Mitgliederliste dieses Spaces sehen? Space-Admins und Community-Admins haben immer Zugriff. Der Space selbst wird dadurch nicht zugänglicher.</p>
<?php if (!$configured): ?><p>Bis zum ersten Speichern gelten die bisherigen HumHub-Einstellungen.</p><?php endif; ?>
<?php if (Yii::$app->request->get('saved')): ?><div class="alert alert-success">Einstellung gespeichert.</div><?php endif; ?>
<?php $form = ActiveForm::begin(); ?>
<?= $form->field($model, 'visibility')->dropDownList(Policy::OPTIONS)->label('Mitgliederliste sichtbar für') ?>
<?= Html::submitButton('Speichern', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end(); ?>
</div></div>
