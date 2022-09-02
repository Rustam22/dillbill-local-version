<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * UserSearch represents the model behind the search form of `backend\models\User`.
 */
class UserSearch extends User
{
    public $name;
    public $surname;
    public $confirmed;
    public $proficiency;
    public $currentLevel;
    public $currentPacketId;
    public $condition;
    public $availability;
    public $timezone;
    public $currentSchedule;
    public $cp;
    public $cpBalance;
    public $lpd;
    public $googleCalendar;
    public $calendarGmail;

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['id', 'status', 'created_at', 'updated_at'], 'string'],
            [['username', 'name', 'surname', 'email',
                'availability', 'currentSchedule', 'timezone',
                'confirmed', 'currentLevel', 'currentPacketId', 'cp',
                'cpBalance', 'lpd', 'googleCalendar', 'calendarGmail'], 'safe'],
            [['username', 'name', 'surname',
                'email', 'availability', 'currentSchedule',
                'timezone', 'confirmed', 'currentLevel',
                'currentPacketId', 'cp', 'cpBalance', 'lpd', 'created_at',
                'googleCalendar', 'calendarGmail'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios(): array
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
    public function search(array $params): ActiveDataProvider
    {
        $query = User::find();
        $query->joinWith(['userProfile profile']); //set relation alias
        $query->joinWith(['userParameters parameters']); //set relation alias
        //$query->joinWith(['userProfile', 'userParameters']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['confirmed'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['userParameters.name' => SORT_ASC],
            'desc' => ['userParameters.name' => SORT_DESC],
        ];


        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['=', 'userParameters.confirmed', $this->confirmed])
            ->andFilterWhere(['like', 'parameters.proficiency', $this->proficiency])
            ->andFilterWhere(['=', 'parameters.currentLevel', $this->currentLevel])
            ->andFilterWhere(['like', 'parameters.currentPacketId', $this->currentPacketId])
            ->andFilterWhere(['=', 'parameters.currentSchedule', $this->currentSchedule])
            ->andFilterWhere(['=', 'parameters.cp', $this->cp])
            //->andFilterWhere(['like', 'parameters.cpBalance', $this->cpBalance])
            ->andFilterWhere(['>=', '(parameters.cpBalance - DATEDIFF(NOW(), parameters.lpd))', $this->cpBalance])
            ->andFilterWhere(['like', 'parameters.lpd', $this->lpd])
            ->andFilterWhere(['like', 'profile.timezone', $this->timezone])
            ->andFilterWhere(['like', 'parameters.availability', $this->availability])
            ->andFilterWhere(['like', 'DATE_FORMAT(FROM_UNIXTIME(`created_at`), "%d-%m-%Y")', $this->created_at]);

        return $dataProvider;
    }
}
