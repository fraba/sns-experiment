<?php

use yii\widgets\ActiveForm;
use humhub\widgets\ModalButton;
use yii\helpers\Url;

?>

<?php
$name = "";
$email = "";
$ingest_for_str = "all users";
$user_id = (int) $user_id;

if ($user_id !== -1) {
  $name = ucfirst($user_profile->firstname) . " " . ucfirst($user_profile->lastname);
  $email = $user->email;
  $ingest_for_str = "$name ($email)";
}

if ($model) :
?>
  <p>Select the surveys you want to ingest for <?= $ingest_for_str ?>.</p>
  <?php if ($user_id === -1) : ?>
    <p><b>WARNING:</b> Ingesting surveys for all users may take a long time</p>
  <?php endif ?>
  <?php

  $form = ActiveForm::begin(["id" => "surveys-form", "action" => "/admin/surveys/ingest"]);

  foreach ($surveys as $key => $value) {
    echo $form->field($model, $key)->checkbox();
  }

  ?>

  <div class="modal-footer">
    <?= ModalButton::submitModal(Url::to(['/admin/surveys/ingest', 'user_id' => $user_id]), Yii::t('AdminModule.views_surveys_index', 'Ingest Survey(s)')); ?>
  </div>

<?php
  ActiveForm::end();

endif;
