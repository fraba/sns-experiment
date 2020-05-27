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

    /**
     * Assigns a user to a group or unassigns it if it's already part of the group
     * @param integer $group_id 
     * @param integer $user_id
     */
    public static function assignToGroup($group_id, $user_id)
    {
        $user_group = ExperimentUserGroup::findOne(['group_id' => $group_id, 'user_id' => $user_id]);

        if ($user_group === NULL) {
            $exp_user_group = new ExperimentUserGroup;
            $exp_user_group->group_id = $group_id;
            $exp_user_group->user_id = $user_id;
            $exp_user_group->save();
            return;
        }

        $user_group->delete();
    }
}
