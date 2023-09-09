<?php

namespace app\controllers;

use app\models\Video;
use sizeg\jwt\JwtHttpBearerAuth;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class StreamController extends Controller
{
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

    public function actionIndex($fileName = null)
    {
        $user = \Yii::$app->user->identity;
        if (!$user->activeLicense()) {
            throw new ForbiddenHttpException('You must buy a license first!');
        }

        $video_id = \Yii::$app->request->headers->get('videoid');
        // var_dump(\Yii::$app->request->headers);
        $video = Video::findOne(['id' => $video_id]);
        if (!$video) {
            throw new NotFoundHttpException("Couldn't find requested video!");
        }

        $resultPath = $video->video_path;
        if (!empty($fileName)) {
            $resultPath = dirname($resultPath)."/{$fileName}";
            if (!file_exists($resultPath)) {
                throw new NotFoundHttpException("Couldn't find requested file!");
            }
        }
        header('Content-Type: application/x-mpegURL');
        \Yii::$app->response->sendFile($resultPath, null, ['inline' => true, 'mimeType' => 'application/x-mpegURL']);
    }

    public function actionKey($video_id)
    {
        $user = \Yii::$app->user->identity;
        if (!$user->activeLicense()) {
            throw new ForbiddenHttpException('You must buy a license first!');
        }
        $video = Video::findOne(['id' => $video_id]);
        if (!$video) {
            throw new NotFoundHttpException("Couldn't find requested video!");
        }
        $keyPath = '/var/www/html/public/media/video/key';
        \Yii::$app->response->sendFile($keyPath.'/'.$video->id.'.pem', null, ['inline' => true]);
    }
}
