<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[UsersCategories]].
 *
 * @see UsersCategories
 */
class UsersCategoriesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UsersCategories[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UsersCategories|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
