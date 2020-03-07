<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\GridView;
use yii\widgets\Pjax;

?>
<div class="panel-body">
    <h4><?= Yii::t('AdminModule.views_experiments_index', 'Manage experiments'); ?></h4>
    <div id="new_note"></div>
    <div id="wait" style="display:none;position:absolute;top:50%;left:50%;padding:2px; z-index: 1000;">
        <img src='static/img/ajaxloader.gif' />
    </div>
    <div class="pull-right">
        <?= Html::a('<i class="fa fa-plus" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('AdminModule.views_experiments_index', 'Create Experiment'), Url::to(['create']), ['class' => 'btn btn-sm btn-success']); ?>
    </div>

    <div class="clearfix"></div>
    <div class="panel-body" style="overflow-x:scroll; white-space: nowrap;">

        <?php
        Pjax::begin(['id' => 'pjax-grid-view']);
        echo GridView::widget([
            'id' => 'experiment-grid',
            'dataProvider' => $dataProvider,
            'tableOptions' => ['class' => 'table table-hover'],
            'columns' => [
                'id',
                [
                    'attribute' => 'start',
                    'format' => ['date', 'php:m/d/Y']
                ],
                [
                    'attribute' => 'end',
                    'format' => ['date', 'php:m/d/Y']
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('AdminModule.views_experiments_index', 'Actions'),
                    'template' => '{view} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="fa fa-eye"></i> View', Url::to($url), [
                                'title' => Yii::t('AdminModule.views_experiments_index', 'View Experiment'),
                                'class' => 'btn btn-primary btn-xs tt'
                            ]);
                        },
                        'update' => function ($url, $model) {
                            return false;
                        },
                        'delete' => function ($url, $model) {
                            return Html::a('<i class="fa fa-times"></i>', Url::to($url), [
                                'title' => Yii::t('AdminModule.views_experiments_index', 'Remove Experiment'),
                                'class' => 'btn btn-danger btn-xs tt'
                            ]);
                        }
                    ],
                ],
            ],
        ]);
        Pjax::end();
        ?>
    </div>