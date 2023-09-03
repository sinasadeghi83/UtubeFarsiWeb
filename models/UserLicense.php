<?php

namespace app\models;

/**
 * This is the model class for table "user_license".
 *
 * @property int      $id
 * @property null|int $user_id
 * @property null|int $license_id
 * @property string   $payment_id
 * @property string   $created_at
 * @property License  $license
 * @property User     $user
 */
class UserLicense extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'user_license';
    }

    public function rules()
    {
        return [
            [['user_id', 'license_id'], 'integer'],
            [['payment_id'], 'required'],
            [['created_at'], 'safe'],
            [['payment_id'], 'string', 'max' => 255],
            [['payment_id'], 'unique'],
            [['license_id'], 'exist', 'skipOnError' => true, 'targetClass' => License::class, 'targetAttribute' => ['license_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'user_id' => \Yii::t('app', 'User ID'),
            'license_id' => \Yii::t('app', 'License ID'),
            'payment_id' => \Yii::t('app', 'Payment ID'),
            'created_at' => \Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[License]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLicense()
    {
        return $this->hasOne(License::class, ['id' => 'license_id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}
