<?php

namespace app\models;

/**
 * This is the model class for table "hashtag_video".
 *
 * @property int     $hashtag_id
 * @property int     $video_id
 * @property Hashtag $hashtag
 * @property Video   $video
 */
class HashtagVideo extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'hashtag_video';
    }

    public function rules()
    {
        return [
            [['hashtag_id', 'video_id'], 'required'],
            [['hashtag_id', 'video_id'], 'integer'],
            [['hashtag_id', 'video_id'], 'unique', 'targetAttribute' => ['hashtag_id', 'video_id']],
            [['hashtag_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hashtag::class, 'targetAttribute' => ['hashtag_id' => 'id']],
            [['video_id'], 'exist', 'skipOnError' => true, 'targetClass' => Video::class, 'targetAttribute' => ['video_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'hashtag_id' => \Yii::t('app', 'Hashtag ID'),
            'video_id' => \Yii::t('app', 'Video ID'),
        ];
    }

    /**
     * Gets query for [[Hashtag]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHashtag()
    {
        return $this->hasOne(Hashtag::class, ['id' => 'hashtag_id']);
    }

    /**
     * Gets query for [[Video]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVideo()
    {
        return $this->hasOne(Video::class, ['id' => 'video_id']);
    }
}
