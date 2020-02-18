<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[Favorites]].
 *
 * @see Favorites
 */
class FavoritesQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return Favorites[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Favorites|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
