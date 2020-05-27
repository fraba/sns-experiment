<?php

namespace humhub\modules\admin\controllers;

use humhub\components\Response;
use humhub\modules\admin\components\Controller;
use humhub\modules\admin\models\forms\AddGroupMemberForm;
use humhub\modules\admin\models\SurveysSearch;
use humhub\modules\admin\models\UserSearch;
use humhub\modules\admin\notifications\ExcludeGroupNotification;
use humhub\modules\admin\permissions\ManageGroups;
use humhub\modules\user\models\forms\EditGroupForm;
use humhub\modules\user\models\Surveys;
use humhub\modules\user\models\Group;
use humhub\modules\user\models\GroupUser;
use humhub\modules\user\models\User;
use humhub\modules\user\models\UserPicker;
use humhub\modules\user\models\Profile;
use humhub\modules\admin\models\CsvForm;
use humhub\modules\admin\controllers\RedcapController;
use yii\web\UploadedFile;
use Yii;
use yii\db\Exception;
use yii\db\Query;
use yii\web\HttpException;

/**
 * Group Administration Controller
 *
 * @since 0.5
 */
class SurveysController extends Controller
{

    /**
     * @inheritdoc
     */
    public $adminOnly = false;

    public function init()
    {
        $this->subLayout = '@admin/views/layouts/user';
        // $this->appendPageTitle(Yii::t('AdminModule.base', 'Groups'));

        return parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getAccessRules()
    {
        return [
            ['permissions' => ManageGroups::class],
        ];
    }

    /**
     * List all available user groups
     */
    public function actionIndex()
    {
        $searchModel = new SurveysSearch();
        $dataProvider = $searchModel->searchAll(Yii::$app->request->queryParams);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * Displays a single Survey model.
     * @param integer $user_id
     * @return mixed
     */
    public function actionViewall($user_id)
    {
        $searchModel = new SurveysSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('viewall', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * Displays a single Survey model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'survey' => $model,
        ]);
    }

    /**
     * Automatically ingest surveys responses for one user
     * @param integer $user_id
     * @return mixed
     */
    public function actionIngest($user_id)
    {
        $post = Yii::$app->request->post();
        $model = $post["DynamicModel"];

        $redcap = new RedcapController();
        $users = (int) $user_id === -1 ? User::find()->all() : [User::findOne($user_id)];

        if ($users === NULL) {
            throw "Could not find user(s)";
        }

        $surveys_to_ingest = array_keys(array_filter($model, function ($v) {
            return (int) $v === 1;
        }));

        if (count($surveys_to_ingest) === 0) {
            throw new Exception("No surveys selected");
        }

        $ingested = [];
        $not_ingested = [];

        foreach ($users as $user) {
            $surveys = $redcap->getSurveysResponsesForUser($surveys_to_ingest, $user);
            $saved_or_updated = false;

            foreach ($surveys['ingested'] as $survey_id => $survey_data) {
                // If data for a certain survey exists, update it 
                $survey_in_db = Surveys::findOne(['user_id' => $user->id, 'survey_id' => $survey_id]);

                $survey = $survey_in_db === NULL ? new Surveys : $survey_in_db;
                $survey->isNewRecord = $survey_in_db === NULL;
                $survey->user_id = $user->id;
                $survey->survey_data = $survey_data;
                $survey->survey_id = $survey_id;

                if ($survey_in_db === NULL) {
                    $saved_or_updated = true;
                    $survey->save();
                    continue;
                }

                $saved_or_updated = true;
                $survey->update();
            }

            if ($saved_or_updated) {
                array_push($ingested, $user->email);
            } else {
                array_push($not_ingested, $user->email);
            }
        }

        $ingested = implode(", ", $ingested);
        $not_ingested = implode(", ", $not_ingested);

        $message = <<<EOT
            Survey(s) ingested for the following users:
            <br/>
                $ingested
            <br/>
            <br/>
            Survey(s) NOT ingested for the following users:
            <br />
            <p>
                <b><i>Selected survey(s) might not be enabled as a survey or the user did not complete the survey</b></i>
            </p>
            <br/>
                $not_ingested
        EOT;

        \Yii::$app->session->setFlash('survey_ingest_info', $message);

        return $this->redirect(["/admin/surveys"]);
    }

    public function actionSelectsurveys($user_id)
    {
        $redcap = new RedcapController();
        $surveys = $redcap->getSurveys();
        $surveys_model = new \yii\base\DynamicModel(array_keys($surveys));
        $user = User::findOne(['id' => $user_id]);
        $user_profile = Profile::findOne(['user_id' => $user_id]);

        return $this->renderAjax("surveys_modal", [
            'user_id' => $user_id,
            'user' => $user,
            'user_profile' => $user_profile,
            'surveys' => $surveys,
            'model' => $surveys_model
        ]);
    }

    public function actionCsvimport()
    {
        $model = new CsvForm();
        if ($model->load(Yii::$app->request->post())) {
            $model->csv_file = UploadedFile::getInstance($model, 'csv_file');
            if ($model->csv_file /*&& $model->validate()*/) {
                $time = time();
                $model->csv_file->saveAs('uploads/csv/' . $time . '.' . $model->csv_file->extension);
                $model->csv_file = 'uploads/csv/' . $time . '.' . $model->csv_file->extension;

                $handle = fopen($model->csv_file, "r");
                while (($fileop = fgetcsv($handle, 1000, ",")) !== false) {

                    $SurveyModel =  Surveys::find()->where(['user_email' => $fileop[0]])->one();

                    if ($SurveyModel === null) {
                        $NewSurveyModel = new Surveys();
                        $NewSurveyModel->user_email = $fileop[0];
                        $NewSurveyModel->pol_op = $fileop[1];
                        $NewSurveyModel->pol_op_abo = $fileop[2];
                        $NewSurveyModel->pol_op_imm = $fileop[3];
                        $NewSurveyModel->pol_op_gay = $fileop[4];
                        $NewSurveyModel->pol_op_eco = $fileop[5];
                        $NewSurveyModel->int_abo_sur = $fileop[6];
                        $NewSurveyModel->int_gay_sur = $fileop[7];
                        $NewSurveyModel->int_eco_sur = $fileop[8];
                        $NewSurveyModel->int_imm_sur = $fileop[9];
                        $NewSurveyModel->int_abo_obs = $fileop[10];
                        $NewSurveyModel->int_gay_obs = $fileop[11];
                        $NewSurveyModel->int_eco_obs = $fileop[12];
                        $NewSurveyModel->int_imm_obs = $fileop[13];
                        $NewSurveyModel->save();
                    } else {
                        //$SurveyModel->user_email = $fileop[0];
                        $SurveyModel->pol_op = $fileop[1];
                        $SurveyModel->pol_op_abo = $fileop[2];
                        $SurveyModel->pol_op_imm = $fileop[3];
                        $SurveyModel->pol_op_gay = $fileop[4];
                        $SurveyModel->pol_op_eco = $fileop[5];
                        $SurveyModel->int_abo_sur = $fileop[6];
                        $SurveyModel->int_gay_sur = $fileop[7];
                        $SurveyModel->int_eco_sur = $fileop[8];
                        $SurveyModel->int_imm_sur = $fileop[9];
                        $SurveyModel->int_abo_obs = $fileop[10];
                        $SurveyModel->int_gay_obs = $fileop[11];
                        $SurveyModel->int_eco_obs = $fileop[12];
                        $SurveyModel->int_imm_obs = $fileop[13];
                        $SurveyModel->save();
                    }
                }

                fclose($handle);
                @unlink(Yii::getAlias('@webroot') . "/" . $model->csv_file);

                echo "<div class='alert alert-success'>Data uploaded successfully.</div>";
            }
        }
    }


    /**
     * Edits or Creates a user group
     */
    public function actionEdit()
    {

        // Create Group Edit Form
        $group = EditGroupForm::findOne(['id' => Yii::$app->request->get('id')]);

        if (!$group) {
            $group = new EditGroupForm();
        }

        $this->checkGroupAccess($group);

        $wasNew = $group->isNewRecord;

        if ($group->load(Yii::$app->request->post()) && $group->validate() && $group->save()) {
            $this->view->saved();
            if ($wasNew) {
                return $this->redirect([
                    '/admin/group/manage-group-users',
                    'id' => $group->id,
                ]);
            }
        }

        return $this->render('edit', [
            'group' => $group,
            'showDeleteButton' => (!$group->isNewRecord && !$group->is_admin_group),
            'isCreateForm' => $group->isNewRecord,
            'isManagerApprovalSetting' => Yii::$app->getModule('user')->settings->get('auth.needApproval'),
        ]);
    }

    public function actionManagePermissions()
    {
        $group = Group::findOne(['id' => Yii::$app->request->get('id')]);

        $this->checkGroupAccess($group);

        // Save changed permission states
        if (!$group->isNewRecord && Yii::$app->request->post('dropDownColumnSubmit')) {
            $permission = Yii::$app->user->permissionManager->getById(Yii::$app->request->post('permissionId'), Yii::$app->request->post('moduleId'));
            if ($permission === null) {
                throw new HttpException(500, 'Could not find permission!');
            }
            Yii::$app->user->permissionManager->setGroupState($group->id, $permission, Yii::$app->request->post('state'));

            return $this->asJson([]);
        }

        return $this->render('permissions', ['group' => $group]);
    }

    public function actionManageGroupUsers()
    {
        $group = Group::findOne(['id' => Yii::$app->request->get('id')]);
        $this->checkGroupAccess($group);

        $searchModel = new UserSearch();
        $searchModel->query = $group->getUsers();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('members', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'group' => $group,
            'addGroupMemberForm' => new AddGroupMemberForm(),
            'isManagerApprovalSetting' => Yii::$app->getModule('user')->settings->get('auth.needApproval'),
        ]);
    }

