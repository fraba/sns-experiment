<?php

namespace humhub\modules\user;

use humhub\modules\content\models\ContentContainer;
use humhub\modules\user\models\User;
use humhub\modules\user\models\Password;
use humhub\modules\user\models\Profile;
use humhub\modules\user\models\GroupUser;
use humhub\modules\user\models\Mentioning;
use humhub\modules\user\models\Follow;
use humhub\modules\user\models\Surveys;
use Yii;
use yii\base\BaseObject;
use IU\PHPCap\RedCapProject;


/**
 * Events provides callbacks for all defined module events.
 *
 * @author luke
 */
class Events extends BaseObject
{

    /**
     * On rebuild of the search index, rebuild all user records
     *
     * @param \yii\base\Event $event
     */
    public static function onSearchRebuild($event)
    {
        foreach (User::find()->visible()->each() as $user) {
            Yii::$app->search->add($user);
        }
    }

    /**
     * On delete of a Content or ContentAddon
     *
     * @param \yii\base\Event $event
     */
    public static function onContentDelete($event)
    {
        Mentioning::deleteAll(['object_model' => $event->sender->className(), 'object_id' => $event->sender->getPrimaryKey()]);
        Follow::deleteAll(['object_model' => $event->sender->className(), 'object_id' => $event->sender->getPrimaryKey()]);
    }

    /**
     * Callback to validate module database records.
     *
     * @param \yii\base\Event $event
     */
    public static function onIntegrityCheck($event)
    {
        $integrityController = $event->sender;

        $integrityController->showTestHeadline('User Module - ContentContainer (' . User::find()->count() . ' entries)');
        foreach (User::find()->joinWith(['contentContainerRecord'])->each() as $user) {
            if ($user->contentContainerRecord === null) {
                if ($integrityController->showFix('Deleting user ' . $user->id . ' without content container record!')) {
                    $user->delete();
                }
            }
        }

        $integrityController->showTestHeadline('User Module - Users (' . User::find()->count() . ' entries)');
        foreach (User::find()->joinWith(['profile'])->each() as $user) {
            if ($user->profile->isNewRecord) {
                $integrityController->showWarning('User with id ' . $user->id . ' has no profile record!');
            }
        }

        foreach (GroupUser::find()->joinWith(['user'])->each() as $groupUser) {
            if ($groupUser->user == null) {
                if ($integrityController->showFix('Deleting group admin ' . $groupUser->id . ' without existing user!')) {
                    $groupUser->delete();
                }
            }
        }

        $integrityController->showTestHeadline('User Module - Password (' . Password::find()->count() . ' entries)');
        foreach (Password::find()->joinWith(['user'])->each() as $password) {
            if ($password->user == null) {
                if ($integrityController->showFix('Deleting password ' . $password->id . ' without existing user!')) {
                    $password->delete();
                }
            }
        }

        $integrityController->showTestHeadline('User Module - Profile (' . Profile::find()->count() . ' entries)');
        foreach (Profile::find()->joinWith(['user'])->each() as $profile) {
            if ($profile->user == null) {
                if ($integrityController->showFix('Deleting profile ' . $profile->user_id . ' without existing user!')) {
                    $profile->delete();
                }
            }
        }

        $integrityController->showTestHeadline('User Module - Mentioning (' . Mentioning::find()->count() . ' entries)');
        foreach (Mentioning::find()->joinWith(['user'])->each() as $mentioning) {
            if ($mentioning->user == null) {
                if ($integrityController->showFix('Deleting mentioning ' . $mentioning->id . ' of non existing user!')) {
                    $mentioning->delete();
                }
            }
            if ($mentioning->getPolymorphicRelation() == null) {
                if ($integrityController->showFix('Deleting mentioning ' . $mentioning->id . ' of non target!')) {
                    $mentioning->delete();
                }
            }
        }

        $integrityController->showTestHeadline('User Module - Follow (' . Follow::find()->count() . ' entries)');
        foreach (Follow::find()->joinWith(['user'])->each() as $follow) {
            if ($follow->user == null) {
                if ($integrityController->showFix('Deleting follow ' . $follow->id . ' of non existing user!')) {
                    $follow->delete();
                }
            }

            try {
                if ($follow->getTarget() == null) {
                    if ($integrityController->showFix('Deleting follow ' . $follow->id . ' of non target!')) {
                        $follow->delete();
                    }
                }
            } catch (\Exception $e) {
                if ($integrityController->showFix('Deleting follow ' . $follow->id . ' of non target!')) {
                    $follow->delete();
                }
            }
        }

        $userIds = User::find()->select('id')->asArray()->all();
        foreach ($userIds as $key => $id) {
            $userIds[$key] = $id['id'];
        }
        $integrityController->showTestHeadline('User Module - Content container (' . ContentContainer::find()->count() . ' entries)');
        foreach (ContentContainer::find()->where(['NOT IN', 'owner_user_id', $userIds])->each() as $contentContainer) {
            if ($contentContainer['class'] == User::class && $contentContainer['pk'] == $contentContainer['owner_user_id']) {
                if ($integrityController->showFix('Deleting content container ' . $contentContainer->id . ' without existing user!')) {
                    $contentContainer->delete();
                }
            }
        }
    }

    /**
     * Tasks on hourly cron job
     *
     * @param \yii\base\Event $event
     */
    public static function onHourlyCron($event)
    {
        Yii::$app->queue->push(new jobs\SyncUsers());
        Yii::$app->queue->push(new jobs\DeleteExpiredSessions());
    }

    /**
     * Tasks on user registration
     * 
     * @param \yii\base\Event $event
     */
    public static function onUserRegistration($event)
    {
        $user_id = $event->sender->models['User']->id;
        $user_email = $event->sender->models['User']->email;

        $api_url = Yii::$app->settings->get("redcap.apiurl");
        $api_token = Yii::$app->settings->get("redcap.apitoken");
        $survey_id = Yii::$app->settings->get("redcap.surveyid");

        if ($api_url === NULL || $api_token === NULL || $survey_id === NULL) {
            return;
        }

        $project = new RedCapProject($api_url, $api_token);

        $participants = $project->exportSurveyParticipants($survey_id);

        $participant_index = array_search($user_email, array_column($participants, "email"));

        if (!$participant_index) {
            return;
        }

        $survey_records_for_participant = $project->exportRecordsAp([
            "format" => "php",
            "forms" => [$survey_id],
            "recordIds" => [$participants[$participant_index]["record"]]
        ]);

        if (!$survey_records_for_participant || count($survey_records_for_participant) === 0) {
            return;
        }

        $serialized_survey_data = serialize($survey_records_for_participant[0]);

        // Load records into DB
        $survey_records = Surveys::findOne(['user_id' => $user_id]);

        if ($survey_records === NULL) {
            $survey_records = new Surveys;
            $survey_records->isNewRecord = true;
        }

        $survey_records->user_id = $user_id;
        $survey_records->survey_data = $serialized_survey_data;

        if ($survey_records !== NULL) {
            $survey_records->update();
            return;
        }

        $survey_records->save();
    }
}
