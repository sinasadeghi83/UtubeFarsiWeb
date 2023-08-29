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

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Exception\ExceptionInterface;
use FFMpeg\Format\VideoInterface;
use Streaming\Exception\InvalidArgumentException;
use Streaming\Exception\RuntimeException;

class AutoReps implements \IteratorAggregate
{
    /** @var \FFMpeg\FFProbe\DataMapping\Stream */
    private $video;

    /** @var \FFMpeg\FFProbe\DataMapping\Format */
    private $original_format;

    /**
     * regular video's heights.
     *
     * @var array side_values
     */
    private $sides = [144, 240, 360, 480, 720, 1080, 1440, 2160];

    /** @var array */
    private $k_bitrate;

    /** @var bool */
    private $sort = true;

    /** @const VideoInterface */
    private $format;

    /**
     * AutoReps constructor.
     */
    public function __construct(Media $media, VideoInterface $format, array $sides = null, array $k_bitrate = null)
    {
        $this->format = $format;
        $this->video = $media->getStreams()->videos()->first();
        $this->original_format = $media->getFormat();
        $this->sides($sides, $k_bitrate);
        $this->kiloBitrate($k_bitrate);
    }

    /**
     * Set sort order for reps.
     */
    public function sort(bool $sort)
    {
        $this->sort = $sort;
    }

    public function getCalculatedReps(bool $sort = false): array
    {
        $reps = [];
        foreach ($this->sides as $key => $height) {
            array_push($reps, $this->addRep($this->k_bitrate[$key], $this->computeSide($height, 'Width'), $height));
        }

        if ($sort) {
            $this->sortReps($reps);
        }

        return $reps;
    }

    public function getOriginalRep(): Representation
    {
        $dimension = $this->getDimensions();
        $width = $this->computeSide($dimension->getHeight(), 'Width');
        $height = $this->computeSide($dimension->getWidth(), 'Height');

        return $this->addRep($this->getKiloBitRate(), $width, $height);
    }

    public function getSides(): array
    {
        return $this->sides;
    }

    public function getKBitrate(): array
    {
        return $this->k_bitrate;
    }

    /**
     * Retrieve an external iterator reps.
     *
     * @return \Traversable An instance of an object implementing <b>Iterator</b> or <b>Traversable</b>
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        $reps = $this->getCalculatedReps();
        array_push($reps, $this->getOriginalRep());
        $this->sortReps($reps);

        return new \ArrayIterator($reps);
    }

    private function getDimensions(): Dimension
    {
        try {
            return $this->video->getDimensions();
        } catch (ExceptionInterface $e) {
            throw new RuntimeException('Unable to extract dimensions.: '.$e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @return mixed
     *
     * @throws InvalidArgumentException
     */
    private function getKiloBitRate(): int
    {
        if (!$this->video->has('bit_rate')) {
            if (!$this->original_format->has('bit_rate')) {
                throw new InvalidArgumentException('Unable to extract bitrate.');
            }

            return intval(($this->original_format->get('bit_rate') / 1024) * .9);
        }

        return intval($this->video->get('bit_rate') / 1024);
    }

    /**
     * @TODO: fix #79
     */
    private function kiloBitrate(?array $k_bitrate_values): void
    {
        $k_bit_rates = [];
        $count_sides = count($this->sides);

        if (!is_null($k_bitrate_values)) {
            if ($count_sides !== count($k_bitrate_values)) {
                throw new InvalidArgumentException('The count of side value array must be the same as the count of kilo bitrate array');
            }
            $this->k_bitrate = $k_bitrate_values;

            return;
        }

        $k_bitrate_value = $this->getKiloBitRate();
        $divided_by = 1.5;

        while ($count_sides) {
            $k_bit_rates[] = (($k_bitrate = intval($k_bitrate_value / $divided_by)) < 64) ? 64 : $k_bitrate;
            $divided_by += .5;
            --$count_sides;
        }

        $this->k_bitrate = array_reverse($k_bit_rates);
    }

    private function sideFilter(int $height): bool
    {
        return $height < $this->getDimensions()->getHeight();
    }

    private function sides(?array $sides, ?array $k_bitrate): void
    {
        if (!is_null($sides) && is_null($k_bitrate)) {
            sort($sides);
        }

        $this->sides = array_values(array_filter($sides ?? $this->sides, [$this, 'sideFilter']));
    }

    private function computeSide(int $value, string $side): int
    {
        $ratio = clone $this->getDimensions()->getRatio();

        return call_user_func_array([$ratio, 'calculate'.$side], [$value, $this->format->getModulus()]);
    }

    /**
     * @param mixed $k_bitrate
     * @param mixed $width
     * @param mixed $height
     *
     * @throws InvalidArgumentException
     */
    private function addRep($k_bitrate, $width, $height): Representation
    {
        return (new Representation())->setKiloBitrate($k_bitrate)->setResize($width, $height);
    }

    private function sortReps(array &$reps): void
    {
        usort($reps, function (Representation $rep1, Representation $rep2) {
            $ascending = $rep1->getKiloBitrate() - $rep2->getKiloBitrate();

            return $this->sort ? $ascending : -($ascending);
        });
    }
}
