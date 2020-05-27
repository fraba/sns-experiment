<?php

namespace humhub\modules\admin\controllers;

use humhub\modules\user\models\Surveys;
use IU\PHPCap\RedCapProject;
use Yii;

class RedcapController
{
  private $api_url;
  private $api_token;
  private $default_survey_id;

  public $RedcapInstance = null;

  function __construct()
  {
    $this->api_url = Yii::$app->settings->get("redcap.apiurl");
    $this->api_token = Yii::$app->settings->get("redcap.apitoken");
    $this->default_survey_id = Yii::$app->settings->get("redcap.surveyid");

    if ($this->api_url === NULL || $this->api_token === NULL) {
      throw "Cannot initalize Redcap without API access";
    }

    $this->RedcapInstance = new RedCapProject($this->api_url, $this->api_token);
  }

  public function getSurveys()
  {
    return $this->RedcapInstance->exportInstruments();
  }

  private function getSurveyIdForRecordId($record_id, $participants_by_survey)
  {
    foreach ($participants_by_survey as $survey => $participants) {
      foreach ($participants as $participant) {
        if ($participant["record"] === $record_id) {
          return $survey;
        }
      }
    }
  }

  public function getSurveysResponsesForUser($surveys_to_ingest, $user)
  {
    if (!$user) return NULL;

    $participants_by_survey = [];
    $surveys_ingested = [];
    $records_ids = [];
    $surveys_not_ingested = [];

    foreach ($surveys_to_ingest as $survey) {
      try {
        $participants_for_survey = $this->RedcapInstance->exportSurveyParticipants($survey);
        $participants_by_survey[$survey] = $participants_for_survey;

        foreach ($participants_for_survey as $participant) {
          if ($participant["email"] === $user->email && $participant["response_status"] === 2) {
            array_push($surveys_ingested, $survey);
            array_push($records_ids, $participant["record"]);
            break;
          }
        }
      } catch (\IU\PHPCap\PhpCapException $e) {
        array_push($surveys_not_ingested, $survey);
      }
    }

    if (count($surveys_ingested) === 0) {
      return [
        'ingested' => [],
        'not_ingested' => []
      ];
    }

    $records = $this->RedcapInstance->exportRecordsAp([
      "format" => "php",
      "forms" => $surveys_ingested,
      "recordIds" => $records_ids
    ]);

    $return = [];

    foreach ($records as $record) {
      $survey_data = serialize($record);
      $survey_id = $this->getSurveyIdForRecordId($record["id"], $participants_by_survey);

      $return[$survey_id] = $survey_data;
    }

    return [
      'ingested' => $return,
      'not_ingested' => $surveys_not_ingested
    ];
  }
}
