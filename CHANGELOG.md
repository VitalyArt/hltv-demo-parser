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
