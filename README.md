### HLTV Demo Parser

Парсит демо файл для получения информации

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
