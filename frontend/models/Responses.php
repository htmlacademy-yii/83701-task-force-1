<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "responses".
 *
 * @property int $id
 * @property int $task_id
 * @property int $whose_user_id
 * @property int|null $price
 * @property string|null $text
 * @property string $created_at
 *
 * @property Tasks $task
 * @property Users $whoseUser
 */
class Responses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'responses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'whose_user_id'], 'required'],
            [['task_id', 'whose_user_id', 'price'], 'integer'],
            [['text'], 'string'],
            [['created_at'], 'safe'],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
            [['whose_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['whose_user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'task_id' => 'Task ID',
            'whose_user_id' => 'Whose User ID',
            'price' => 'Price',
            'text' => 'Text',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Task]].
     *
     * @return \yii\db\ActiveQuery|TasksQuery
     */
    public function getTask()
    {
        return $this->hasOne(Tasks::className(), ['id' => 'task_id']);
    }

    /**
     * Gets query for [[WhoseUser]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getWhoseUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'whose_user_id']);
    }

    /**
     * {@inheritdoc}
     * @return ResponsesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ResponsesQuery(get_called_class());
    }
}
