### HLTV Demo Parser

This package is designed to obtain information from the demo of the servers or games on the Half-Life 1 engine.
Installation is possible in two versions:
1. Composer
To install with composer, either run

```
$ php composer.phar require vitalyart/hltv-demo-parser "*"
```

or add

```
"vitalyart/hltv-demo-parser": "*"
```

to the ```require``` section of your `composer.json` file.

2. Manual installation (Without composer)

Clone or download package from github and include bootstrap file

```
include '/path/to/hltv-demo-parser/src/bootstrap.php';
```

```php
$parser = new \VitalyArt\DemoParser();
$parser->setDemo('/path/to/demo/pub-1609152130-de_dust2_2x2.dem');

$demo = $parser->getDemo();

$demo->getDemoProtocol();
$demo->getNetProtocol();
$demo->getMapName();
$demo->getClientName();
$demo->getStartTime();
$demo->getEndTime();

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
