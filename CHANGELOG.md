# v3.1.0
- `Demo::getDuration()` now derives from the `LAST_IN_SEGMENT` DemoFrame timestamp in the PLAYBACK data segment (falls back to entry `trackTime`)
- New `Demo::getMapCrc()` — returns the map CRC from the demo header
- New `Demo::getGameDirectory()` — returns the game directory (e.g. `cstrike`)
- New `Entry::getParsedFrames(): DemoFrame[]` — parsed macro block headers:
  - LOADING entries: all sequential macro blocks
  - PLAYBACK entries: LAST_IN_SEGMENT frame found by scanning the end of the segment
- New `DemoFrame` class — represents a single macro block (type, time, frame, payload length)
- New `MacroTypeEnum` — 10 macro types (0–9) used in GoldSrc demo files
- Fixed entry table parsing: corrected `SIZE_ENTRY` to 92 bytes; entries now start properly after the count field

# v3.0.2
- Refactor Parser: add constants, fix type safety, improve tests

# v3.0.1
- Fixed the return type in `Entry::getCDTrack()`
- New documentation on GitHub Pages
- Added the changelog to the documentation

# v3.0.0

### Compatibility-breaking changes:
- PHP version has been upgraded to 8.2.
- The namespace `exceptions` has been renamed to `Exceptions`
- The file `bootstrap.php` has been removed.
- The `getTypeString()` method of the `Entry` class now returns an `EntryTypeEnum`.
- `DateTime` has been changed to `DateTimeImmutable`.

### Non-compatibility-breaking changes:
- DTO classes are now readonly.
- All parser exceptions now inherit from the `ParserException` class.
- The Demo class has added a `getDuration()` method that returns the duration of the demo file in seconds.
- An `EntryTypeEnum` enum has been added.
