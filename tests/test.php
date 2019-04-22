<?php

use VitalyArt\DemoParser\Demo;
use VitalyArt\DemoParser\Entry;
use VitalyArt\DemoParser\exceptions\FileNotSpecifiedException;
use VitalyArt\DemoParser\exceptions\WrongExtensionException;
use VitalyArt\DemoParser\Parser;

class HltvDemoParserTest extends PHPUnit_Framework_TestCase
{
    /** @var Parser */
    private $parser;

    public function setUp(): void
    {
        $this->parser = new Parser();
    }

    /**
     * @expectedException FileNotSpecifiedException
     */
    public function testNoDemoFileSpecified(): void
    {
        $this->parser->getDemo();
    }

    /**
     * @expectedException VitalyArt\DemoParser\exceptions\FileNotExistsException
     */
    public function testDemoFileNotExists(): void
    {
        $this->parser->setDemoFile('/invalid/path');
        $this->parser->getDemo();
    }

    /**
     * @expectedException VitalyArt\DemoParser\exceptions\IsNotADemoException
     */
    public function testDemoFileIsNotDemo(): void
    {
        $this->parser->setDemoFile(__DIR__ . '/demos/no-demo-file.dem');
        $this->parser->getDemo();
    }

    /**
     * @expectedException WrongExtensionException
     */
    public function testDemoFileWrongExtension(): void
    {
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
