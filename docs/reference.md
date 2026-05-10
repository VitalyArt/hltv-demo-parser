## Class `\VitalyArt\DemoParser\Parser::class`

| Method                              | Return type               |
|-------------------------------------|---------------------------|
| $parser->setDemoFile(string\$file); | void                      |
| $parser->getDemo();                 | \VitalyArt\DemoParser\Demo |

## Class `\VitalyArt\DemoParser\Demo::class`

| Method                    | Return type                     |
|---------------------------|---------------------------------|
| $demo->getDemoProtocol(); | int                             |
| $demo->getNetProtocol();  | int                             |
| $demo->getMapName();      | string                          |
| $demo->getClientName();   | string                          |
| $demo->getStartTime();    | DateTimeImmutable\|null         |
| $demo->getEndTime();      | DateTimeImmutable\|null         |
| $demo->getDuration();     | int\|false                      |

> `getStartTime()` returns `null` when the demo filename does not contain a date pattern (`-ymdHi-`).  
> `getEndTime()` returns `null` when `getStartTime()` is `null`.  
> `getDuration()` returns `false` when no playback entry is found in the demo.

## Class `\VitalyArt\DemoParser\Entry::class`

| Method                   | Return type                                     |
|--------------------------|-------------------------------------------------|
| $entry->getTypeString()  | \VitalyArt\DemoParser\Enums\EntryTypeEnum       |
| $entry->getType()        | int                                             |
| $entry->getDescription() | string                                          |
| $entry->getFlags()       | int                                             |
| $entry->getCDTrack()     | int                                             |
| $entry->getTrackTime()   | float                                           |
| $entry->getFrames()      | int                                             |
| $entry->getOffset()      | int                                             |
| $entry->getFileLength()  | int                                             |

## Enum `\VitalyArt\DemoParser\Enums\EntryTypeEnum`

A backed string enum representing the type of demo entry.

| Case        | Value      | Description                                      |
|-------------|------------|--------------------------------------------------|
| LOADING     | `'loading'`  | Loading segment — map is being loaded             |
| PLAYBACK    | `'playback'` | Playback segment — the actual recorded gameplay   |

## Exceptions

All exceptions live in `VitalyArt\DemoParser\Exceptions` and extend the base `ParserException`.

| Exception                | Thrown when                                         |
|--------------------------|-----------------------------------------------------|
| ParserException          | Base exception for all parser errors                |
| FileNotSpecifiedException| `getDemo()` was called without `setDemoFile()`      |
| FileNotExistsException   | The specified demo file does not exist              |
| WrongExtensionException  | The file does not have a `.dem` extension           |
| NotReadableException     | The file exists but cannot be opened for reading    |
| IsNotADemoException      | The file does not start with the `HLDEMO` magic bytes |
