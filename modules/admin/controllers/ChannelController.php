<?php

namespace app\modules\admin\controllers;

use app\models\Channel;
use app\modules\admin\models\ImageForm;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Channel controller for the `admin` module.
 */
class ChannelController extends ActiveController
{
    public $modelClass = 'app\models\Channel';

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => JwtHttpBearerAuth::class,
            'optional' => [
            ],
        ];

        return $behaviors;
    }

    public function actionProfile($id)
    {
        $channel = Channel::findOne(['id' => $id]);
        if (empty($channel)) {
            throw new NotFoundHttpException("There's no such channel with this id!");
        }
        $model = new ImageForm();

        $model->imageFile = UploadedFile::getInstanceByName('imageFile');
        if ($path = $model->upload()) {
            $oldPath = $channel->prof_img_path;
            // file is uploaded successfully
            $channel->prof_img_path = $path;
            $channel->save();
            $this->removeIfExists($oldPath);

            return $channel;
        }

        \Yii::$app->response->statusCode = 400;

        return $channel->errors;
    }

    public function actionHeader($id)
    {
        $channel = Channel::findOne(['id' => $id]);
        if (empty($channel)) {
            throw new NotFoundHttpException("There's no such channel with this id!");
        }
        $model = new ImageForm();

        $model->imageFile = UploadedFile::getInstanceByName('imageFile');
        if ($path = $model->upload()) {
            $oldPath = $channel->header_path;
            // file is uploaded successfully
            $channel->header_path = $path;
            $channel->save();

            $this->removeIfExists($oldPath);

            return $channel;
        }

        \Yii::$app->response->statusCode = 400;

        return [
            'errors' => $channel->errors,
        ];
    }

    public function removeIfExists($filePath)
    {
        if (file_exists($filePath)) {
            return unlink($filePath);
        }

        return true;
    }
}
