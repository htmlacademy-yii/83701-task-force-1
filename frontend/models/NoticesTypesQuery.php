<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[NoticesTypes]].
 *
 * @see NoticesTypes
 */
class NoticesTypesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return NoticesTypes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return NoticesTypes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
