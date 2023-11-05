<?php

namespace phpunit\unit;

use DateTime;
use PHPUnit\Framework\TestCase;
use VitalyArt\DemoParser\Demo;
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
        $this->assertInstanceOf(DateTime::class, $demo->getStartTime());
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

    protected function setValidDemo(): void
    {
        $this->parser->setDemoFile(__DIR__ . '/demos/navi-vs-fx-iem5eu-third-1101231211-de_dust2.dem');
    }
}
