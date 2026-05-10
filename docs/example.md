## Basic usage

```php
$parser = new \VitalyArt\DemoParser\Parser();
$parser->setDemoFile('/path/to/demo/pub-1609152130-de_dust2_2x2.dem');

$demo = $parser->getDemo();

echo $demo->getDemoProtocol();  // 3
echo $demo->getNetProtocol();   // 48
echo $demo->getMapName();       // de_dust2
echo $demo->getClientName();    // Half-Life

$startTime = $demo->getStartTime(); // DateTimeImmutable or null
$endTime   = $demo->getEndTime();   // DateTimeImmutable or null

if ($startTime !== null) {
    echo $startTime->format('Y-m-d H:i:s');
}

$duration = $demo->getDuration(); // int or false

if ($duration !== false) {
    echo "Duration: {$duration}s";
}

foreach ($demo->getEntries() as $entry) {
    $type = $entry->getTypeString(); // EntryTypeEnum::LOADING or PLAYBACK
    echo $entry->getDescription();
    echo $entry->getFrames();
}
```

## Error handling

```php
$parser = new \VitalyArt\DemoParser\Parser();

try {
    $parser->setDemoFile('/path/to/demo.dem');
    $demo = $parser->getDemo();
} catch (\VitalyArt\DemoParser\Exceptions\ParserException $e) {
    echo 'Error: ' . $e->getMessage();
}
```

See the [Error Handling](errors.html) page for detailed exception handling.