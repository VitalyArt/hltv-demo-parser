<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use VitalyArt\DemoParser\Enums\EntryTypeEnum;

readonly class Entry
{
    public function __construct(
        private EntryTypeEnum $typeString,
        private int $type,
        private string $description,
        private int $flags,
        private int $CDTrack,
        private float $trackTime,
        private int $frames,
        private int $offset,
        private int $fileLength,
    )
    {

    }

    /**
     * Entry type
     */
    public function getTypeString(): EntryTypeEnum
    {
        return $this->typeString;
    }

    /**
     * Integer entry type
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Flags
     */
    public function getFlags(): int
    {
        return $this->flags;
    }

    /**
     * CD track
     */
    public function getCDTrack(): int
    {
        return $this->CDTrack;
    }

    /**
     * Track time
     */
    public function getTrackTime(): float
    {
        return $this->trackTime;
    }

    /**
     * Frames
     */
    public function getFrames(): int
    {
        return $this->frames;
    }

    /**
     * Offset
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * File length
     */
    public function getFileLength(): int
    {
        return $this->fileLength;
    }
}
