<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use DateTimeImmutable;

readonly class Demo
{
    public function __construct(
        private int $demoProtocol,
        private int $netProtocol,
        private string $mapName,
        private string $clientName,
        private array $entries,
        private DateTimeImmutable|null $startTime,
        private DateTimeImmutable|null $endTime,
        private int|false $duration,
    )
    {

    }

    public function getDemoProtocol(): int
    {
        return $this->demoProtocol;
    }

    public function getNetProtocol(): int
    {
        return $this->netProtocol;
    }

    public function getMapName(): string
    {
        return $this->mapName;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getStartTime(): DateTimeImmutable|null
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTimeImmutable|null
    {
        return $this->endTime;
    }

    public function getDuration(): int|false
    {
        return $this->duration;
    }
}
