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
    <img src='static/img/ajaxloader.gif'/>
</div>
    <div class="pull-right">     
    <?php 
        $model = new CsvForm;
        $form = ActiveForm::begin([
                //'action' => ['surveys/csvimport'],
                'options' => ['enctype'=>'multipart/form-data', 'id'=>'form', 'class'=>'form-inline']
            
            ]); ?>
    <?= $form->field($model,'csv_file')->fileInput(); ?>
    <div class="form-group">
        <?= Html::submitButton('Upload CSV',['class'=>'btn btn-sm btn-success subm']) ?>
        <?php echo $form->errorSummary($model); ?>
    </div>
<?php ActiveForm::end(); ?>
     
    <div class="help-block">
        <?php //echo Yii::t('AdminModule.views_surveys_index', 'Users can be assigned to different groups (e.g. teams, departments etc.) with specific standard spaces, group managers and permissions.'); ?>
    </div>
</div>

<?php // echo \humhub\modules\admin\widgets\GroupMenu::widget(); ?>
<div class="clearfix"></div>
<div class="panel-body" style="overflow-x:scroll; white-space: nowrap;">

    <?php
    Pjax::begin(['id' => 'pjax-grid-view']);
    echo GridView::widget([
        'id'=>'project-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-hover'],
        'columns' => [
            'user_email',
            'pol_op',
            'pol_op_abo',
            'pol_op_imm',
            'pol_op_gay',
            'pol_op_eco',
            'int_abo_sur',
            'int_gay_sur',
            'int_eco_sur',
            'int_imm_sur',
            'int_abo_obs',
            'int_gay_obs',
            'int_eco_obs',
            'int_imm_obs',
            /*
            [
                'attribute' => 'members',
                'label' => Yii::t('AdminModule.views_surveys_index', 'Members'),
                'format' => 'raw',
                'options' => ['style' => 'text-align:center;'],
                'value' => function ($data) {
                    return $data->getGroupUsers()->count();
                }
            ],
            [
                'class' => \humhub\libs\ActionColumn::class,
                'actions' => function($group, $key, $index) {
                    /* @var $group \humhub\modules\user\models\Group *//*
                    if($group->is_admin_group && !Yii::$app->user->isAdmin()) {
                        return [];
                    }

                    return  [
                        Yii::t('AdminModule.user', 'Settings') => ['edit'],
                        '---',
                        Yii::t('AdminModule.user', 'Permissions') => ['manage-permissions'],
                        Yii::t('AdminModule.user', 'Members') => ['manage-group-users'],
                    ];
                }
            ],*/
        ],
    ]);
    Pjax::end();
    ?>
</div>

 <script>
 $(document).ready(function (e) {
 $("#form").on('submit',(function(e) {
  e.preventDefault();
  $.ajax({
         url: '<?php echo Url::toRoute(["surveys/csvimport"]); ?>', //' surveys/csvimport",
   type: "POST",
   data:  new FormData(this),
   contentType: false,
         cache: false,
   processData:false,
   beforeSend : function(){$("#wait").css("display", "block"); }, 
   success: function(response)
      {
             $("#wait").css("display", "none");
		     $( '#new_note' ).html(response);
             $.pjax({container: '#pjax-grid-view'});
             setTimeout( "$('#new_note').hide();", 4000);
      },          
    });
 }));
});
</script>