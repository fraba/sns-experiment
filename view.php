<?php

use humhub\widgets\Button;

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;

$MODULE = 'AdminModule.views_surveys_view';

$survey_responses = unserialize($survey->survey_data);

$email = $survey->user->email;

?>

<div class="panel-body">
    <?= Button::defaultType(Yii::t($MODULE, 'Back to Surveys List'))->link(Url::to(['/admin/surveys']))->left()->sm(); ?>
    <div class="clearfix"></div>
    <hr>
    <div>
        <h3>Survey Responses (<?= $survey->survey_id ?>)</h3>
        <p>
            <?= $email ?>
        </p>
        <hr>

    </div>

    <div class="row">
    </div>

    <?php
    echo DetailView::widget([
        'model' => $survey_responses,
    ]);
    ?>
</div>