<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use DateTimeImmutable;
use VitalyArt\DemoParser\Enums\EntryTypeEnum;
use VitalyArt\DemoParser\Exceptions\FileNotExistsException;
use VitalyArt\DemoParser\Exceptions\FileNotSpecifiedException;
use VitalyArt\DemoParser\Exceptions\IsNotADemoException;
use VitalyArt\DemoParser\Exceptions\NotReadableException;
use VitalyArt\DemoParser\Exceptions\WrongExtensionException;

class Parser
{
    private const OFFSET_DEMO_PROTOCOL = 8;
    private const OFFSET_NET_PROTOCOL = 12;
    private const OFFSET_MAP_NAME = 16;
    private const OFFSET_CLIENT_NAME = 276;
    private const OFFSET_ENTRIES_TABLE = 540;
    private const SIZE_PROTOCOL_STRING = 260;
    private const SIZE_ENTRY_STRING = 64;
    private const SIZE_INT = 4;
    private const SIZE_FLOAT = 4;
    private const SIZE_ENTRY = 96;
    private const OFFSET_ENTRY_TYPE = 4;
    private const OFFSET_ENTRY_FLAGS = 68;
    private const OFFSET_ENTRY_CDTRACK = 72;
    private const OFFSET_ENTRY_TRACKTIME = 76;
    private const OFFSET_ENTRY_FRAMES = 80;
    private const OFFSET_ENTRY_OFFSET = 84;
    private const OFFSET_ENTRY_FILELENGTH = 88;

    /**
     * Demo object
     */
    private Demo $demo;

    /**
     * Name of demo file without extension
     */
    private string $fileName;

    /**
     * @var resource
     */
    private $handle;

    /**
     * @var Entry[]|null
     */
    private array|null $entries = null;

    /**
     * @var string Path to demo file
     */
    private string $demoFile = '';

    /**
     * Process
     */
    private function bootstrap(): void
    {
        $this->fileName = pathinfo($this->demoFile, PATHINFO_FILENAME);
        $this->checkFile();
        $this->handle();
        $this->fillDemo();
        fclose($this->handle);
    }

    /**
     * Set demo file
     * @param string $demoFile Path to demo file
     */
    public function setDemoFile(string $demoFile): void
    {
        $this->demoFile = $demoFile;
    }

    /**
     * Fill demo from data
     */
    private function fillDemo(): void
    {
        $demoProtocol = $this->readInt(self::OFFSET_DEMO_PROTOCOL);
        $netProtocol = $this->readInt(self::OFFSET_NET_PROTOCOL);
        $mapName = $this->readData(self::OFFSET_MAP_NAME, self::SIZE_PROTOCOL_STRING);
        $clientName = $this->readData(self::OFFSET_CLIENT_NAME, self::SIZE_PROTOCOL_STRING);
        $entries = $this->getEntries();
        $startDate = $this->getStartDate();
        $duration = $this->getDuration();
        $endTime = $this->computeEndTime($startDate, $duration);

        $this->demo = new Demo(
            $demoProtocol ?: 0,
            $netProtocol ?: 0,
            $mapName ?: '',
            $clientName ?: '',
            $entries,
            $startDate,
            $endTime,
            $duration,
        );
    }

    /**
     * @return Demo
     * @throws FileNotSpecifiedException
     */
    public function getDemo(): Demo
    {
        if (!$this->demoFile) {
            throw new FileNotSpecifiedException('No demo file specified');
        }

        $this->bootstrap();
        return $this->demo;
    }

    /**
     * Checks a file
     * @throws FileNotExistsException If file not found on file system
     * @throws WrongExtensionException If file extension is not a DEM
     */
    private function checkFile(): void
    {
        if (!is_file($this->demoFile)) {
            throw new FileNotExistsException("Demo file not found in path {$this->demoFile}");
        }

        if (pathinfo($this->demoFile, PATHINFO_EXTENSION) !== 'dem') {
            throw new WrongExtensionException("Extension of file {$this->demoFile} is not DEM");
        }
    }

    /**
     * @throws NotReadableException If file is not readable
     * @throws IsNotADemoException IF file is a not demo
     */
    private function handle(): void
    {
        $this->handle = fopen($this->demoFile, 'r');

        if (!$this->handle) {
            throw new NotReadableException("File {$this->demoFile} is not readable");
        }

        if ($this->readData(0, 8) !== 'HLDEMO') {
            throw new IsNotADemoException("File {$this->demoFile} is not a demo");
        }
    }

