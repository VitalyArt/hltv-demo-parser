<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class HltvDemoParserTest extends PHPUnit_Framework_TestCase
{
    private $parser;
    
    public function setUp()
    {
        $this->parser = new \VitalyArt\DemoParser\Parser;
    }
    
    /**
     * @expectedException \VitalyArt\DemoParser\exceptions\FileNotSpecifiedException
     */
    public function testNoDemoFileSpecified()
    {
        $this->parser->getDemo();
    }
    
    /**
     * @expectedException VitalyArt\DemoParser\exceptions\FileNotExistsException
     */
    public function testDemoFileNotExists()
    {
        $this->parser->setDemoFile('/invalid/path');
        $this->parser->getDemo();
    }
    
    /**
     * @expectedException VitalyArt\DemoParser\exceptions\IsNotADemoException
     */
    public function testDemoFileIsNotDemo()
    {
        $this->parser->setDemoFile(__DIR__ . '/demos/no-demo-file.dem');
        $this->parser->getDemo();
    }
    
    /**
     * @expectedException \VitalyArt\DemoParser\exceptions\WrongExtensionException
     */
    public function testDemoFileWrongExtension()
    {
        $this->parser->setDemoFile(__DIR__ . '/demos/demo-file-with-wrong-extension.txt');
        $this->parser->getDemo();
    }

    public function testDemoInstanceofDemo()
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertInstanceOf(\VitalyArt\DemoParser\Demo::class, $demo);
    }

    public function testStarttimeInstanceofDatetime()
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertInstanceOf(DateTime::class, $demo->getStartTime());
    }
    
    public function testEntriesCount()
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        $this->assertEquals(count($demo->getEntries()), 2);
    }
    
    public function testEntriesInstanceofEntry()
    {
        $this->setValidDemo();
        $demo = $this->parser->getDemo();
        foreach ($demo->getEntries() as $entry) {
            $this->assertInstanceOf(\VitalyArt\DemoParser\Entry::class, $entry);
        }
    }
    
    protected function setValidDemo()
    {
        $this->parser->setDemoFile(__DIR__ . '/demos/navi-vs-fx-iem5eu-third-1101231211-de_dust2.dem');
    }
}