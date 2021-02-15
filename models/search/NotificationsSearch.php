<?php

namespace app\models\search;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Notifications;

/**
 * NotificationsSearch represents the model behind the search form of `app\models\Notifications`.
 */
class NotificationsSearch extends Notifications
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_notification', 'id_user', 'id_tocheck', 'status', 'description', 'url', 'timestamp'], 'required'],
            [['id_user', 'id_tocheck', 'timestamp', 'deleted'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['type_notification', 'status', 'url'], 'string', 'max' => 250],
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
        $query = Notifications::find();

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
            'id_notifications' => $this->id_notifications,
            'type_notification' => $this->type_notification,
            'id_user' => $this->id_user,
            'id_tocheck' => $this->id_tocheck,
            'status' => $this->status,
            'description' => $this->description,
            'url' => $this->url,
            'timestamp' => $this->timestamp,
            'price' => $this->price,
            'deleted' => $this->deleted,
        ]);

        $query->andFilterWhere(['like', 'id_notifications', $this->id_notifications])
            ->andFilterWhere(['like', 'type_notification', $this->type_notification])
            ->andFilterWhere(['like', 'id_user', $this->id_user])
            ->andFilterWhere(['like', 'id_tocheck', $this->id_tocheck])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'timestamp', $this->timestamp])
            ->andFilterWhere(['like', 'price', $this->price])
            ->andFilterWhere(['like', 'deleted', $this->deleted]);

        return $dataProvider;
    }
}
