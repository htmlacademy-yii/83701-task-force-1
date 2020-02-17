<?php

namespace frontend\models;

/**
 * This is the ActiveQuery class for [[ChatMsgs]].
 *
 * @see ChatMsgs
 */
class ChatMsgsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ChatMsgs[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ChatMsgs|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
