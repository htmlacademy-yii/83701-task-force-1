<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property int $customer_id
 * @property int|null $executor_id
 * @property int|null $city_id
 * @property int $category_id
 * @property string $title
 * @property string $text
 * @property string $time_start
 * @property string $time_end
 * @property int|null $budget
 * @property string $status
 * @property float|null $latitude
 * @property float|null $longitude
 *
 * @property ChatMsgs[] $chatMsgs
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property Categories $category
 * @property Cities $city
 * @property Users $customer
 * @property Users $executor
 * @property TasksMedia[] $tasksMedia
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['customer_id', 'category_id', 'title', 'text', 'time_end'], 'required'],
            [['customer_id', 'executor_id', 'city_id', 'category_id', 'budget'], 'integer'],
            [['title', 'text', 'status'], 'string'],
            [['time_start', 'time_end'], 'safe'],
            [['latitude', 'longitude'], 'number'],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Categories::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['customer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['customer_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['executor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer_id' => 'Customer ID',
            'executor_id' => 'Executor ID',
            'city_id' => 'City ID',
            'category_id' => 'Category ID',
            'title' => 'Title',
            'text' => 'Text',
            'time_start' => 'Time Start',
            'time_end' => 'Time End',
            'budget' => 'Budget',
            'status' => 'Status',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
        ];
    }

    /**
     * Gets query for [[ChatMsgs]].
     *
     * @return \yii\db\ActiveQuery|ChatMsgsQuery
     */
    public function getChatMsgs()
    {
        return $this->hasMany(ChatMsgs::className(), ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery|ResponsesQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Responses::className(), ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery|ReviewsQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['task_id' => 'id']);
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery|CategoriesQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Categories::className(), ['id' => 'category_id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery|CitiesQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[Customer]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getCustomer()
    {
        return $this->hasOne(Users::className(), ['id' => 'customer_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(Users::className(), ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[TasksMedia]].
     *
     * @return \yii\db\ActiveQuery|TasksMediaQuery
     */
    public function getTasksMedia()
    {
        return $this->hasMany(TasksMedia::className(), ['task_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return TasksQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TasksQuery(get_called_class());
    }
}
