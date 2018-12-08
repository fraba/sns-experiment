<?php
//use yii\helpers\Html;
//use yii\helpers\Url;

if(Yii::$app->user->identity->isSystemAdmin()){  
    $user_email = $user->email;
    $connection = Yii::$app->getDb();
    $command = $connection->createCommand("select * from _user_surveys where user_email = :user_email", [':user_email' => $user_email]);
    $result = $command->queryAll();
    if($result){
?>
    <div id="user-tags-panel" class="panel panel-default" style="position: relative;">

        <?php echo \humhub\widgets\PanelMenu::widget(['id' => 'user-tags-panel']); ?>
        <div class="panel-heading"><?php echo Yii::t('UserModule.widgets_views_userTags', '<strong>User</strong> surveys'); ?></div>
        <div class="panel-body">
            <!-- start: Surveys for user -->
            <ul class="list-group">
            <?php foreach($result as $r){ ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    pol_op: <span class="badge badge-primary badge-pill"><?php echo $r['pol_op']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    pol_op_abo: <span class="badge badge-primary badge-pill"><?php echo $r['pol_op_abo']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    pol_op_imm: <span class="badge badge-primary badge-pill"><?php echo $r['pol_op_imm']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    pol_op_gay: <span class="badge badge-primary badge-pill"><?php echo $r['pol_op_gay']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    pol_op_eco: <span class="badge badge-primary badge-pill"><?php echo $r['pol_op_eco']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_abo_sur: <span class="badge badge-primary badge-pill"><?php echo $r['int_abo_sur']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_gay_sur: <span class="badge badge-primary badge-pill"><?php echo $r['int_gay_sur']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_eco_sur: <span class="badge badge-primary badge-pill"><?php echo $r['int_eco_sur']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_imm_sur: <span class="badge badge-primary badge-pill"><?php echo $r['int_imm_sur']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_abo_obs: <span class="badge badge-primary badge-pill"><?php echo $r['int_abo_obs']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_gay_obs: <span class="badge badge-primary badge-pill"><?php echo $r['int_gay_obs']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_eco_obs: <span class="badge badge-primary badge-pill"><?php echo $r['int_eco_obs']; ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    int_imm_obs: <span class="badge badge-primary badge-pill"><?php echo $r['int_imm_obs']; ?></span>
                </li>
            <?php } ?>
            </ul>
            <!-- end: Surveys for user skills -->
        </div>
    </div>
<?php } } ?>

<script type="text/javascript">
    function toggleUp() {
        $('.pups').slideUp("fast", function () {
            // Animation complete.
            $('#collapse').hide();
            $('#expand').show();
        });
    }

    function toggleDown() {
        $('.pups').slideDown("fast", function () {
            // Animation complete.
            $('#expand').hide();
            $('#collapse').show();
        });
    }
</script>