<?php

namespace VitalyArt\DemoParser;

class Entry
{
    private $typeString;
    private $type;
    private $description;
    private $flags;
    private $CDTrack;
    private $trackTime;
    private $frames;
    private $offset;
    private $fileLength;

    /**
     * @param string $typeString
     * @param integer $type
     * @param string $description
     * @param integer $flags
     * @param string $CDTrack
     * @param float $trackTime
     * @param integer $frames
     * @param integer $offset
     * @param integer $fileLength
     */
    public function __construct(
        string $typeString,
        int $type,
        string $description,
        int $flags,
        string $CDTrack,
        float $trackTime,
        int $frames,
        int $offset,
        int $fileLength
    )
    {
        $this->typeString = $typeString;
        $this->type = $type;
        $this->description = $description;
        $this->flags = $flags;
        $this->CDTrack = $CDTrack;
        $this->trackTime = $trackTime;
        $this->frames = $frames;
        $this->offset = $offset;
        $this->fileLength = $fileLength;
    }

    /**
     * Entry type
     * @return string
     */
    public function getTypeString(): string
    {
        return $this->typeString;
    }

    /**
     * Integer entry type
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Description
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Flags
     * @return int
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * CD track
     * @return string
     */
    public function getCDTrack(): string
    {
        return $this->CDTrack;
    }

    /**
     * Track time
     * @return float
     */
    public function getTrackTime(): float
    {
        return $this->trackTime;
    }

    /**
     * Frames
     * @return int
     */
    public function getFrames(): int
    {
        return $this->frames;
    }

    /**
     * Offset
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * File length
     * @return int
     */
    public function getFileLength(): int
    {
        return $this->fileLength;
    }
}
