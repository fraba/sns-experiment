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
        $groups = $this->hasMany(ExperimentGroup::class, ['experiment_id' => 'id'])->all();
        foreach ($groups as $group) {
            $group->getUsers();
        }
        $this->groups = $groups;
        return $groups;
    }

    public static function getOngoingExperiments()
    {
        $date = date("Y-m-d h:i:s");
        return self::find()->where([">", "end", $date]);
    }

    public static function getAllForUserId($user_id, $ongoing = true)
    {
        $now = date("Y-m-d h:i:s");

        $experiments = self::find()->innerJoin("experiment_group", "experiment.id = experiment_group.experiment_id")
            ->innerJoin("experiment_user_group", "experiment_group.id = experiment_user_group.group_id")
            ->where([">", "experiment.end", $now])
            ->andWhere(["=", "experiment_user_group.user_id", $user_id])
            ->all();

        foreach ($experiments as $e) {
            $e->getExperimentGroup();
        }

        return $experiments;
    }

    public static function getOngoingForUserId($user_id, $ongoing = true)
    {
        $now = date("Y-m-d h:i:s");

        $experiment = self::find()->innerJoin("experiment_group", "experiment.id = experiment_group.experiment_id")
            ->innerJoin("experiment_user_group", "experiment_group.id = experiment_user_group.group_id")
            ->where([">", "experiment.end", $now])
            ->andWhere(["=", "experiment_user_group.user_id", $user_id])
            ->one();

        $experiment->getExperimentGroup();

        return $experiment;
    }
}
