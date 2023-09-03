<?php

namespace app\modules\admin\models;

use yii\base\Model;

class ImageForm extends Model
{
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $path = 'uploads/'.$this->imageFile->baseName.strval(time()).'.'.$this->imageFile->extension;
            $this->imageFile->saveAs($path);

            return $path;
        }

        return null;
    }
}
