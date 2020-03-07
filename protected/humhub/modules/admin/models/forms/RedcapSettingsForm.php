<?php

namespace humhub\modules\admin\models\forms;

use Yii;

/**
 * RedcapSettingsForm
 */
class RedcapSettingsForm extends \yii\base\Model
{

    public $api_url;
    public $api_token;
    public $survey_id;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $settingsManager = Yii::$app->settings;
        $this->api_url = $settingsManager->get('redcap.apiurl');
        $this->api_token = $settingsManager->get('redcap.apitoken');
        $this->survey_id = $settingsManager->get('redcap.surveyid');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_url', 'api_token', 'survey_id'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'api_url' => Yii::t('AdminModule.forms_RedcapSettingsForm', 'API URL'),
            'api_token' => Yii::t('AdminModule.forms_RedcapSettingsForm', 'API Token'),
            'survey_id' => Yii::t('AdminModule.forms_RedcapSettingsForm', 'Survey Id')
        ];
    }

    /**
     * Saves the form
     *
     * @return boolean
     */
    public function save()
    {
        $settingsManager = Yii::$app->settings;
        $settingsManager->set('redcap.apiurl', $this->api_url);
        $settingsManager->set('redcap.apitoken', $this->api_token);
        $settingsManager->set('redcap.surveyid', $this->survey_id);

        return true;
    }
}
