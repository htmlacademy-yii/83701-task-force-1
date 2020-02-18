<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "notices".
 *
 * @property int $id
 * @property int $user_id
 * @property int $notice_type_id
 * @property string|null $details
 * @property int $is_new
 *
 * @property NoticesTypes $noticeType
 * @property Users $user
 */
class Notices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'notices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'notice_type_id'], 'required'],
            [['user_id', 'notice_type_id', 'is_new'], 'integer'],
            [['details'], 'string'],
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
            'details' => 'Details',
            'is_new' => 'Is New',
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
     * @return NoticesQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new NoticesQuery(get_called_class());
    }
}
