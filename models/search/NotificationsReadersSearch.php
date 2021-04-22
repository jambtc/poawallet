<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\NotificationsReaders;
use Yii;

/**
 * NotificationsReadersSearch represents the model behind the search form of `app\models\NotificationsReaders`.
 */
class NotificationsReadersSearch extends NotificationsReaders
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'id_notification', 'id_user', 'alreadyread'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
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
        $query = NotificationsReaders::find()
            ->joinWith(['notification'])
        ;

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_notification' => $this->id_notification,
            'id_user' => (Yii::$app->user->id == 1) ? null : Yii::$app->user->id, //$this->id_user,
            'alreadyread' => $this->alreadyread,
        ]);

        // echo '<pre>'.print_r($dataProvider->getmodels(),true);exit;


        return $dataProvider;
    }
}
