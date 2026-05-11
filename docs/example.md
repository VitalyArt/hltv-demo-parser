## Basic usage

```php
$parser = new \VitalyArt\DemoParser\Parser();
$parser->setDemoFile('/path/to/demo/pub-1609152130-de_dust2_2x2.dem');

$demo = $parser->getDemo();

echo $demo->getDemoProtocol();  // 5
echo $demo->getNetProtocol();   // 48
echo $demo->getMapName();       // de_dust2
echo $demo->getClientName();    // cstrike
echo $demo->getGameDirectory(); // cstrike
echo $demo->getMapCrc();        // 0

$startTime = $demo->getStartTime(); // DateTimeImmutable or null
$endTime   = $demo->getEndTime();   // DateTimeImmutable or null

if ($startTime !== null) {
    echo $startTime->format('Y-m-d H:i:s');
}

$duration = $demo->getDuration(); // int or false (from LAST_IN_SEGMENT frame time)

if ($duration !== false) {
    echo "Duration: {$duration}s"; // e.g. 2891
}

foreach ($demo->getEntries() as $entry) {
    $type = $entry->getTypeString(); // EntryTypeEnum::LOADING or PLAYBACK
    echo $entry->getDescription();
    echo $entry->getFrames();

    foreach ($entry->getParsedFrames() as $frame) {
        printf("[%s] time=%.3f frame=%d\n",
            $frame->getType()->name,
            $frame->getTime(),
            $frame->getFrame(),
        );
    }
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