<?php

namespace VitalyArt\DemoParser;

use DateTime;

class Demo
{
    private $demoProtocol;
    private $netProtocol;
    private $mapName;
    private $clientName;
    private $entries;
    private $startTime;
    private $endTime;

    public function __construct(
        int $demoProtocol,
        int $netProtocol,
        string $mapName,
        string $clientName,
        array $entries,
        ?DateTime $startTime,
        ?DateTime $endTime
    )
    {
        $this->demoProtocol = $demoProtocol;
        $this->netProtocol = $netProtocol;
        $this->mapName = $mapName;
        $this->clientName = $clientName;
        $this->entries = $entries;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
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

    public function getStartTime(): ?DateTime
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTime
    {
        return $this->endTime;
    }
}
