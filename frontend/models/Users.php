<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $email
 * @property string $password_hash
 * @property string $created_at
 * @property string $last_activity
 * @property string $phio
 * @property int $city_id
 * @property int $open_profile
 * @property int $open_contacts
 * @property string|null $avatar_link
 * @property string $birthday
 * @property string|null $biography
 * @property string|null $phone
 * @property string|null $skype
 * @property string|null $telegram
 * @property int $views_number
 * @property float|null $rate
 * @property int $fail_count
 *
 * @property ChatMsgs[] $chatMsgs
 * @property Favorites[] $favorites
 * @property Favorites[] $favorites0
 * @property Users[] $users
 * @property Users[] $selectedUsers
 * @property Notices[] $notices
 * @property Responses[] $responses
 * @property Reviews[] $reviews
 * @property Tasks[] $tasks
 * @property Tasks[] $tasks0
 * @property Cities $city
 * @property UsersCategories[] $usersCategories
 * @property Categories[] $categories
 * @property UsersMedia[] $usersMedia
 * @property UsersNoticesTypes[] $usersNoticesTypes
 * @property NoticesTypes[] $noticeTypes
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password_hash', 'phio', 'city_id', 'birthday'], 'required'],
            [['created_at', 'last_activity', 'birthday'], 'safe'],
            [['city_id', 'open_profile', 'open_contacts', 'views_number', 'fail_count'], 'integer'],
            [['biography'], 'string'],
            [['rate'], 'number'],
            [['email', 'phio', 'phone', 'skype', 'telegram'], 'string', 'max' => 256],
            [['password_hash', 'avatar_link'], 'string', 'max' => 512],
            [['email'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => Cities::className(), 'targetAttribute' => ['city_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'created_at' => 'Created At',
            'last_activity' => 'Last Activity',
            'phio' => 'Phio',
            'city_id' => 'City ID',
            'open_profile' => 'Open Profile',
            'open_contacts' => 'Open Contacts',
            'avatar_link' => 'Avatar Link',
            'birthday' => 'Birthday',
            'biography' => 'Biography',
            'phone' => 'Phone',
            'skype' => 'Skype',
            'telegram' => 'Telegram',
            'views_number' => 'Views Number',
            'rate' => 'Rate',
            'fail_count' => 'Fail Count',
        ];
    }

    /**
     * Gets query for [[ChatMsgs]].
     *
     * @return \yii\db\ActiveQuery|ChatMsgsQuery
     */
    public function getChatMsgs()
    {
        return $this->hasMany(ChatMsgs::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Favorites]].
     *
     * @return \yii\db\ActiveQuery|FavoritesQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorites::className(), ['selected_user_id' => 'id']);
    }

    /**
     * Gets query for [[Favorites0]].
     *
     * @return \yii\db\ActiveQuery|FavoritesQuery
     */
    public function getFavorites0()
    {
        return $this->hasMany(Favorites::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getUsers()
    {
        return $this->hasMany(Users::className(), ['id' => 'user_id'])->viaTable('favorites', ['selected_user_id' => 'id']);
    }

    /**
     * Gets query for [[SelectedUsers]].
     *
     * @return \yii\db\ActiveQuery|UsersQuery
     */
    public function getSelectedUsers()
    {
        return $this->hasMany(Users::className(), ['id' => 'selected_user_id'])->viaTable('favorites', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Notices]].
     *
     * @return \yii\db\ActiveQuery|NoticesQuery
     */
    public function getNotices()
    {
        return $this->hasMany(Notices::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Responses]].
     *
     * @return \yii\db\ActiveQuery|ResponsesQuery
     */
    public function getResponses()
    {
        return $this->hasMany(Responses::className(), ['whose_user_id' => 'id']);
    }

    /**
     * Gets query for [[Reviews]].
     *
     * @return \yii\db\ActiveQuery|ReviewsQuery
     */
    public function getReviews()
    {
        return $this->hasMany(Reviews::className(), ['whom_user_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery|TasksQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::className(), ['customer_id' => 'id']);
    }

    /**
     * Gets query for [[Tasks0]].
     *
     * @return \yii\db\ActiveQuery|TasksQuery
     */
    public function getTasks0()
    {
        return $this->hasMany(Tasks::className(), ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[City]].
     *
     * @return \yii\db\ActiveQuery|CitiesQuery
     */
    public function getCity()
    {
        return $this->hasOne(Cities::className(), ['id' => 'city_id']);
    }

    /**
     * Gets query for [[UsersCategories]].
     *
     * @return \yii\db\ActiveQuery|UsersCategoriesQuery
     */
    public function getUsersCategories()
    {
        return $this->hasMany(UsersCategories::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[Categories]].
     *
     * @return \yii\db\ActiveQuery|CategoriesQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Categories::className(), ['id' => 'category_id'])->viaTable('users_categories', ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UsersMedia]].
     *
     * @return \yii\db\ActiveQuery|UsersMediaQuery
     */
    public function getUsersMedia()
    {
        return $this->hasMany(UsersMedia::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[UsersNoticesTypes]].
     *
     * @return \yii\db\ActiveQuery|UsersNoticesTypesQuery
     */
    public function getUsersNoticesTypes()
    {
        return $this->hasMany(UsersNoticesTypes::className(), ['user_id' => 'id']);
    }

    /**
     * Gets query for [[NoticeTypes]].
     *
     * @return \yii\db\ActiveQuery|NoticesTypesQuery
     */
    public function getNoticeTypes()
    {
        return $this->hasMany(NoticesTypes::className(), ['id' => 'notice_type_id'])->viaTable('users_notices_types', ['user_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return UsersQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UsersQuery(get_called_class());
    }
}
