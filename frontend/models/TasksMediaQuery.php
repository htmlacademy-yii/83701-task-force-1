<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[TasksMedia]].
 *
 * @see TasksMedia
 */
class TasksMediaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return TasksMedia[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return TasksMedia|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
