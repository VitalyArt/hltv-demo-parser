<?php

namespace VitalyArt\DemoParser;

use exceptions\FileException;

class Parser implements \ArrayAccess
{
    const BAD_FILE_EXTENSION = 10;
    const BAD_FILE_FORMAT = 20;
    const FILE_NOT_READY = 30;

    private $data = [];

    private $fileName;
    
    private $handle;

    private function checkFile($file)
    {
        if(!is_file($file)) {
            throw new FileException("Demo file not found in path {$file}");
        }
        if(pathinfo($file, PATHINFO_EXTENSION) !== 'dem') {
            throw new FileException("{$file} is not a DEMO");
        }
    }
    
    /**
     * @param string $file
     * @throws \Exception
     */
    function __construct($file)
    {
        $this->checkFile($file);
        $this->handle = fopen($file, "r");

        if (!$this->handle) {
            throw new \Exception('File not found or unable to read', static::FILE_NOT_READY);
        }

        if ($this->readData(0, 8) !== "HLDEMO") {
            throw new \Exception('Bad file format', static::BAD_FILE_FORMAT);
        }

        $entriesList = [];

        $entriesOffset = $this->readInt(540);
        $entriesCount = $this->readInt($entriesOffset);

        for ($i = 0; $i < $entriesCount; $i++) {
            if ($this->readData($entriesOffset + 96 * $i + 4, 64)) {
                $key = trim($this->readData($entriesOffset + 96 * $i + 4, 64));
                $key = mb_convert_case($key, MB_CASE_LOWER, "UTF-8");

                $entry = [
                    'nEntryType'    => $this->readInt($entriesOffset + 96 * $i),
                    'szDescription' => $this->readData($entriesOffset + 96 * $i + 4, 64),
                    'nFlags'        => $this->readInt($entriesOffset + 96 * $i + 68),
                    'nCDTrack'      => $this->readInt($entriesOffset + 96 * $i + 72),
                    'fTrackTime'    => $this->readFloat($entriesOffset + 96 * $i + 76),
                    'nFrames'       => $this->readInt($entriesOffset + 96 * $i + 80),
                    'nOffset'       => $this->readInt($entriesOffset + 96 * $i + 84),
                    'nFileLength'   => $this->readInt($entriesOffset + 96 * $i + 88)
                ];

                if ($this->isValidEntry($entry)) {
                    $entriesList[$key] = $entry;
                }
            }
        }

        $this->fileName = pathinfo($file)['filename'];

        $this->data = [
            'demoProtocol' => $this->readInt(8),
            'netProtocol'  => $this->readInt(12),
            'mapName'      => $this->readData(16, 260),
            'clientName'   => $this->readData(276, 260),
            'entries'      => $entriesList,
            'startTime'    => $this->getStartDate(),
            'endTime'      => $this->getEndTime($entriesList),
        ];

        fclose($this->handle);
    }

    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool|\DateTime
     */
    private function getStartDate()
    {
        if (preg_match("/.+-(\d+)-.+/", $this->fileName, $matches)) {
            return \DateTime::createFromFormat('ymdHi', $matches[1]);
        }

        return false;
    }

    private function getEndTime($entriesList)
    {
        $startTime = $this->getStartDate();
        $playbackTime = intval($entriesList['playback']['fTrackTime']);

        if (!$startTime) {
            return false;
        }

        return $startTime->modify('+' . $playbackTime . ' seconds');
    }

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

    /**
     * @param string $file
     * @return string
     */
    private function extOfFile($file)
    {
        $path = explode('.', $file);
        return mb_convert_case(end($path), MB_CASE_LOWER);
    }

    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->data[] = $value;
        } else {
            $this->data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }
}