<?php


use humhub\libs\Html;
use humhub\widgets\Button;
use humhub\modules\user\models\Experiment;
use yii\jui\DatePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use wbraganca\dynamicform\DynamicFormWidget;

$js = '
jQuery(".dynamicform_experiment_group_wrapper").on("afterInsert", function(e, item) {
    console.log("da");
    jQuery(".group-container__header > h3").each(function(index) {
        jQuery(this).html("Group: " + (index + 1))
    });
});

jQuery(".dynamicform_experiment_group_wrapper").on("afterDelete", function(e) {
    jQuery(".group-container__header > h3").each(function(index) {
        jQuery(this).html("Group: " + (index + 1))
    });
});
';

$this->registerJs($js);


?>

<div class="panel-body">
    <div class="clearfix">
        <div class="pull-right">
            <?= Button::defaultType(Yii::t('AdminModule.views_experiments_create', 'Back to Experiments'))->link(Url::to(['/admin/experiments']))->right()->sm(); ?>
        </div>

        <h4 class="pull-left"><?= Yii::t('AdminModule.views_experiments_create', 'Create Experiment'); ?></h4>
    </div>
    <br>
    <?php $model = new Experiment; ?>
    <?php $form = ActiveForm::begin(['id' => 'edit-experiment-form', 'layout' => 'horizontal', 'options' => ['enctype' => 'multipart/form-data']]); ?>
    <h3>Timeframe</h3>
    <div class="form-group">
        <?= $form->field($model, 'start')->widget(
            DatePicker::classname(),
            [
                'dateFormat' => 'php:Y-m-d',
                'options' => ['readonly' => true, 'class' => 'form-control'],
                'clientOptions' => [
                    'todayHighlight' => true,
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => date('Y') . ':' . date('Y'),
                    'minDate' => 0,
                    'onSelect' => new yii\web\JsExpression('function(){
                    console.log("test");
                    onFromDateSelected();
                }')
                ]
            ]
        ) ?>
    </div>

    <div class="form-group">
        <?= $form->field($model, 'end')->widget(
            DatePicker::classname(),
            [
                'dateFormat' => 'php:Y-m-d',
                'options' => ['readonly' => true, 'class' => 'form-control'],
                'clientOptions' => [
                    'todayHighlight' => true,
                    'changeMonth' => true,
                    'changeYear' => true,
                    'yearRange' => date('Y') . ':' . date('Y'),
                    'minDate' => 0
                ]
            ]
        ) ?>
    </div>

    <?php
    DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_experiment_group_wrapper',
        'widgetBody' => '.container-items',
        'widgetItem' => '.item',
        'min' => 1,
        'insertButton' => '.experiment-add-item', // css class
        'deleteButton' => '.experiment-remove-item', // css class
        'model' => $modelsExperimentGroup[0],
        'formId' => 'edit-experiment-form',
        'formFields' => [
            'size',
            'topic_in',
            'topic_probability',
            'pol_op_in',
            'pol_op_probability'
        ],
    ]);
    ?>
    <div class="container-items">
        <?php foreach ($modelsExperimentGroup as $index => $modelExperimentGroup) : ?>
            <div class="item panel panel-default">
                <div class="panel-heading group-container__header">
                    <h3 class="panel-title pull-left">Group 1</h3>
                    <div class="group-container__controls pull-right">
                        <button type="button" class="btn btn-sm btn-default experiment-remove-item">Remove Group</button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <?php
                    if (!$modelExperimentGroup->isNewRecord) {
                        echo Html::activeHiddenInput($modelExperimentGroup, "[{$index}]id");
                    }
                    ?>
                    <?= $form->field($modelExperimentGroup, "[{$index}]size")->textInput(['type' => 'number', 'min' => 1]); ?>
                    <div class="experiments__flex-container">
                        <p>OR</p>
                    </div>
                    <?= $form->field($modelExperimentGroup, "[{$index}]csv_file")->fileInput(); ?>
                    <h4>Filter</h4>
                    <?= $form->field($modelExperimentGroup, "[{$index}]topic_in")->dropDownList(array_merge([NULL => 'Select Topic'], $topics)); ?>
                    <?= $form->field($modelExperimentGroup, "[{$index}]topic_probability")->textInput(['type' => 'number', 'min' => 0, 'max' => 100]); ?>
                    <?= $form->field($modelExperimentGroup, "[{$index}]pol_op_in")->dropDownList(array_merge([NULL => 'Select Political Opinion'], $topics)); ?>
                    <?= $form->field($modelExperimentGroup, "[{$index}]pol_op_probability")->textInput(['type' => 'number', 'min' => 0, 'max' => 100]); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="experiments__flex-container">
        <button type="button" class="btn btn-sm btn-default experiment-add-item">+ Add Group</button>
        <div class="clearfix"></div>
    </div>
    <div class="clearfix"></div>

    <?php DynamicFormWidget::end(); ?>
    <div class="experiments__flex-container">
        <?= Html::submitButton(Yii::t('AdminModule.views_experiments_create', 'Create Experiment'), ['class' => 'btn btn-sm btn-primary']); ?>
        <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<style>
    .experiments__flex-container {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
</style>

<script>
    function onFromDateSelected() {
        const selectedFromDate = $('#<?= Html::getInputId($model, 'start') ?>').datepicker("getDate");
        $('#<?= Html::getInputId($model, 'end') ?>').datepicker("option", "minDate", selectedFromDate);
    }
</script>