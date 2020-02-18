<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "favorites".
 *
 * @property int $id
 * @property int $user_id
 * @property int $selected_user_id
 *
 * @property Users $selectedUser
 * @property Users $user
 */
class Favorites extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'favorites';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'selected_user_id'], 'required'],
            [['user_id', 'selected_user_id'], 'integer'],
            [['user_id', 'selected_user_id'], 'unique', 'targetAttribute' => ['user_id', 'selected_user_id']],
            [['selected_user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['selected_user_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'selected_user_id' => 'Selected User ID',
        ];
    }

    /**
     * Gets query for [[SelectedUser]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getSelectedUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'selected_user_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id' => 'user_id']);
    }

    /**
     * {@inheritdoc}
     * @return FavoritesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new FavoritesQuery(get_called_class());
    }
}
