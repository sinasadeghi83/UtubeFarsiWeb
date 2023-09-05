<?php

namespace app\modules\admin\models;

use yii\base\Model;

class VideoForm extends Model
{
    public $videoFile;

    public function rules()
    {
        return [
            [['videoFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'mp4, webm'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $path = \Yii::getAlias('@webroot').'/uploads/'.$this->videoFile->baseName.strval(time()).'.'.$this->videoFile->extension;
            $this->videoFile->saveAs($path);

            return $path;
        }

        return null;
    }
}
