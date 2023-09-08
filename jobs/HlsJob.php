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
        $keyPath = "/var/www/html/public/media/video/key/{$this->video->id}.pem";
        $this->removeIfExists($keyPath);
        $this->removeIfExists(dirname($this->video->video_path));
        $ffmpeg = FFMpeg::create();
        $video = $ffmpeg->open($this->filePath);

        $url = Url::to('/stream/key/'.$this->video->id);

        $video->hls()
            ->encryption($keyPath, $url)
            ->x264()
            ->autoGenerateRepresentations([720, 360]) // You can limit the number of representatons
            ->save($this->video->video_path)
        ;

        $this->removeIfExists($this->filePath);
    }

    public function removeIfExists($path)
    {
        if (file_exists($path)) {
            if (is_dir($path)) {
                return $this->removeDirectory($path);
            }

            return unlink($path);
        }

        return true;
    }

    public function removeDirectory($dir)
    {
        $it = new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator(
            $it,
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        return rmdir($dir);
    }
}
