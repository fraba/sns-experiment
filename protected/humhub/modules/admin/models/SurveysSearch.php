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
            [['user_id', 'user_email'], 'safe'],
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
    public function search($params)
    {
        $query = Surveys::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                //'user_id',
                'user_email',
            ]
        ]);

        $this->load($params);

       // if (!$this->validate()) {
       //     $query->where('0=1');
        //    return $dataProvider;
       // }

        //$query->andFilterWhere(['like', 'user_id', $this->user_id]);
        $query->andFilterWhere(['like', 'user_email', $this->user_email]);

        return $dataProvider;
    }

}
