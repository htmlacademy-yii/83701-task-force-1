<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "users_notices_types".
 *
 * @property int $id
 * @property int $user_id
 * @property int $notice_type_id
 *
 * @property NoticesTypes $noticeType
 * @property Users $user
 */
class UsersNoticesTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users_notices_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'notice_type_id'], 'required'],
            [['user_id', 'notice_type_id'], 'integer'],
            [['user_id', 'notice_type_id'], 'unique', 'targetAttribute' => ['user_id', 'notice_type_id']],
            [['notice_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => NoticesTypes::className(), 'targetAttribute' => ['notice_type_id' => 'id']],
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
            'notice_type_id' => 'Notice Type ID',
        ];
    }

    /**
     * Gets query for [[NoticeType]].
     *
     * @return \yii\db\ActiveQuery|NoticesTypesQuery
     */
    public function getNoticeType()
    {
        return $this->hasOne(NoticesTypes::className(), ['id' => 'notice_type_id']);
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
     * @return UsersNoticesTypesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersNoticesTypesQuery(get_called_class());
    }
}
