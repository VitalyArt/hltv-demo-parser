<?php

declare(strict_types=1);

namespace VitalyArt\DemoParser;

use DateTimeImmutable;
use VitalyArt\DemoParser\Enums\EntryTypeEnum;
use VitalyArt\DemoParser\Enums\MacroTypeEnum;
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
    private const OFFSET_MAP_CRC = 536;
    private const OFFSET_ENTRIES_TABLE = 540;
    private const SIZE_PROTOCOL_STRING = 260;
    private const SIZE_ENTRY_STRING = 64;
    private const SIZE_INT = 4;
    private const SIZE_FLOAT = 4;
    private const SIZE_ENTRY = 92;
    private const MACRO_HEADER_SIZE = 9;
    private const EXTINFO_SIZE_NET42 = 560;
    private const EXTINFO_SIZE_NET45 = 464;
    private const MACRO6_PAYLOAD_SIZE = 84;
    private const MACRO7_PAYLOAD_SIZE = 8;
    private const SOUND_PREFIX_SIZE = 8;
    private const SOUND_SUFFIX_SIZE = 16;
    private const MAX_STRING_SCAN = 4096;
    private const LAST_FRAME_SCAN_RANGE = 512;
    private const MAX_VALID_TIME = 100000;
    private const MAX_VALID_FRAME = 1000000;
    private const OFFSET_ENTRY_TYPE = 4;
    private const OFFSET_ENTRY_FLAGS = 68;
    private const OFFSET_ENTRY_CDTRACK = 72;
    private const OFFSET_ENTRY_TRACKTIME = 76;
    private const OFFSET_ENTRY_FRAMES = 80;
    private const OFFSET_ENTRY_OFFSET = 84;
    private const OFFSET_ENTRY_FILELENGTH = 88;

    private Demo $demo;
    private string $fileName;
    private int $demoProtocol = 0;
    private int $netProtocol = 0;

    /** @var resource */
    private $handle;

    /** @var Entry[]|null */
    private array|null $entries = null;

    private string $demoFile = '';

    private function bootstrap(): void
    {
        $this->fileName = pathinfo($this->demoFile, PATHINFO_FILENAME);
        $this->checkFile();
        $this->handle();
        $this->fillDemo();
        fclose($this->handle);
    }

    public function setDemoFile(string $demoFile): void
    {
        $this->demoFile = $demoFile;
    }

    private function fillDemo(): void
    {
        $this->demoProtocol = $this->readInt(self::OFFSET_DEMO_PROTOCOL) ?: 0;
        $this->netProtocol = $this->readInt(self::OFFSET_NET_PROTOCOL) ?: 0;
        $mapName = $this->readData(self::OFFSET_MAP_NAME, self::SIZE_PROTOCOL_STRING) ?: '';
        $clientName = $this->readData(self::OFFSET_CLIENT_NAME, self::SIZE_PROTOCOL_STRING) ?: '';
        $mapCrc = $this->readInt(self::OFFSET_MAP_CRC) ?: 0;
        $gameDirectory = $this->readData(self::OFFSET_CLIENT_NAME, self::SIZE_PROTOCOL_STRING) ?: '';
        $entries = $this->getEntries();
        $startDate = $this->getStartDate();
        $duration = $this->getDuration();
        $endTime = $this->computeEndTime($startDate, $duration);

        $this->demo = new Demo(
            $this->demoProtocol,
            $this->netProtocol,
            $mapName,
            $clientName,
            $entries,
            $startDate,
            $endTime,
            $duration,
            $mapCrc,
            $gameDirectory,
        );
    }

    public function getDemo(): Demo
    {
        if (!$this->demoFile) {
            throw new FileNotSpecifiedException('No demo file specified');
        }

        $this->bootstrap();
        return $this->demo;
    }

    private function checkFile(): void
    {
        if (!is_file($this->demoFile)) {
            throw new FileNotExistsException("Demo file not found in path {$this->demoFile}");
        }

        if (pathinfo($this->demoFile, PATHINFO_EXTENSION) !== 'dem') {
            throw new WrongExtensionException("Extension of file {$this->demoFile} is not DEM");
        }
    }

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

    /** @return Entry[] */
    private function getEntries(): array
    {
        $entriesOffset = $this->readInt(self::OFFSET_ENTRIES_TABLE);

        if ($entriesOffset === false) {
            return [];
        }

        $entriesCount = $this->readInt($entriesOffset);

        if ($entriesCount === false || $entriesCount <= 0 || $entriesCount > 1024) {
            return [];
        }

        $this->entries = [];

        for ($i = 0; $i < $entriesCount; $i++) {
            $baseOffset = $entriesOffset + self::SIZE_INT + self::SIZE_ENTRY * $i;
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

            $parsedFrames = $this->parseEntryFrames($offset, $fileLength, $entryType);

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
                $parsedFrames,
            );

            if ($this->isValidEntry($entry)) {
                $this->entries[$typeString] = $entry;
            }
        }

        return $this->entries;
    }

    /** @return DemoFrame[] */
    private function parseEntryFrames(int $entryOffset, int $entryLength, EntryTypeEnum $entryType): array
    {
        if ($entryLength <= 0) {
            return [];
        }

        if ($entryType === EntryTypeEnum::PLAYBACK) {
            return $this->findLastFrameInSegment($entryOffset, $entryLength);
        }

        $frames = [];
        $pos = $entryOffset;
        $end = $entryOffset + $entryLength;

        while ($pos + 9 <= $end) {
            if (fseek($this->handle, $pos) === -1) {
                break;
            }

            $typeByte = fread($this->handle, 1);
            if ($typeByte === false || $typeByte === '') {
                break;
            }
            $type = unpack('C', $typeByte)[1];

            $timeData = fread($this->handle, self::SIZE_FLOAT);
            if ($timeData === false || strlen($timeData) < self::SIZE_FLOAT) {
                break;
            }
            $time = unpack('f', $timeData)[1];

            $frameData = fread($this->handle, self::SIZE_INT);
            if ($frameData === false || strlen($frameData) < self::SIZE_INT) {
                break;
            }
            $frame = unpack('i', $frameData)[1];

            $payloadLength = $this->skipMacroPayload($type, $pos + 9, $end);

            $macroType = MacroTypeEnum::tryFrom($type);
            $frames[] = new DemoFrame(
                $macroType ?? MacroTypeEnum::UNUSED,
                $time,
                $frame,
                $payloadLength,
            );

            if ($type === 5) {
                break;
            }

            $pos += 9 + $payloadLength;
        }

        return $frames;
    }

    /** @return DemoFrame[] */
    private function findLastFrameInSegment(int $entryOffset, int $entryLength): array
    {
        $end = $entryOffset + $entryLength - self::MACRO_HEADER_SIZE;
        $scanStart = $entryOffset + max(0, $entryLength - self::LAST_FRAME_SCAN_RANGE);

        for ($pos = $end; $pos >= $scanStart; $pos--) {
            if (fseek($this->handle, $pos) === -1) {
                break;
            }

            $typeByte = fread($this->handle, 1);
            if ($typeByte === false || $typeByte === '') {
                break;
            }
            $type = unpack('C', $typeByte)[1];

            if ($type !== MacroTypeEnum::LAST_IN_SEGMENT->value) {
                continue;
            }

            $timeData = fread($this->handle, self::SIZE_FLOAT);
            if ($timeData === false || strlen($timeData) < self::SIZE_FLOAT) {
                continue;
            }
            $time = unpack('f', $timeData)[1];

            $frameData = fread($this->handle, self::SIZE_INT);
            if ($frameData === false || strlen($frameData) < self::SIZE_INT) {
                continue;
            }
            $frame = unpack('i', $frameData)[1];

            if ($time < 0 || $time > self::MAX_VALID_TIME || $frame < 0 || $frame > self::MAX_VALID_FRAME) {
                continue;
            }

            return [new DemoFrame(MacroTypeEnum::LAST_IN_SEGMENT, $time, $frame, 0)];
        }

        return [];
    }

    private function skipMacroPayload(int $type, int $payloadPos, int $end): int
    {
        switch ($type) {
            case MacroTypeEnum::GAME_DATA_START->value:
            case MacroTypeEnum::GAME_DATA_NORMAL->value:
                $extinfoSize = ($this->netProtocol === 42)
                    ? self::EXTINFO_SIZE_NET42
                    : self::EXTINFO_SIZE_NET45;
                $dataPos = $payloadPos + $extinfoSize;

                if ($dataPos + self::SIZE_INT > $end) {
                    return 0;
                }

                if (fseek($this->handle, $dataPos) === -1) {
                    return 0;
                }

                $chunkLenData = fread($this->handle, self::SIZE_INT);
                if ($chunkLenData === false || strlen($chunkLenData) < self::SIZE_INT) {
                    return 0;
                }

                $chunkLength = unpack('i', $chunkLenData)[1];
                if ($chunkLength < 0) {
                    return 0;
                }

                return $extinfoSize + self::SIZE_INT + $chunkLength;

            case MacroTypeEnum::UNUSED->value:
                return 0;

            case MacroTypeEnum::CLIENT_COMMAND->value:
            case MacroTypeEnum::STRING->value:
                return $this->readStringLength($payloadPos, $end);

            case MacroTypeEnum::LAST_IN_SEGMENT->value:
                return 0;

            case MacroTypeEnum::UNKNOWN_6->value:
                return self::MACRO6_PAYLOAD_SIZE;

            case MacroTypeEnum::UNKNOWN_7->value:
                return self::MACRO7_PAYLOAD_SIZE;

            case MacroTypeEnum::PLAY_SOUND->value:
                return $this->readSoundPayloadLength($payloadPos, $end);

            case MacroTypeEnum::DELTA_DATA->value:
                return $this->readDeltaPayloadLength($payloadPos, $end);

            default:
                return 0;
        }
    }

    private function readStringLength(int $pos, int $end): int
    {
        if (fseek($this->handle, $pos) === -1) {
            return 0;
        }

        $max = min($end - $pos, self::MAX_STRING_SCAN);
        $data = fread($this->handle, $max);
        if ($data === false) {
            return 0;
        }

        $nullPos = strpos($data, "\0");
        if ($nullPos === false) {
            return strlen($data);
        }

        return $nullPos + 1;
    }

    private function readSoundPayloadLength(int $pos, int $end): int
    {
        if ($pos + self::SOUND_PREFIX_SIZE > $end) {
            return 0;
        }

        if (fseek($this->handle, $pos + self::SIZE_INT) === -1) {
            return 0;
        }

        $nameLenData = fread($this->handle, self::SIZE_INT);
        if ($nameLenData === false || strlen($nameLenData) < self::SIZE_INT) {
            return 0;
        }

        $nameLength = unpack('i', $nameLenData)[1];
        if ($nameLength < 0 || $nameLength > 256) {
            return 0;
        }

        return self::SOUND_PREFIX_SIZE + $nameLength + self::SOUND_SUFFIX_SIZE;
    }

    private function readDeltaPayloadLength(int $pos, int $end): int
    {
        if ($pos + 4 > $end) {
            return 0;
        }

        if (fseek($this->handle, $pos) === -1) {
            return 0;
        }

        $lenData = fread($this->handle, self::SIZE_INT);
        if ($lenData === false || strlen($lenData) < self::SIZE_INT) {
            return 0;
        }

        $chunkLength = unpack('i', $lenData)[1];
        if ($chunkLength < 0) {
            return 0;
        }

        return self::SIZE_INT + $chunkLength;
    }

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
        $total = 0.0;
        $found = false;

        foreach ($this->getEntries() as $entry) {
            $entryTime = 0.0;
            $fromFrame = false;

            foreach ($entry->getParsedFrames() as $frame) {
                if ($frame->getType() === MacroTypeEnum::LAST_IN_SEGMENT) {
                    $entryTime = $frame->getTime();
                    $fromFrame = true;
                }
            }

            if (!$fromFrame) {
                $entryTime = $entry->getTrackTime();
            }

            if ($entryTime > 0) {
                $total += $entryTime;
                $found = true;
            }
        }

        return $found ? intval($total) : false;
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
