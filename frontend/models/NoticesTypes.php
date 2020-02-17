<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "notices_types".
 *
 * @property int $id
 * @property string $type
 *
 * @property Notices[] $notices
 * @property UsersNoticesTypes[] $usersNoticesTypes
 * @property Users[] $users
 */
class NoticesTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notices_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'string', 'max' => 256],
            [['type'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[Notices]].
     *
     * @return \yii\db\ActiveQuery|NoticesQuery
     */
    public function getNotices()
    {
        return $this->hasMany(Notices::className(), ['notice_type_id' => 'id']);
    }

    /**
     * Gets query for [[UsersNoticesTypes]].
     *
     * @return \yii\db\ActiveQuery|UsersNoticesTypesQuery
     */
    public function getUsersNoticesTypes()
    {
        return $this->hasMany(UsersNoticesTypes::className(), ['notice_type_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['id' => 'user_id'])->viaTable('users_notices_types', ['notice_type_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return NoticesTypesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoticesTypesQuery(get_called_class());
    }
}
