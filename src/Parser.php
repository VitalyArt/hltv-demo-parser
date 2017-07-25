<?php

namespace VitalyArt\DemoParser;

use VitalyArt\DemoParser\exceptions\FileNotExistsException;
use VitalyArt\DemoParser\exceptions\FileNotSpecifiedException;
use VitalyArt\DemoParser\exceptions\IsNotADemoException;
use VitalyArt\DemoParser\exceptions\NotReadableException;
use VitalyArt\DemoParser\exceptions\WrongExtensionException;

class Parser
{
    /**
     * Demo object
     * @var Demo
     */
    private $demo;

    /**
     * @var string Name of demo file without extension
     */
    private $fileName;
    
    /**
     * @var resource
     */
    private $handle;
    
    /**
     * @var Entry[]
     */
    private $entries;
    
    /**
     * @var string Path to demo file 
     */
    private $demoFile;

    /**
     * Process
     */
    private function bootstrap()
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
    public function setDemoFile($demoFile)
    {
        $this->demoFile = $demoFile;
    }
    
    /**
     * Fill demo from data
     */
    private function fillDemo()
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
    public function getDemo()
    {
        if(!$this->demoFile) {
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
    private function checkFile()
    {
        if(!is_file($this->demoFile)) {
            throw new FileNotExistsException("Demo file not found in path {$this->demoFile}");
        }
        if(pathinfo($this->demoFile, PATHINFO_EXTENSION) !== 'dem') {
            throw new WrongExtensionException("Extension of file {$this->demoFile} is not DEM");
        }
    }
    
    /**
     * @throws NotReadableException If file is not readable
     * @throws IsNotADemoException IF file is a not demo
     */
    private function handle()
    {
        $this->handle = fopen($this->demoFile, "r");
        if (!$this->handle) {
            throw new NotReadableException("File {$this->demoFile} is not readable");
        }
        if ($this->readData(0, 8) !== "HLDEMO") {
            throw new IsNotADemoException("File {$this->demoFile} is not a demo");
        }
    }

    /**
     * @return Entry[]
     */
    private function getEntries()
    {
        if($this->entries === null) {
            $entriesOffset = $this->readInt(540);
            $entriesCount = $this->readInt($entriesOffset);
            $this->entries = [];
            for ($i = 0; $i < $entriesCount; $i++) {
                if ($this->readData($entriesOffset + 96 * $i + 4, 64)) {
                    $key = trim($this->readData($entriesOffset + 96 * $i + 4, 64));
                    $key = mb_convert_case($key, MB_CASE_LOWER, "UTF-8");
                    $entry = new \VitalyArt\DemoParser\Entry(
                        $key,
                        $this->readInt($entriesOffset + 96 * $i),
                        $this->readData($entriesOffset + 96 * $i + 4, 64),
                        $this->readInt($entriesOffset + 96 * $i + 68),
                        $this->readInt($entriesOffset + 96 * $i + 72),
                        $this->readFloat($entriesOffset + 96 * $i + 76),
                        $this->readInt($entriesOffset + 96 * $i + 80),
                        $this->readInt($entriesOffset + 96 * $i + 84),
                        $this->readInt($entriesOffset + 96 * $i + 88)
                    );
                    if($this->isValidEntry($entry)) {
                        $this->entries[$key] = $entry;
                    }
                }
            }
        }
        return $this->entries;
    }
    
    /**
     * Start time
     * @return null|\DateTime
     */
    private function getStartDate()
    {
        if (preg_match("/.+-(\d+)-.+/", $this->fileName, $matches)) {
            return \DateTime::createFromFormat('ymdHi', $matches[1]);
        }
        return null;
    }

    /**
     * End time
     * @return null|\DateTime
     */
    private function getEndTime()
    {
        $startTime = $this->getStartDate();
        if (!$startTime) {
            return null;
        }
        $playbackTime = null;
        foreach($this->getEntries() as $entry) {
            if($entry->getTypeString() === 'playback') {
                $playbackTime = intval($entry->getTrackTime());
                break;
            }
        }
        if ($playbackTime === null) {
            return null;
        }
        return $startTime->modify("+ {$playbackTime} seconds");
    }
    
    /**
     * Check entry
     * @param Entry $entry
     * @return boolean
     */
    private function isValidEntry($entry)
    {
        foreach ($entry as $value) {
            if ($value === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param integer $offset
     * @return bool
     */
    private function readInt($offset)
    {
        if (fseek($this->handle, $offset) == -1) {
            return false;
        }
        $data = unpack("i", fread($this->handle, 4));
        return $data[1];
    }

    /**
     * @param resource $file
     * @param integer $offset
     * @return bool
     */
    private function readUint($file, $offset)
    {
        if (fseek($file, $offset) == -1) {
            return false;
        }
        $data = unpack("I", fread($file, 4));
        return $data[1];
    }

    /**
     * @param integer $offset
     * @return bool
     */
    private function readFloat($offset)
    {
        if (fseek($this->handle, $offset) == -1) {
            return false;
        }
        $data = unpack("f", fread($this->handle, 4));
        return $data[1];
    }

    /**
     * @param integer $offset
     * @param integer $Len
     * @return string
     */
    private function readData($offset, $Len)
    {
        if (fseek($this->handle, $offset) == -1) {
            return false;
        }
        return trim(fread($this->handle, $Len));
    }
}
