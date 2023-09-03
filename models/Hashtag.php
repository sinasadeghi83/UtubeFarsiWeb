<?php

namespace app\models;

/**
 * This is the model class for table "hashtag".
 *
 * @property int            $id
 * @property string         $name
 * @property string         $created_at
 * @property HashtagVideo[] $hashtagVideos
 * @property Video[]        $videos
 */
class Hashtag extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'hashtag';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'name' => \Yii::t('app', 'Name'),
            'created_at' => \Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[HashtagVideos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHashtagVideos()
    {
        return $this->hasMany(HashtagVideo::class, ['hashtag_id' => 'id']);
    }

    /**
     * Gets query for [[Videos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVideos()
    {
        return $this->hasMany(Video::class, ['id' => 'video_id'])->viaTable('hashtag_video', ['hashtag_id' => 'id']);
    }
}
