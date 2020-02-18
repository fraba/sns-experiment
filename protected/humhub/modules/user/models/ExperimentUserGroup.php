<?php

namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;

class ExperimentUserGroup extends ActiveRecord
{

    public static function tableName()
    {
        return 'experiment_user_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [['user_id', 'group_id'], 'int'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('AdminModule.models_ExperimentGroup', 'User Id'),
            'group_id' => Yii::t('AdminModule.models_ExperimentGroup', 'Experiment Group Id'),
        ];
    }

    public function getUser()
    {
        $user = $this->hasOne(User::class, ['id' => 'user_id'])->one();
        $user->getProfile();
        return $user;
    }
}