    public function actionRemoveGroupUser()
    {
        $this->forcePostRequest();
        $request = Yii::$app->request;
        $group = Group::findOne(['id' => $request->get('id')]);
        $this->checkGroupAccess($group);

        if ($group->removeUser($request->get('userId'))) {
            ExcludeGroupNotification::instance()
                ->about($group)
                ->from(Yii::$app->user->identity)
                ->send(User::findOne(['id' => $request->get('userId')]));
        }

        if ($request->isAjax) {
            Yii::$app->response->format = 'json';
            return ['success' => true];
        }

        return $this->redirect([
            '/admin/group/manage-group-users',
            'id' => $group->id,
        ]);
    }

    /**
     * Deletes a group
     *
     * On deletion all group members will be moved to another group.
     */
    public function actionDelete()
    {
        $this->forcePostRequest();
        $group = Group::findOne(['id' => Yii::$app->request->get('id')]);

        $this->checkGroupAccess($group);

        //Double check to get sure we don't remove the admin group
        if (!$group->is_admin_group) {
            $group->delete();
        }

        return $this->redirect(['/admin/group']);
    }

    public function actionEditManagerRole()
    {
        $this->forcePostRequest();

        $group = Group::findOne(Yii::$app->request->post('id'));
        $this->checkGroupAccess($group);

        $value = Yii::$app->request->post('value');

        if ($value === null) {
            throw new HttpException(
                400,
                Yii::t('AdminModule.controllers_GroupController', 'No value found!')
            );
        }

        $groupUser = $group->getGroupUser(User::findOne(Yii::$app->request->post('userId')));
        if ($groupUser === null) {
            throw new HttpException(
                404,
                Yii::t('AdminModule.controllers_GroupController', 'Group user not found!')
            );
        }

        $groupUser->is_group_manager = (bool) $value;

        return $this->asJson(['success' => $groupUser->save()]);
    }

