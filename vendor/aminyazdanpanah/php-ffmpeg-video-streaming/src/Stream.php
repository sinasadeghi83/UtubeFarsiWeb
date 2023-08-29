<?php

/**
 * This file is part of the PHP-FFmpeg-video-streaming package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Streaming;

use FFMpeg\Exception\ExceptionInterface;
use Streaming\Clouds\Cloud;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;
use Streaming\Filters\StreamFilterInterface;
use Streaming\Traits\Formats;

abstract class Stream implements StreamInterface
{
    use Formats;

    /** @var string */
    protected $path;

    /** @var Media */
    private $media;

    /** @var string */
    private $tmp_dir = '';

    /**
     * Stream constructor.
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
        $this->path = $media->getPathfile();
    }

    /**
     * clear tmp files.
     */
    public function __destruct()
    {
        // make sure that FFmpeg process has benn terminated
        usleep(500000);
        File::remove($this->tmp_dir);

        if ($this->media->isTmp()) {
            File::remove($this->media->getPathfile());
        }
    }

    public function getMedia(): Media
    {
        return $this->media;
    }

    public function isTmpDir(): bool
    {
        return (bool) $this->tmp_dir;
    }

    public function pathInfo(int $option): string
    {
        return pathinfo($this->path, $option);
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function save(string $path = null, array $clouds = []): Stream
    {
        $this->paths($path, $clouds);
        $this->run();
        $this->clouds($clouds, $path);

        return $this;
    }

    public function live(string $url): void
    {
        $this->path = $url;
        $this->run();
    }

    public function metadata(): Metadata
    {
        return new Metadata($this);
    }

    protected function getFilePath(): string
    {
        return str_replace(
            '\\',
            '/',
            $this->pathInfo(PATHINFO_DIRNAME).'/'.$this->pathInfo(PATHINFO_FILENAME)
        );
    }

    abstract protected function getPath(): string;

    abstract protected function getFilter(): StreamFilterInterface;

    private function moveTmp(?string $path): void
    {
        if ($this->isTmpDir() && !is_null($path)) {
            File::move($this->tmp_dir, dirname($path));
            $this->path = $path;
            $this->tmp_dir = '';
        }
    }

    /**
     * @param string $path
     */
    private function clouds(array $clouds, ?string $path): void
    {
        if (!empty($clouds)) {
            Cloud::uploadDirectory($clouds, $this->tmp_dir);
            $this->moveTmp($path);
        }
    }

    /**
     * Run FFmpeg to package media content.
     */
    private function run(): void
    {
        $this->media->addFilter($this->getFilter());

        $commands = (new CommandBuilder($this->media, $this->getFormat()))->build($this->getFormat(), $this->getPath());
        $pass = $this->format->getPasses();
        $listeners = $this->format->createProgressListener($this->media->baseMedia(), $this->media->getFFProbe(), 1, $pass);

        try {
            $this->media->getFFMpegDriver()->command($commands, false, $listeners);
        } catch (ExceptionInterface $e) {
            throw new RuntimeException('An error occurred while saving files: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    private function paths(?string $path, array $clouds): void
    {
        if (!empty($clouds)) {
            $this->tmp_dir = File::tmpDir();
            $this->path = $this->tmp_dir.basename($clouds['options']['filename'] ?? $path ?? $this->path);
        } elseif (!is_null($path)) {
            if (strlen($path) > PHP_MAXPATHLEN) {
                throw new InvalidArgumentException('The path is too long');
            }

            File::makeDir(dirname($path));
            $this->path = $path;
        } elseif ($this->media->isTmp()) {
            throw new InvalidArgumentException('You need to specify a path. It is not possible to save to a tmp directory');
        }
    }
}
