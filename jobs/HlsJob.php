<?php

namespace app\jobs;

use Streaming\FFMpeg;
use yii\base\BaseObject;
use yii\helpers\Url;

class HlsJob extends BaseObject implements \yii\queue\JobInterface
{
    public $filePath;
    public $video;

    public function execute($queue)
    {
        $keyPath = '/var/www/html/public/media/video/key/';
        $keyExtension = '.pem';

        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($this->filePath);
        $url = Url::to('/stream/key/'.$this->video->id);
        $video->hls()
            ->encryption($keyPath.$this->video->id.$keyExtension, $url)
            ->x264()
            ->autoGenerateRepresentations([720, 360]) // You can limit the number of representatons
            ->save($this->video->video_path)
        ;

        return 'failed';
    }
}
