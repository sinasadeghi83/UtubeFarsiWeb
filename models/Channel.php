<?php

namespace app\models;

/**
 * This is the model class for table "channel".
 *
 * @property int         $id
 * @property string      $title
 * @property string      $username
 * @property null|string $description
 * @property null|string $links
 * @property null|string $header_path
 * @property null|string $prof_img_path
 * @property string      $youtube_subscribers
 * @property string      $youtube_views
 * @property string      $joined_at
 * @property string      $updated_at
 * @property Video[]     $videos
 */
class Channel extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'channel';
    }

    public function rules()
    {
        return [
            [['title', 'username', 'youtube_subscribers', 'youtube_views', 'joined_at'], 'required'],
            [['description'], 'string', 'max' => 1000],
            [['joined_at', 'updated_at'], 'safe'],
            [['title', 'username', 'links', 'header_path', 'prof_img_path', 'youtube_subscribers', 'youtube_views'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('app', 'ID'),
            'title' => \Yii::t('app', 'Title'),
            'username' => \Yii::t('app', 'Username'),
            'description' => \Yii::t('app', 'Description'),
            'links' => \Yii::t('app', 'Links'),
            'header_path' => \Yii::t('app', 'Header Path'),
            'prof_img_path' => \Yii::t('app', 'Prof Img Path'),
            'youtube_subscribers' => \Yii::t('app', 'Youtube Subscribers'),
            'youtube_views' => \Yii::t('app', 'Youtube Views'),
            'joined_at' => \Yii::t('app', 'Joined At'),
            'updated_at' => \Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * Gets query for [[Videos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVideos()
    {
        return $this->hasMany(Video::class, ['channel_id' => 'id']);
    }
}
