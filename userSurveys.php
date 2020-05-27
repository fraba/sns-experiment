<?php
//use yii\helpers\Html;
//use yii\helpers\Url;

use humhub\modules\user\models\Surveys;

if (Yii::$app->user->identity->isSystemAdmin()) {
    $user_id = $user->id;
    $surveys = Surveys::findAll(['user_id' => $user_id]);
    if ($surveys) {
?>
        <div id="user-tags-panel" class="panel panel-default" style="position: relative;">

            <?php echo \humhub\widgets\PanelMenu::widget(['id' => 'user-tags-panel']); ?>
            <div class="panel-heading"><?php echo Yii::t('UserModule.widgets_views_userTags', '<strong>User</strong> surveys'); ?></div>
            <div class="panel-body">
                <!-- start: Surveys for user -->
                <?php
                foreach ($surveys as $survey) :
                ?>
                    <ul class="list-group">
                        <?php
                        $responses = unserialize($survey->survey_data);
                        foreach ($responses as $key => $value) :
                        ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?php echo $key ?> <span class="badge badge-primary badge-pill"><?php echo $value; ?></span>
                            </li>
                        <?php
                        endforeach;
                        ?>
                    </ul>
                    <!-- end: Surveys for user skills -->
                <?php endforeach; ?>
            </div>
        </div>
<?php
    }
}
?>

<script type="text/javascript">
    function toggleUp() {
        $('.pups').slideUp("fast", function() {
            // Animation complete.
            $('#collapse').hide();
            $('#expand').show();
        });
    }

    function toggleDown() {
        $('.pups').slideDown("fast", function() {
            // Animation complete.
            $('#expand').hide();
            $('#collapse').show();
        });
    }
</script>