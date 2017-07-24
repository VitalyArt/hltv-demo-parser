<?php

namespace VitalyArt;


class DemoParser implements \ArrayAccess
{
    const BAD_FILE_EXTENSION = 10;
    const BAD_FILE_FORMAT = 20;
    const FILE_NOT_READY = 30;

    private $data = [];

    private $fileName;

    /**
     * @param string $file
     * @throws \Exception
     */
    function __construct($file)
    {
        if ($this->extOfFile($file) !== "dem") {
            throw new \Exception('Bad file extension', static::BAD_FILE_EXTENSION);
        }

        $handle = fopen($file, "r");

        if (!$handle) {
            throw new \Exception('File not found or unable to read', static::FILE_NOT_READY);
        }

        if ($this->readData($handle, 0, 8) !== "HLDEMO") {
            throw new \Exception('Bad file format', static::BAD_FILE_FORMAT);
        }

        $entriesList = [];

        $entriesOffset = $this->readInt($handle, 540);
        $entriesCount = $this->readInt($handle, $entriesOffset);

        for ($i = 0; $i < $entriesCount; $i++) {
            if ($this->readData($handle, $entriesOffset + 96 * $i + 4, 64)) {
                $key = trim($this->readData($handle, $entriesOffset + 96 * $i + 4, 64));
                $key = mb_convert_case($key, MB_CASE_LOWER, "UTF-8");

                $entry = [
                    'nEntryType'    => $this->readInt($handle, $entriesOffset + 96 * $i),
                    'szDescription' => $this->readData($handle, $entriesOffset + 96 * $i + 4, 64),
                    'nFlags'        => $this->readInt($handle, $entriesOffset + 96 * $i + 68),
                    'nCDTrack'      => $this->readInt($handle, $entriesOffset + 96 * $i + 72),
                    'fTrackTime'    => $this->readFloat($handle, $entriesOffset + 96 * $i + 76),
                    'nFrames'       => $this->readInt($handle, $entriesOffset + 96 * $i + 80),
                    'nOffset'       => $this->readInt($handle, $entriesOffset + 96 * $i + 84),
                    'nFileLength'   => $this->readInt($handle, $entriesOffset + 96 * $i + 88)
                ];

                if ($this->isValidEntry($entry)) {
                    $entriesList[$key] = $entry;
                }
            }
        }

        $this->fileName = pathinfo($file)['filename'];

        $this->data = [
            'demoProtocol' => $this->readInt($handle, 8),
            'netProtocol'  => $this->readInt($handle, 12),
            'mapName'      => $this->readData($handle, 16, 260),
            'clientName'   => $this->readData($handle, 276, 260),
            'entries'      => $entriesList,
            'startTime'    => $this->getStartDate(),
            'endTime'      => $this->getEndTime($entriesList),
        ];

        fclose($handle);
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
     * @param resource $file
     * @param integer $offset
     * @return bool
     */
    private function readInt($file, $offset)
    {
        if (fseek($file, $offset) == -1) {
            return false;
        }

        $data = unpack("i", fread($file, 4));
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
     * @param resource $file
     * @param integer $offset
     * @return bool
     */
    private function readFloat($file, $offset)
    {
        if (fseek($file, $offset) == -1) {
            return false;
        }

        $data = unpack("f", fread($file, 4));
        return $data[1];
    }

    /**
     * @param resource $file
     * @param integer $offset
     * @param integer $Len
     * @return string
     */
    private function readData($file, $offset, $Len)
    {
        if (fseek($file, $offset) == -1) {
            return false;
        }

        return trim(fread($file, $Len));
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