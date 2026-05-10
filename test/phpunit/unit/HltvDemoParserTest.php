<?php

declare(strict_types=1);

namespace phpunit\unit;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use VitalyArt\DemoParser\Demo;
use VitalyArt\DemoParser\Enums\EntryTypeEnum;
use VitalyArt\DemoParser\Entry;
use VitalyArt\DemoParser\Exceptions\FileNotExistsException;
use VitalyArt\DemoParser\Exceptions\FileNotSpecifiedException;
use VitalyArt\DemoParser\Exceptions\IsNotADemoException;
use VitalyArt\DemoParser\Exceptions\WrongExtensionException;
use VitalyArt\DemoParser\Parser;

class HltvDemoParserTest extends TestCase
{
    /** @var Parser */
    private $parser;

    public function setUp(): void
    {
        $this->parser = new Parser();
    }

    public function testNoDemoFileSpecified(): void
    {
        $this->expectException(FileNotSpecifiedException::class);

        $this->parser->getDemo();
    }

    public function testDemoFileNotExists(): void
    {
        $this->expectException(FileNotExistsException::class);

        $this->parser->setDemoFile('/invalid/path');
        $this->parser->getDemo();
    }

    public function testDemoFileIsNotDemo(): void
    {
        $this->expectException(IsNotADemoException::class);

        $this->parser->setDemoFile(__DIR__ . '/demos/no-demo-file.dem');
        $this->parser->getDemo();
    }

    public function testDemoFileWrongExtension(): void
    {
        $this->expectException(WrongExtensionException::class);
        $this->parser->setDemoFile(__DIR__ . '/demos/demo-file-with-wrong-extension.txt');
        $this->parser->getDemo();
    }

    public function testDemoInstanceofDemo(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertInstanceOf(Demo::class, $demo);
    }

    public function testStarttimeInstanceofDatetime(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertInstanceOf(DateTimeImmutable::class, $demo->getStartTime());
    }

    public function testEntriesCount(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertEquals(count($demo->getEntries()), 2);
    }

    public function testEntriesInstanceofEntry(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        foreach ($demo->getEntries() as $entry) {
            $this->assertInstanceOf(Entry::class, $entry);
        }
    }

    public function testGetDuration(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $duration = $demo->getDuration();
        $this->assertIsInt($duration);
        $this->assertGreaterThan(0, $duration);
    }

    public function testGetMapName(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $mapName = $demo->getMapName();
        $this->assertIsString($mapName);
        $this->assertNotEmpty($mapName);
    }

    public function testGetClientName(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $clientName = $demo->getClientName();
        $this->assertIsString($clientName);
    }

    public function testGetStartTime(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertInstanceOf(DateTimeImmutable::class, $demo->getStartTime());
    }

    public function testGetEndTime(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertInstanceOf(DateTimeImmutable::class, $demo->getEndTime());
    }

    public function testGetEntriesContainsPlaybackAndLoading(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $entries = $demo->getEntries();
        $this->assertArrayHasKey('loading', $entries);
        $this->assertArrayHasKey('playback', $entries);
    }

    public function testEntryTypesAreCorrectEnum(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $entries = $demo->getEntries();

        $this->assertSame(EntryTypeEnum::LOADING, $entries['loading']->getTypeString());
        $this->assertSame(EntryTypeEnum::PLAYBACK, $entries['playback']->getTypeString());
    }

    public function testEntryHasExpectedStructure(): void
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();

        foreach ($demo->getEntries() as $entry) {
            $this->assertInstanceOf(EntryTypeEnum::class, $entry->getTypeString());
            $this->assertIsInt($entry->getType());
            $this->assertIsString($entry->getDescription());
            $this->assertIsInt($entry->getFlags());
            $this->assertIsInt($entry->getCDTrack());
            $this->assertIsFloat($entry->getTrackTime());
            $this->assertIsInt($entry->getFrames());
            $this->assertIsInt($entry->getOffset());
            $this->assertIsInt($entry->getFileLength());
        }
    }

    public function testCorruptedDemoReturnsDemoInstance(): void
    {
        $this->parser->setDemoFile(__DIR__ . '/demos/corrupted-demo.dem');
        $demo = $this->parser->getDemo();
        $this->assertInstanceOf(Demo::class, $demo);
        $this->assertIsArray($demo->getEntries());
        $this->assertCount(0, $demo->getEntries());
    }

    protected function setValidDemo(): void
    {
        $this->parser->setDemoFile(__DIR__ . '/demos/navi-vs-fx-iem5eu-third-1101231211-de_dust2.dem');
    }
}
