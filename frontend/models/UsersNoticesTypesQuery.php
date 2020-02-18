<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[UsersNoticesTypes]].
 *
 * @see UsersNoticesTypes
 */
class UsersNoticesTypesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UsersNoticesTypes[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UsersNoticesTypes|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
