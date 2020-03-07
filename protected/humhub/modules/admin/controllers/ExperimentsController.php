<?php

namespace humhub\modules\admin\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\base\Exception;
use yii\db\Expression;
use humhub\modules\admin\components\Controller;
use humhub\modules\admin\models\ExperimentsSearch;
use humhub\modules\admin\models\DynamicFormModel;
use humhub\modules\user\models\Experiment;
use humhub\modules\user\models\ExperimentGroup;
use humhub\modules\user\models\ExperimentUserGroup;
use humhub\modules\admin\permissions\ManageGroups;
use humhub\modules\user\models\User;
use kartik\mpdf\Pdf;

class ExperimentsController extends Controller
{

    /**
     * @inheritdoc
     */
    public $adminOnly = false;

    public static $TOPICS = [
        'abo' => 'Abortion',
        'imm' => 'Immigration',
        'gay' => 'LGBT Rights',
        'eco' => 'Economic governance',
        'cli' => 'Climate change'
    ];

    public static $POLITICAL_OPINIONS = [
        'left' => 'Left',
        'right' => 'Right'
    ];

    public function init()
    {
        $this->subLayout = '@admin/views/layouts/user';
        $this->appendPageTitle(Yii::t('AdminModule.base', 'Experiments'));

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
     * Lists all Experiment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExperimentsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Experiment model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'experiment' => $model,
            'topics' => self::$TOPICS,
            'political_opinions' => self::$POLITICAL_OPINIONS
        ]);
    }

    /**
     * Creates a new Experiment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $modelExperiment = new Experiment;
        $modelsExperimentGroup = [new ExperimentGroup];

        if ($modelExperiment->load(Yii::$app->request->post())) {
            $modelsExperimentGroup = DynamicFormModel::createMultiple(ExperimentGroup::classname());
            DynamicFormModel::loadMultiple($modelsExperimentGroup, Yii::$app->request->post());

            // validate all models
            $valid = $modelExperiment->validate();
            $valid = DynamicFormModel::validateMultiple($modelsExperimentGroup) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $modelExperiment->save(false)) {
                        foreach ($modelsExperimentGroup as $modelExperimentGroup) {
                            if ($modelExperimentGroup->topic_in === NULL) {
                                $modelExperimentGroup->topic_probability = NULL;
                            }

                            if ($modelExperimentGroup->pol_op_in === NULL) {
                                $modelExperimentGroup->pol_op_probability = NULL;
                            }

                            $modelExperimentGroup->experiment_id = $modelExperiment->id;

                            $modelExperimentGroup->csv_file = UploadedFile::getInstance($modelExperimentGroup, 'csv_file');

                            $users_ids = NULL;
                            if ($modelExperimentGroup->csv_file) {
                                $users_ids = $this->processCSVFile($modelExperimentGroup->csv_file);
                            } else {
                                $users_ids = $this->getRandomUsers($modelExperimentGroup->size);
                            }

                            if (!$users_ids) {
                                $transaction->rollBack();
                                break;
                            }

                            if (!($flag = $modelExperimentGroup->save(false))) {
                                $transaction->rollBack();
                                break;
                            }

                            foreach ($users_ids as $user_id) {
                                $experiment_user = new ExperimentUserGroup;
                                $experiment_user->user_id = $user_id;
                                $experiment_user->group_id = $modelExperimentGroup->id;

                                if (!($flag = $experiment_user->save(false))) {
                                    $transaction->rollBack();
                                    break;
                                }
                            }
                        }
                    }

                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $modelExperiment->id]);
                    }
                } catch (Exception $e) {
                    die($e);
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('create', [
            'topics' => self::$TOPICS,
            'political_opinions' => self::$POLITICAL_OPINIONS,
            'modelExperiment' => $modelExperiment,
            'modelsExperimentGroup' => (empty($modelsExperimentGroup)) ? [new ExperimentGroup] : $modelsExperimentGroup
        ]);
    }

    /**
     * Updates an existing Experiment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $modelExperiment = $this->findModel($id);
        $modelsExperimentGroup = $modelExperiment->addresses;

        if ($modelExperiment->load(Yii::$app->request->post())) {

            $oldIDs = ArrayHelper::map($modelsExperimentGroup, 'id', 'id');
            $modelsExperimentGroup = DynamicFormModel::createMultiple(ExperimentGroup::classname(), $modelsExperimentGroup);
            DynamicFormModel::loadMultiple($modelsExperimentGroup, Yii::$app->request->post());
            $deletedIDs = array_diff($oldIDs, array_filter(ArrayHelper::map($modelsExperimentGroup, 'id', 'id')));

            // validate all models
            $valid = $modelExperiment->validate();
            $valid = DynamicFormModel::validateMultiple($modelsExperimentGroup) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $modelExperiment->save(false)) {
                        if (!empty($deletedIDs)) {
                            ExperimentGroup::deleteAll(['id' => $deletedIDs]);
                        }
                        foreach ($modelsExperimentGroup as $modelExperimentGroup) {
                            $modelExperimentGroup->experiment_id = $modelExperiment->id;
                            if (!($flag = $modelExperimentGroup->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $modelExperiment->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        return $this->render('update', [
            'modelExperiment' => $modelExperiment,
            'modelsExperimentGroup' => (empty($modelsExperimentGroup)) ? [new ExperimentGroup] : $modelsExperimentGroup
        ]);
    }

    /**
     * Deletes an existing Experiment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $name = $model->id;

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Experiment  <strong>#"' . $name . '"</strong> deleted successfully.');
        }

        return $this->redirect(['index']);
    }

    /**
     * Generates a report in PDF format
     * @param integer $id 
     * @return mixed
     */
    public function actionReport($id)
    {
        $model = $this->findModel($id);

        $content = $this->renderPartial("view", ['id' => $id, 'experiment' => $model, 'TOPICS' => self::$TOPICS]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
            'mode' => Pdf::MODE_UTF8,
            // A4 paper format
            'format' => Pdf::FORMAT_A4,
            // portrait orientation
            'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
            'destination' => Pdf::DEST_DOWNLOAD,
            // your html content input
            'content' => $content,
            'filename' => "Experiment $id - Report",
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting 
            // any css to be embedded if required
            'cssInline' => '.kv-heading-1{font-size:18px}',
            // set mPDF properties on the fly
            'options' => ['title' => "Experiment - $id"],
            // call mPDF methods on the fly
            'methods' => [
                'SetHeader' => ["Experiment - $id"],
                'SetFooter' => ['{PAGENO}'],
            ]
        ]);

        // return the pdf output as per the destination setting
        return $pdf->render();

        // Yii::$app->response->sendStreamAsFile($pdfRendered, "Experiment Report - $id", ['mimeType' => 'application/pdf']);
    }

    private function getRandomUsers($size)
    {
        $no_of_users = User::find()->count();

        $users = [];
        if ($no_of_users <= $size) {
            $users = User::findBySql("SELECT id FROM user")->asArray()->all();
        } else {
            $users = User::find()->select("id")->orderBy(new Expression("rand()"))->limit($size)->asArray()->all();
        }

        $random_ids = [];
        foreach ($users as $user) {
            array_push($random_ids, $user["id"]);
        }

        return $random_ids;
    }

    private function processCSVFile($file = NULL)
    {
        if (!$file) {
            return false;
        }

        $user_ids = [];

        $handle = fopen($file, "r");

        while (($row_data = fgetcsv($handle, 1000, ",")) !== false) {
            $no_of_ids = count($row_data);
            for ($i = 0; $i < $no_of_ids; $i++) {
                array_push($user_ids, $row_data[$i]);
            }
        }

        fclose($handle);
        return $user_ids;
    }

    /**
     * Finds the Experiment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Experiment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Experiment::findOne($id)) !== NULL) {
            $model->groups = $model->getExperimentGroup();

            if ($model->groups !== NULL) {
                foreach ($model->groups as $group) {
                    $users_in_group = $group->getUsers();

                    $group->users = [];

                    if ($users_in_group !== NULL) {
                        foreach ($users_in_group as $user_in_group) {
                            $user = $user_in_group->getUser();

                            array_push($group->users, $user);
                        }
                    }
                }
            }

            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
