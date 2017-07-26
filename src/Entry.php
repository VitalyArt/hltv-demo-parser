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
    public function __construct($typeString, $type, $description, $flags, $CDTrack, $trackTime, $frames, $offset, $fileLength)
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
    public function getTypeString()
    {
        return $this->typeString;
    }

    /**
     * Integer entry type
     * @return integer
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Description
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Flags
     * @return integer
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * CD track
     * @return string
     */
    public function getCDTrack()
    {
        return $this->CDTrack;
    }

    /**
     * Track time
     * @return float
     */
    public function getTrackTime()
    {
        return $this->trackTime;
    }

    /**
     * Frames
     * @return integer
     */
    public function getFrames()
    {
        return $this->frames;
    }

    /**
     * Offset
     * @return integer
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * File length
     * @return integer
     */
    public function getFileLength()
    {
        return $this->fileLength;
    }
}