    /**
     * @return Entry[]
     */
    private function getEntries(): array
    {
        $entriesOffset = $this->readInt(self::OFFSET_ENTRIES_TABLE);

        if ($entriesOffset === false) {
            return [];
        }

        $entriesCount = $this->readInt($entriesOffset);

        if ($entriesCount === false) {
            return [];
        }

        $this->entries = [];

        for ($i = 0; $i < $entriesCount; $i++) {
            $baseOffset = $entriesOffset + self::SIZE_ENTRY * $i;
            $typeStringRaw = $this->readData($baseOffset + self::OFFSET_ENTRY_TYPE, self::SIZE_ENTRY_STRING);

            if (!$typeStringRaw) {
                continue;
            }

            $typeString = trim(mb_convert_encoding($typeStringRaw, 'UTF-8', 'ISO-8859-1'));
            $typeString = mb_convert_case($typeString, MB_CASE_LOWER, 'UTF-8');

            $entryType = EntryTypeEnum::tryFrom($typeString);
            if (!$entryType) {
                continue;
            }

            $type = $this->readInt($baseOffset);
            $flags = $this->readInt($baseOffset + self::OFFSET_ENTRY_FLAGS);
            $cdTrack = $this->readInt($baseOffset + self::OFFSET_ENTRY_CDTRACK);
            $trackTime = $this->readFloat($baseOffset + self::OFFSET_ENTRY_TRACKTIME);
            $frames = $this->readInt($baseOffset + self::OFFSET_ENTRY_FRAMES);
            $offset = $this->readInt($baseOffset + self::OFFSET_ENTRY_OFFSET);
            $fileLength = $this->readInt($baseOffset + self::OFFSET_ENTRY_FILELENGTH);

            if ($type === false || $flags === false || $cdTrack === false
                || $trackTime === false || $frames === false
                || $offset === false || $fileLength === false
            ) {
                continue;
            }

            $entry = new Entry(
                $entryType,
                $type,
                $typeStringRaw,
                $flags,
                $cdTrack,
                $trackTime,
                $frames,
                $offset,
                $fileLength,
            );

            if ($this->isValidEntry($entry)) {
                $this->entries[$typeString] = $entry;
            }
        }

        return $this->entries;
    }

    /**
     * Start time
     */
    private function getStartDate(): DateTimeImmutable|null
    {
        if (preg_match('/.+-(\d+)-.+/', $this->fileName, $matches)) {
            return DateTimeImmutable::createFromFormat('ymdHi', $matches[1]);
        }

        return null;
    }

    private function computeEndTime(DateTimeImmutable|null $startTime, int|false $duration): DateTimeImmutable|null
    {
        if (!$startTime || $duration === false) {
            return null;
        }

        return $startTime->modify("+ {$duration} seconds");
    }

    private function getDuration(): int|false
    {
        foreach ($this->getEntries() as $entry) {
            if ($entry->getTypeString() === EntryTypeEnum::PLAYBACK) {
                return intval($entry->getTrackTime());
            }
        }

        return false;
    }

    private function isValidEntry(Entry $entry): bool
    {
        return $entry->getTrackTime() >= 0
            && $entry->getFrames() >= 0
            && $entry->getOffset() >= 0
            && $entry->getFileLength() >= 0;
    }

    private function readInt(int $offset): int|false
    {
        if (fseek($this->handle, $offset) === -1) {
            return false;
        }

        $data = fread($this->handle, self::SIZE_INT);

        if ($data === false || strlen($data) < self::SIZE_INT) {
            return false;
        }

        $result = unpack('i', $data);
        return $result[1];
    }

    private function readFloat(int $offset): float|false
    {
        if (fseek($this->handle, $offset) === -1) {
            return false;
        }

        $data = fread($this->handle, self::SIZE_FLOAT);

        if ($data === false || strlen($data) < self::SIZE_FLOAT) {
            return false;
        }

        $result = unpack('f', $data);
        return $result[1];
    }

    private function readData(int $offset, int $len): string|false
    {
        if (fseek($this->handle, $offset) === -1) {
            return false;
        }

        $data = fread($this->handle, $len);

        if ($data === false) {
            return false;
        }

        return trim($data);
    }
}
