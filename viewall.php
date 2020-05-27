<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\GridView;
use yii\widgets\Pjax;
?>

<div class="panel-body">
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-9">
        <h4><?= Yii::t('AdminModule.views_surveys_index', 'Survey(s) Taken'); ?></h4>
        <h5><?= Yii::t('AdminModule.views_surveys_index', 'List of the surveys that this user has taken'); ?></h5>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>
  <div class="panel-body" style="overflow-x:scroll; white-space: nowrap;">

    <?php
    Pjax::begin(['id' => 'pjax-grid-view']);
    echo GridView::widget([
      'id' => 'project-grid',
      'dataProvider' => $dataProvider,
      'tableOptions' => ['class' => 'table table-hover'],
      'columns' => [
        [
          'attribute' => 'survey_id',
          'label' => Yii::t('AdminModule.views_suveys_index', 'Survey ID'),
          'value' => function ($data) {
            return $data->survey_id;
          }
        ],
        [
          'class' => 'yii\grid\ActionColumn',
          'header' => Yii::t('AdminModule.views_surveys_index', 'Actions'),
          'template' => '{view} {ingest}',
          'buttons' => [
            'view' => function ($url, $model) {
              return Html::a('<i class="fa fa-eye"></i> View Survey(s) Responses', Url::to($url), [
                'title' => Yii::t('AdminModule.views_surveys_index', 'View Survey Responses'),
                'class' => 'btn btn-primary btn-xs tt'
              ]);
            },
          ],
        ],
      ],
    ]);
    Pjax::end();
    ?>
  </div>