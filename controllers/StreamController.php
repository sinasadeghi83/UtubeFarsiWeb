<?php

namespace app\controllers;

use yii\web\Controller;

class StreamController extends Controller
{
    public function actionIndex($fileName)
    {
        $videoPath = '/var/www/html/public/media/video/';
        header('Content-Type: application/x-mpegURL');
        \Yii::$app->response->sendFile($videoPath.$fileName, null, ['inline' => true, 'mimeType' => 'application/x-mpegURL']);
    }

    public function actionKey($videoName)
    {
        $keyPath = '/var/www/html/public/media/video/key/';
        \Yii::$app->response->sendFile($keyPath.$videoName.VideoController::keyExtension, null, ['inline' => true]);
    }
}
