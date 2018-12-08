<?php
namespace humhub\modules\user\widgets;

/**
 * UserSurveysWidget lists all surveys of the user
 */
class UserSurveys extends \yii\base\Widget
{
    public $user;
    
    public function run()
    {
        return $this->render('userSurveys', ['user' => $this->user]);
    }
}
?>