<?php

namespace VitalyArt\DemoParser;

class Demo
{
    private $demoProtocol;
    private $netProtocol;
    private $mapName;
    private $clientName;
    private $entries;
    private $startTime;
    private $endTime;
    
    public function __construct($demoProtocol, $netProtocol, $mapName, $clientName, $entries, $startTime, $endTime)
    {
        $this->demoProtocol = $demoProtocol;
        $this->netProtocol = $netProtocol;
        $this->mapName = $mapName;
        $this->clientName = $clientName;
        $this->entries = $entries;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
    
    public function getDemoProtocol()
    {
        return $this->demoProtocol;
    }

    public function getNetProtocol()
    {
        return $this->netProtocol;
    }

    public function getMapName()
    {
        return $this->mapName;
    }

    public function getClientName()
    {
        return $this->clientName;
    }

    public function getEntries()
    {
        return $this->entries;
    }

    public function getStartTime()
    {
        return $this->startTime;
    }

    public function getEndTime()
    {
        return $this->endTime;
    }
}
