<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use DateTime;

readonly class Demo
{
    public function __construct(
        private int $demoProtocol,
        private int $netProtocol,
        private string $mapName,
        private string $clientName,
        private array $entries,
        private DateTime|null $startTime,
        private DateTime|null $endTime,
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

    public function getStartTime(): DateTime|null
    {
        return $this->startTime;
    }

    public function getEndTime(): DateTime|null
    {
        return $this->endTime;
    }
}
