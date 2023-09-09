<?php

namespace app\models;

/**
 * This is the model class for table "video".
 *
 * @property int            $id
 * @property string         $title
 * @property null|string    $descriptions
 * @property int            $channel_id
 * @property string         $youtube_views
 * @property string         $publish_date
 * @property string         $youtube_link
 * @property string         $video_path
 * @property string         $updated_at
 * @property string         $created_at
 * @property Channel        $channel
 * @property HashtagVideo[] $hashtagVideos
 * @property Hashtag[]      $hashtags
 */
class Video extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'video';
    }

    public function rules()
    {
        return [
            [['title', 'channel_id', 'youtube_views', 'publish_date', 'youtube_link'], 'required'],
            [['descriptions'], 'string', 'max' => 5000],
            [['channel_id'], 'integer'],
            [['publish_date', 'updated_at', 'created_at', 'video_path'], 'safe'],
            [['title', 'youtube_views', 'youtube_link', 'video_path'], 'string', 'max' => 255],
            [['channel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Channel::class, 'targetAttribute' => ['channel_id' => 'id']],
        ];
    }

    public function setVideoByForm($videoForm)
    {
        $videoName = substr(md5($videoForm->videoFile->baseName), 6, 8);
        $path = \Yii::getAlias('@webroot')."/media/video/{$this->id}/{$videoName}.m3u8";
        $this->video_path = $path;

        return $videoName;
    }

    public function fields()
    {
        $fields = parent::fields();

        // Exclude the 'video_path' column from the response
        unset($fields['video_path']);

        return $fields;
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'title' => \Yii::t('app', 'Title'),
            'descriptions' => \Yii::t('app', 'Descriptions'),
            'channel_id' => \Yii::t('app', 'Channel ID'),
            'youtube_views' => \Yii::t('app', 'Youtube Views'),
            'publish_date' => \Yii::t('app', 'Publish Date'),
            'youtube_link' => \Yii::t('app', 'Youtube Link'),
            'video_path' => \Yii::t('app', 'Video Path'),
            'updated_at' => \Yii::t('app', 'Updated At'),
            'created_at' => \Yii::t('app', 'Created At'),
        ];
    }

    /**
     * Gets query for [[Channel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChannel()
    {
        return $this->hasOne(Channel::class, ['id' => 'channel_id']);
    }

    /**
     * Gets query for [[HashtagVideos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHashtagVideos()
    {
        return $this->hasMany(HashtagVideo::class, ['video_id' => 'id']);
    }

    /**
     * Gets query for [[Hashtags]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHashtags()
    {
        return $this->hasMany(Hashtag::class, ['id' => 'hashtag_id'])->viaTable('hashtag_video', ['video_id' => 'id']);
    }
}
