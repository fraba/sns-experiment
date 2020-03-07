<?php

use humhub\compat\CActiveForm;
use humhub\compat\CHtml;
use humhub\models\Setting;
?>

<?php $this->beginContent('@admin/views/setting/_advancedLayout.php') ?>

<?php $form = CActiveForm::begin(); ?>

<?= $form->errorSummary($model); ?>

<div class="form-group">
    <?= $form->labelEx($model, 'api_url'); ?>
    <?= $form->textField($model, 'api_url', ['class' => 'form-control']); ?>
</div>

<div class="form-group">
    <?= $form->labelEx($model, 'api_token'); ?>
    <?= $form->textField($model, 'api_token', ['class' => 'form-control']); ?>
</div>

<div class="form-group">
    <?= $form->labelEx($model, 'survey_id'); ?>
    <?= $form->textField($model, 'survey_id', ['class' => 'form-control']); ?>
</div>

<hr>
<?= CHtml::submitButton(Yii::t('AdminModule.views_setting_redcap', 'Save'), ['class' => 'btn btn-primary', 'data-ui-loader' => ""]); ?>

<?= \humhub\widgets\DataSaved::widget(); ?>
<?php CActiveForm::end(); ?>

<?php $this->endContent(); ?>