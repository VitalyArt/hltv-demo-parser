---
home: true
heroImage: /logo.svg
heroText: HLTV Demo Parser
tagline: A focused PHP parser for reading metadata, timing, and entry tables from GoldSrc demo files.
actionText: Get Started
actionLink: /install.html
features:
- title: Reads GoldSrc demos
  details: Supports .dem files from HLTV, Counter-Strike 1.6, Half-Life, Team Fortress Classic, Day of Defeat, and compatible mods.
- title: Extracts useful metadata
  details: Returns protocol versions, map name, map CRC, game directory, client name, start and end time, playback duration, parsed entries, and macro block frames.
- title: Small PHP API
  details: Built for PHP 8.2+ with PSR-4 autoloading, readonly DTOs, backed enums, typed exceptions, and only ext-mbstring required.
- title: Predictable failures
  details: Dedicated exceptions cover missing paths, wrong extensions, unreadable files, and invalid demo headers.
footer: GPL-3.0 Licensed | Copyright © VitalyArt
---

```php
$parser = new \VitalyArt\DemoParser\Parser();
$parser->setDemoFile('/path/to/demo/pub-1609152130-de_dust2_2x2.dem');

$demo = $parser->getDemo();

echo $demo->getMapName();
echo $demo->getDuration();
```

Use the sidebar to move through installation, examples, the public API, supported formats, and contribution notes.