    public function actionAddMembers()
    {
        $this->forcePostRequest();
        $form = new AddGroupMemberForm();

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            $form->save();
        }

        return $this->redirect([
            '/admin/group/manage-group-users',
            'id' => $form->groupId,
        ]);
    }

    public function actionNewMemberSearch()
    {
        Yii::$app->response->format = 'json';

        $keyword = Yii::$app->request->get('keyword');
        $group = Group::findOne(Yii::$app->request->get('id'));



        $subQuery = (new Query())->select('*')->from(GroupUser::tableName() . ' g')->where([
            'and',
            'g.user_id=user.id',
            ['g.group_id' => $group->id],
        ]);

        $query = User::find()->where(['not exists', $subQuery]);

        $result = UserPicker::filter([
            'keyword' => $keyword,
            'query' => $query,
            'fillUser' => true,
            'fillUserQuery' => $group->getUsers(),
            'disabledText' => Yii::t(
                'AdminModule.controllers_GroupController',
                'User is already a member of this group.'
            ),
        ]);

        return $result;
    }

    public function actionAdminUserSearch($keyword, $id)
    {
        $group = Group::findOne($id);

        if (!$group) {
            throw new HttpException(404, Yii::t('AdminModule.controllers_GroupController', 'Group not found!'));
        }

        return $this->asJson(UserPicker::filter([
            'query' => $group->getUsers(),
            'keyword' => $keyword,
            'fillQuery' => User::find(),
            'disableFillUser' => false,
        ]));
    }

    public function checkGroupAccess($group)
    {
        if (!$group) {
            throw new HttpException(404, Yii::t('AdminModule.controllers_GroupController', 'Group not found!'));
        }

        if ($group->is_admin_group && !Yii::$app->user->isAdmin()) {
            throw new HttpException(403);
        }
    }

    public function findModel($id)
    {
        $survey = Surveys::findOne($id);

        if (!$survey) {
            throw new HttpException(404, "Survey data not found");
        }

        $survey->user = $survey->getUser();

        return $survey;
    }
}
