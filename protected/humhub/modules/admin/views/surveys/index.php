<?php

use yii\helpers\Url;
use yii\helpers\Html;
use humhub\widgets\GridView;
use humhub\modules\admin\models\CsvForm;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
?>
<div class="panel-body">
    <h4><?= Yii::t('AdminModule.views_surveys_index', 'Manage survey results'); ?></h4>
    <div id="new_note"></div>
    <div id="wait" style="display:none;position:absolute;top:50%;left:50%;padding:2px; z-index: 1000;">
        <img src='static/img/ajaxloader.gif' />
    </div>


    <?php // echo \humhub\modules\admin\widgets\GroupMenu::widget(); 
    ?>
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
                        return $user->email;
                    }
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => Yii::t('AdminModule.views_surveys_index', 'Actions'),
                    'template' => '{view}',
                    'buttons' => [
                        'view' => function ($url, $model) {
                            return Html::a('<i class="fa fa-eye"></i> View Survey Responses', Url::to($url), [
                                'title' => Yii::t('AdminModule.views_surveys_index', 'View Survey Responses'),
                                'class' => 'btn btn-primary btn-xs tt'
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
        $(document).ready(function(e) {
            $("#form").on('submit', (function(e) {
                e.preventDefault();
                $.ajax({
                    url: '<?php echo Url::toRoute(["surveys/csvimport"]); ?>', //' surveys/csvimport",
                    type: "POST",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    beforeSend: function() {
                        $("#wait").css("display", "block");
                    },
                    success: function(response) {
                        $("#wait").css("display", "none");
                        $('#new_note').html(response);
                        $.pjax({
                            container: '#pjax-grid-view'
                        });
                        setTimeout("$('#new_note').hide();", 4000);
                    },
                });
            }));
        });
    </script>