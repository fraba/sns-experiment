<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\GridView;
use humhub\widgets\Modal;
use yii\widgets\Pjax;
?>

<?php

Modal::begin([
    'id' => 'surveys-modal',
    'size' => 'modal-md',
    'header' => '<h3>Select surveys</h3>',
]);
echo '<div id="surveysModalContent"></div>';
Modal::end();
?>
<div class="panel-body">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-9">
                <h4><?= Yii::t('AdminModule.views_surveys_index', 'Manage survey results'); ?></h4>
            </div>
            <div class="col-md-3">
                <?= Html::button('Ingest Surveys', [
                    'value' => Yii::$app->urlManager->createUrl(['/admin/surveys/selectsurveys', 'user_id' => -1]),
                    'class' => 'btn btn-primary btn-surveys-modal',
                    'title' => 'Ingest surveys for all users (this may take a while)',
                    'data-toggle' => 'modal',
                    'data-target' => '#surveys-modal',
                ])
                ?>
            </div>
        </div>
    </div>
    <div id="new_note"></div>
    <div id="wait" style="display:none;position:absolute;top:50%;left:50%;padding:2px; z-index: 1000;">
        <img src='static/img/ajaxloader.gif' />
    </div>

    <?php if (Yii::$app->session->hasFlash('survey_ingest_info')) : ?>
        <div class="alert alert-info" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            <?= Yii::$app->session->getFlash('survey_ingest_info'); ?>
        </div>
    <?php endif; ?>


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
                    'attribute' => 'user_id',
                    'label' => Yii::t('AdminModule.views_suveys_index', 'User'),
                    'value' => function ($data) {
                        $user = $data->getUser();
                        return $user !== NULL ? $user->email : "(survey data found - user not found - probably deleted?)";
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('AdminModule.views_surveys_index', 'Actions'),
                    'template' => '{view} {ingest}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="fa fa-eye"></i> View Survey(s) Responses', Url::to(['/admin/surveys/viewall', 'user_id' => $model->user_id]), [
                                'title' => Yii::t('AdminModule.views_surveys_index', 'View Survey Responses'),
                                'class' => 'btn btn-primary btn-xs tt'
                            ]);
                        },
                        // 'ingest' => function ($url, $model) {
                        //     return Html::a('<i class="fa fa-eye"></i> Ingest Surveys', Url::to(["/admin/surveys/selectsurveys", 'user_id' => $model->user_id]), [
                        //         'title' => Yii::t('AdminModule.views_surveys_index', 'Ingest surveys for this user'),
                        //         'class' => 'btn btn-primary btn-xs tt',
                        //         'data-toggle' => 'modal',
                        //         'data-target' => '#surveys-modal',
                        //     ]);
                        // }
                        'ingest' => function ($url, $model) {
                            return Html::button('Ingest Surveys', [
                                'value' => Yii::$app->urlManager->createUrl(['/admin/surveys/selectsurveys', 'user_id' => $model->user_id]),
                                'class' => 'btn btn-primary btn-xs btn-surveys-modal',
                                'data-toggle' => 'modal',
                                'data-target' => '#surveys-modal',
                            ]);
                        }
                    ],
                ],
            ],
        ]);
        Pjax::end();
        ?>
    </div>

    <script>
        const loaderHTML = `
            <div class="loader humhub-ui-loader">            
                <div class="sk-spinner sk-spinner-three-bounce">
                    <div class="sk-bounce1"></div>
                    <div class="sk-bounce2"></div>
                    <div class="sk-bounce3"></div>
                </div>
            </div>
        `;

        $(document).ready(function(e) {
            $('.btn-surveys-modal').click(function(e) {
                e.preventDefault();
                $('#surveys-modal').modal('show')
                    .find('.modal-body')
                    .html(loaderHTML)
                    .load($(this).attr('value'));
                return false;
            });
        });
    </script>