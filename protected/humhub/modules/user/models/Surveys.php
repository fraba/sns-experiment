<?php

namespace humhub\modules\user\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\components\ActiveQueryUser;
use Yii;

class Surveys extends ActiveRecord
{
    public $user;

    public static function tableName()
    {
        return 'user_surveys';
    }

    // /**
    //  * @inheritdoc
    //  */
    // public function rules()
    // {
    //     return [
    //         [[
    //             'pol_op', 'pol_op_abo', 'pol_op_imm', 'pol_op_gay', 'pol_op_eco', 'int_abo_sur', 'int_gay_sur',
    //             'int_eco_sur', 'int_imm_sur', 'int_abo_obs', 'int_gay_obs', 'int_eco_obs', 'int_imm_obs'
    //         ], 'integer'],
    //         //[['description'], 'string'],
    //         [['user_email'], 'string', 'max' => 255],
    //     ];
    // }

    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['survey_id', 'survey_data'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('UserModule.models_User', 'User Id'),
            'survey_id' => Yii::t('UserModule.models_User', 'Survey Id'),
            'survey_data' => Yii::t('UserModule.models_User', 'Survey Data'),
            // 'pol_op' => Yii::t('UserModule.models_User', 'pol_op'),
            // 'pol_op_abo' => Yii::t('UserModule.models_User', 'pol_op_abo'),
            // 'pol_op_imm' => Yii::t('UserModule.models_User', 'pol_op_imm'),
            // 'pol_op_gay' => Yii::t('UserModule.models_User', 'pol_op_gay'),
            // 'pol_op_eco' => Yii::t('UserModule.models_User', 'pol_op_eco'),
            // 'int_abo_sur' => Yii::t('UserModule.models_User', 'int_abo_sur'),
            // 'int_gay_sur' => Yii::t('UserModule.models_User', 'int_gay_sur'),
            // 'int_eco_sur' => Yii::t('UserModule.models_User', 'int_eco_sur'),
            // 'int_imm_sur' => Yii::t('UserModule.models_User', 'int_imm_sur'),
            // 'int_abo_obs' => Yii::t('UserModule.models_User', 'int_abo_obs'),
            // 'int_gay_obs' => Yii::t('UserModule.models_User', 'int_gay_obs'),
            // 'int_eco_obs' => Yii::t('UserModule.models_User', 'int_eco_obs'),
            // 'int_imm_obs' => Yii::t('UserModule.models_User', 'int_imm_obs'),
        ];
    }

    public function getUser()
    {
        $user = $this->hasOne(User::class, ['id' => 'user_id'])->one();
        $this->user = $user;

        return $user;
    }
}
