<?php

namespace humhub\modules\admin\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use humhub\modules\user\models\Experiment;

/**
 * Description of ExperimentsSearch
 */
class ExperimentsSearch extends Experiment
{

    public function rules()
    {
        return [
            [['id', 'start', 'end'], 'safe'],
        ];
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
        $query = Experiment::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 50],
        ]);

        $dataProvider->setSort([
            'attributes' => [
                'id',
                'start',
                'end'
            ]
        ]);

        $this->load($params);

        $query->andFilterWhere(['=', 'id', $this->id]);

        return $dataProvider;
    }
}
