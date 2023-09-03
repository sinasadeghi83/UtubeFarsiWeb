<?php

namespace app\models;

/**
 * This is the model class for table "license".
 *
 * @property int           $id
 * @property string        $title
 * @property int           $length
 * @property int           $price
 * @property null|int      $status
 * @property UserLicense[] $userLicenses
 * @property User[]        $users
 */
class License extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'license';
    }

    public function rules()
    {
        return [
            [['title', 'length', 'price'], 'required'],
            [['length', 'price', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'title' => \Yii::t('app', 'Title'),
            'length' => \Yii::t('app', 'Length'),
            'price' => \Yii::t('app', 'Price'),
            'status' => \Yii::t('app', 'Status'),
        ];
    }

    /**
     * Gets query for [[UserLicenses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserLicenses()
    {
        return $this->hasMany(UserLicense::class, ['license_id' => 'id']);
    }

    /**
     * Gets query for [[Users]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::class, ['id' => 'user_id'])->viaTable('user_license', ['license_id' => 'id']);
    }
}
