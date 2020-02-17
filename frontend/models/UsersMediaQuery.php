<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[UsersMedia]].
 *
 * @see UsersMedia
 */
class UsersMediaQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UsersMedia[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UsersMedia|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
