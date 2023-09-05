<?php

namespace app\modules\admin\controllers;

use app\jobs\HlsJob;
use app\models\Video;
use app\modules\admin\models\VideoForm;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Video controller for the `admin` module.
 */
class VideoController extends ActiveController
{
    public $modelClass = 'app\models\Video';

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

    public function actionUpload($id)
    {
        $video = Video::findOne(['id' => $id]);
        if (empty($video)) {
            throw new NotFoundHttpException("There's no such video with this id!");
        }
        $model = new VideoForm();

        $model->videoFile = UploadedFile::getInstanceByName('videoFile');
        if ($path = $model->upload()) {
            // file is uploaded successfully
            // TODO Add queue to transcode to hls
            $video->setVideoByForm($model);
            $video->save();
            var_dump(\Yii::$app->queue->push(new HlsJob([
                'filePath' => $path,
                'video' => $video,
            ])));

            return $video;
        }

        \Yii::$app->response->statusCode = 400;

        return [
            'errors' => array_merge($model->errors, $video->errors),
        ];
    }
}
