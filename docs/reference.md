## Class `\VitalyArt\DemoParser\Demo::class`

| Method                    | Return type       |
|---------------------------|-------------------|
| $demo->getDemoProtocol(); | int               |
| $demo->getNetProtocol();  | int               |
| $demo->getMapName();      | string            |
| $demo->getClientName();   | string            |
| $demo->getStartTime();    | DateTimeImmutable |
| $demo->getEndTime();      | DateTimeImmutable |
| $demo->getDuration();     | int               |

## Class `\VitalyArt\DemoParser\Entry::class`

| Method                   | Return type                                     |
|--------------------------|-------------------------------------------------|
| $entry->getTypeString()  | enum(\VitalyArt\DemoParser\Enums\EntryTypeEnum) |
| $entry->getType()        | int                                             |
| $entry->getDescription() | string                                          |
| $entry->getFlags()       | int                                             |
| $entry->getCDTrack()     | int                                             |
| $entry->getTrackTime()   | float                                           |
| $entry->getFrames()      | int                                             |
| $entry->getOffset()      | int                                             |
| $entry->getFileLength()  | int                                             |
