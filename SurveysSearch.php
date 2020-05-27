<?php

namespace humhub\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use humhub\modules\user\models\Surveys;

/**
 * Description of SurveysSearch
 *
 * @author luke
 */
class SurveysSearch extends Surveys
{

    public function rules()
    {
        return [
            [['user_id', 'survey_data'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        //  return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchAll($params)
    {
        $query = Surveys::find()->select("user_id")->distinct();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'user_id',
            ]
        ]);

        $this->load($params);

        $query->andFilterWhere(['like', 'user_id', $this->user_id]);

        return $dataProvider;
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Surveys::find()->where(['user_id' => $params["user_id"]]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'survey_id',
            ]
        ]);

        $this->load($params);

        return $dataProvider;
    }
}
