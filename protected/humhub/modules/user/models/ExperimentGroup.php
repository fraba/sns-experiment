<?php

namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\ExperimentUserGroup;
use Yii;

class ExperimentGroup extends ActiveRecord
{
    public $csv_file;
    public $users;

    public static function tableName()
    {
        return 'experiment_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['topic_in', 'pol_op_in'], 'string'],
            [['size', 'topic_probability', 'pol_op_probability'], 'integer'],
            [['csv_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'csv'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'topic_in' => Yii::t('AdminModule.models_ExperimentGroup', 'Topic In'),
            'pol_op_in' => Yii::t('UserModule.models_ExperimentGroup', 'Pol Op In'),
            'experiment_id' => Yii::t('UserModule.models_ExperimentGroup', 'Experiment Id'),
        ];
    }

    public function getUsers()
    {
        $users = $this->hasMany(ExperimentUserGroup::class, ['group_id' => 'id'])->orderBy("user_id")->all();
        $this->users = $users;
        return $users;
    }
}
