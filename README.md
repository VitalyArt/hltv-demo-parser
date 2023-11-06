### HLTV Demo Parser

![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/vitalyart/hltv-demo-parser/php?style=flat-square) 
![Packagist Version](https://img.shields.io/packagist/v/vitalyart/hltv-demo-parser?style=flat-square)
![GitHub repo size](https://img.shields.io/github/repo-size/vitalyart/hltv-demo-parser?style=flat-square)
![GitHub Workflow Status](https://github.com/VitalyArt/hltv-demo-parser/workflows/PHPUnit/badge.svg?branch=master)

This package is designed to obtain information from the demo of the servers or games on the Half-Life 1 engine.
Installation is possible in two versions:
1. Install with composer, either run

```
$ php composer.phar require vitalyart/hltv-demo-parser "*"
```

or add

```
"vitalyart/hltv-demo-parser": "*"
```

to the ```require``` section of your `composer.json` file.

```php
$parser = new \VitalyArt\DemoParser\Parser();
$parser->setDemoFile('/path/to/demo/pub-1609152130-de_dust2_2x2.dem');

$demo = $parser->getDemo();

$demo->getDemoProtocol();
$demo->getNetProtocol();
$demo->getMapName();
$demo->getClientName();
$demo->getStartTime();
$demo->getEndTime();
$demo->getDuration();

foreach($demo->getEntries() as $entry) {
    $entry->getTypeString();
    $entry->getType();
    $entry->getDescription();
    $entry->getFlags();
    $entry->getCDTrack();
    $entry->getTrackTime();
    $entry->getFrames();
    $entry->getOffset();
    $entry->getFileLength();
}
```
