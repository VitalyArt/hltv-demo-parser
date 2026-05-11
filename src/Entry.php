<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use VitalyArt\DemoParser\Enums\EntryTypeEnum;

readonly class Entry
{
    /** @param DemoFrame[] $parsedFrames */
    public function __construct(
        private EntryTypeEnum $type,
        private int $typeNumber,
        private string $description,
        private int $flags,
        private int $CDTrack,
        private float $trackTime,
        private int $frames,
        private int $offset,
        private int $fileLength,
        private array $parsedFrames = [],
    )
    {
    }

    public function getTypeString(): EntryTypeEnum
    {
        return $this->type;
    }

    public function getType(): int
    {
        return $this->typeNumber;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFlags(): int
    {
        return $this->flags;
    }

    public function getCDTrack(): int
    {
        return $this->CDTrack;
    }

    public function getTrackTime(): float
    {
        return $this->trackTime;
    }

    public function getFrames(): int
    {
        return $this->frames;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getFileLength(): int
    {
        return $this->fileLength;
    }

    /** @return DemoFrame[] */
    public function getParsedFrames(): array
    {
        return $this->parsedFrames;
    }
}
