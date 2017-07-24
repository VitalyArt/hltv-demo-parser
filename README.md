### HLTV Demo Parser

```php
$demo = new \VitalyArt\DemoParser(
    '/path/to/demo/pub-1609152130-de_dust2_2x2.dem'
);

print_r($demo->getData());
```


Result:
```
Array
(
    [demoProtocol] => 5
    [netProtocol] => 48
    [mapName] => de_dust2_2x2
    [clientName] => cstrike
    [entries] => Array
        (
            [loading] => Array
                (
                    [nEntryType] => 2
                    [szDescription] => LOADING
                    [nFlags] => 0
                    [nCDTrack] => 0
                    [fTrackTime] => 0
                    [nFrames] => 0
                    [nOffset] => 0
                    [nFileLength] => 544
                )
            [playback] => Array
                (
                    [nEntryType] => 1
                    [szDescription] => Playback
                    [nFlags] => 0
                    [nCDTrack] => 0
                    [fTrackTime] => 1800.4250488281
                    [nFrames] => 57038
                    [nOffset] => 33542
                    [nFileLength] => 38968795
                )
        )
    [startTime] => DateTime Object
        (
            [date] => 2016-09-15 21:30:00.000000
            [timezone_type] => 3
            [timezone] => Europe/Moscow
        )
    [endTime] => DateTime Object
        (
            [date] => 2016-09-15 22:00:00.000000
            [timezone_type] => 3
            [timezone] => Europe/Moscow
        )
)

```