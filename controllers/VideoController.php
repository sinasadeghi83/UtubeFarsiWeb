<?php

namespace app\controllers;

use Streaming\FFMpeg;
use yii\helpers\Url;
use yii\web\Controller;

class VideoController extends Controller
{
    public const keyExtension = '.pem';
    public const videoExtension = '.mp4';

    public function actionHls()
    {
        $videoPath = '/var/www/html/public/media/video/';
        $keyPath = '/var/www/html/public/media/video/key/';
        $keyExtension = '.pem';
        $videoExtension = '.mp4';
        $videoName = 'PC';

        $ffmpeg = $this->setupFFMpeg();
        $video = $ffmpeg->open($videoPath.$videoName.$videoExtension);
        $url = Url::to('/stream/key/'.$videoName);
        $video->hls()
            ->encryption($keyPath.$videoName.$keyExtension, $url)
            ->x264()
            ->autoGenerateRepresentations([720, 360]) // You can limit the number of representatons
            ->save()
        ;

        return 'failed';
    }

    public function actionDash()
    {
        // $ffmpeg = $this->setupFFMpeg();
        // $video = $ffmpeg->open('/var/www/html/public/media/video/wormhole.mp4');
        // $video->dash()
        //     ->x264()
        //     ->autoGenerateRepresentations([720, 360]) // You can limit the number of representatons
        //     ->save()
        // ;

        // // if ($result) {
        // //     return 'success';
        // // }

        return 'failed';
    }

    public function actionWatch()
    {
        return $this->render('watch');
    }

    public function actionError()
    {
        return $this->render('error');
    }

    private function setupFFMpeg()
    {
        return FFMpeg::create();
    }
}
