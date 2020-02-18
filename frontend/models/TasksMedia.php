<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "tasks_media".
 *
 * @property int $id
 * @property int $task_id
 * @property string $link
 *
 * @property Tasks $task
 */
class TasksMedia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks_media';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['task_id', 'link'], 'required'],
            [['task_id'], 'integer'],
            [['link'], 'string', 'max' => 512],
            [['task_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tasks::className(), 'targetAttribute' => ['task_id' => 'id']],
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
            'link' => 'Link',
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
     * {@inheritdoc}
     * @return TasksMediaQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TasksMediaQuery(get_called_class());
    }
}
