<?php

namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\ExperimentGroup;
use Yii;

class Experiment extends ActiveRecord
{

    // public $start;
    // public $end;
    public $groups;

    public static function tableName()
    {
        return 'experiment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['start', 'end'], "string", "max" => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'start' => Yii::t('AdminModule.models_Experiment', 'Start Time'),
            'end' => Yii::t('AdminModule.models_Experiment', 'End Time'),
        ];
    }

    public function getExperimentGroup()
    {
        return $this->hasMany(ExperimentGroup::class, ['experiment_id' => 'id'])->all();
    }
}
