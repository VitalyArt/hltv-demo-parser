## Class `\VitalyArt\DemoParser\Parser::class`

| Method                              | Return type               |
|-------------------------------------|---------------------------|
| $parser->setDemoFile(string\$file); | void                      |
| $parser->getDemo();                 | \VitalyArt\DemoParser\Demo |

## Class `\VitalyArt\DemoParser\Demo::class`

| Method                     | Return type                     |
|----------------------------|---------------------------------|
| $demo->getDemoProtocol();  | int                             |
| $demo->getNetProtocol();   | int                             |
| $demo->getMapName();       | string                          |
| $demo->getClientName();    | string                          |
| $demo->getMapCrc();        | int                             |
| $demo->getGameDirectory(); | string                          |
| $demo->getStartTime();     | DateTimeImmutable\|null         |
| $demo->getEndTime();       | DateTimeImmutable\|null         |
| $demo->getDuration();      | int\|false                      |
| $demo->getEntries();       | \VitalyArt\DemoParser\Entry[]   |

> `getStartTime()` returns `null` when the demo filename does not contain a date pattern (`-ymdHi-`).  
> `getEndTime()` returns `null` when `getStartTime()` is `null`.  
> `getDuration()` returns `false` when no entry with track time is found.  
> The method sums `getTrackTime()` from all entries, preferring `LAST_IN_SEGMENT` DemoFrame timestamps where available (parsed from entry data segments).  
> `getMapCrc()` returns the CRC-32 of the map file stored in the demo header (often 0 in HLTV demos).  
> `getGameDirectory()` returns the game directory (e.g. `cstrike`, `valve`, `gearbox`).

## Class `\VitalyArt\DemoParser\Entry::class`

| Method                     | Return type                                     |
|----------------------------|-------------------------------------------------|
| $entry->getTypeString()    | \VitalyArt\DemoParser\Enums\EntryTypeEnum       |
| $entry->getType()          | int                                             |
| $entry->getDescription()   | string                                          |
| $entry->getFlags()         | int                                             |
| $entry->getCDTrack()       | int                                             |
| $entry->getTrackTime()     | float                                           |
| $entry->getFrames()        | int                                             |
| $entry->getOffset()        | int                                             |
| $entry->getFileLength()    | int                                             |
| $entry->getParsedFrames()  | \VitalyArt\DemoParser\DemoFrame[]               |

> `getParsedFrames()` returns an array of parsed macro blocks from the entry's data segment.  
> For `LOADING` entries, all macro blocks are parsed sequentially.  
> For `PLAYBACK` entries, the method scans the end of the data segment for the  
> `LAST_IN_SEGMENT` frame (which contains the final playback timestamp).

## Class `\VitalyArt\DemoParser\DemoFrame::class`

Each demo frame represents a sequential macro block extracted from an entry's data segment.

| Method                     | Return type                            |
|----------------------------|----------------------------------------|
| $frame->getType()          | \VitalyArt\DemoParser\Enums\MacroTypeEnum |
| $frame->getTime()          | float                                  |
| $frame->getFrame()         | int                                    |
| $frame->getPayloadLength() | int                                    |

## Enum `\VitalyArt\DemoParser\Enums\EntryTypeEnum`

A backed string enum representing the type of demo entry.

| Case        | Value      | Description                                      |
|-------------|------------|--------------------------------------------------|
| LOADING     | `'loading'`  | Loading segment — map is being loaded             |
| PLAYBACK    | `'playback'` | Playback segment — the actual recorded gameplay   |

## Enum `\VitalyArt\DemoParser\Enums\MacroTypeEnum`

A backed integer enum representing the macro block type within an entry's data segment.

| Case             | Value | Description                                     |
|------------------|-------|-------------------------------------------------|
| GAME_DATA_START  | 0     | Game data with initial signon messages           |
| GAME_DATA_NORMAL | 1     | Game data with normal update messages            |
| UNUSED           | 2     | Tick marker (no payload)                         |
| CLIENT_COMMAND   | 3     | Client console command string                    |
| STRING           | 4     | Generic string data                              |
| LAST_IN_SEGMENT  | 5     | End of the current data segment                  |
| UNKNOWN_6        | 6     | Reserved                                         |
| UNKNOWN_7        | 7     | Reserved                                         |
| PLAY_SOUND       | 8     | Play a sound effect                              |
| DELTA_DATA       | 9     | Delta-compressed data                            |

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
