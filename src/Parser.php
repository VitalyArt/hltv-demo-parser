<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use DateTime;
use VitalyArt\DemoParser\Enums\EntryTypeEnum;
use VitalyArt\DemoParser\Exceptions\FileNotExistsException;
use VitalyArt\DemoParser\Exceptions\FileNotSpecifiedException;
use VitalyArt\DemoParser\Exceptions\IsNotADemoException;
use VitalyArt\DemoParser\Exceptions\NotReadableException;
use VitalyArt\DemoParser\Exceptions\WrongExtensionException;

class Parser
{
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
        $this->demo = new Demo(
            $this->readInt(8),
            $this->readInt(12),
            $this->readData(16, 260),
            $this->readData(276, 260),
            $this->getEntries(),
            $this->getStartDate(),
            $this->getEndTime()
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
        $globalOffset = 96;

        if ($this->entries === null) {
            $entriesOffset = $this->readInt(540);
            $entriesCount  = $this->readInt($entriesOffset);

            $this->entries = [];

            for ($i = 0; $i < $entriesCount; $i++) {
                if ($this->readData($entriesOffset + $globalOffset * $i + 4, 64)) {
                    $typeString = trim($this->readData($entriesOffset + $globalOffset * $i + 4, 64));
                    $typeString = mb_convert_case($typeString, MB_CASE_LOWER, 'UTF-8');

                    $entry = new Entry(
                        EntryTypeEnum::from($typeString),
                        $this->readInt($entriesOffset + $globalOffset * $i),
                        $this->readData($entriesOffset + $globalOffset * $i + 4, 64),
                        $this->readInt($entriesOffset + $globalOffset * $i + 68),
                        $this->readInt($entriesOffset + $globalOffset * $i + 72),
                        $this->readFloat($entriesOffset + $globalOffset * $i + 76),
                        $this->readInt($entriesOffset + $globalOffset * $i + 80),
                        $this->readInt($entriesOffset + $globalOffset * $i + 84),
                        $this->readInt($entriesOffset + $globalOffset * $i + 88)
                    );

                    if ($this->isValidEntry($entry)) {
                        $this->entries[$typeString] = $entry;
                    }
                }
            }
        }

        return $this->entries;
    }

    /**
     * Start time
     * @return DateTime|null
     */
    private function getStartDate(): DateTime|null
    {
        if (preg_match('/.+-(\d+)-.+/', $this->fileName, $matches)) {
            return DateTime::createFromFormat('ymdHi', $matches[1]);
        }

        return null;
    }

    /**
     * End time
     * @return DateTime|null
     */
    private function getEndTime(): DateTime|null
    {
        $startTime = $this->getStartDate();

        if (!$startTime) {
            return null;
        }

        $playbackTime = null;

        foreach ($this->getEntries() as $entry) {
            if ($entry->getTypeString() === EntryTypeEnum::PLAYBACK) {
                $playbackTime = intval($entry->getTrackTime());
                break;
            }
        }

        if ($playbackTime === null) {
            return null;
        }

        return $startTime->modify("+ {$playbackTime} seconds");
    }

    private function isValidEntry(Entry $entry): bool
    {
        foreach ($entry as $value) {
            if ($value === false) {
                return false;
            }
        }

        return true;
    }

    private function readInt(int $offset): int|false
    {
        if (fseek($this->handle, $offset) == -1) {
            return false;
        }

        $data = unpack('i', fread($this->handle, 4));
        return $data[1];
    }

    private function readUint(int $offset): int|false
    {
        if (fseek($this->handle, $offset) == -1) {
            return false;
        }

        $data = unpack('I', fread($this->handle, 4));
        return $data[1];
    }

    private function readFloat(int $offset): float|false
    {
        if (fseek($this->handle, $offset) == -1) {
            return false;
        }

        $data = unpack('f', fread($this->handle, 4));
        return $data[1];
    }

    private function readData(int $offset, int $Len): string|false
    {
        if (fseek($this->handle, $offset) == -1) {
            return false;
        }

        return trim(fread($this->handle, $Len));
    }
}
