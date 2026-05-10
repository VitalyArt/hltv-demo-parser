## Error Handling

The parser throws exceptions when something goes wrong. All exceptions extend `\VitalyArt\DemoParser\Exceptions\ParserException`.

### Exception hierarchy

```
\Exception
└── ParserException
    ├── FileNotSpecifiedException
    ├── FileNotExistsException
    ├── WrongExtensionException
    ├── NotReadableException
    └── IsNotADemoException
```

### Basic error handling

```php
$parser = new \VitalyArt\DemoParser\Parser();

try {
    $parser->setDemoFile('/path/to/demo.dem');
    $demo = $parser->getDemo();
} catch (\VitalyArt\DemoParser\Exceptions\FileNotExistsException $e) {
    echo 'Demo file not found: ' . $e->getMessage();
} catch (\VitalyArt\DemoParser\Exceptions\WrongExtensionException $e) {
    echo 'Wrong file extension (must be .dem): ' . $e->getMessage();
} catch (\VitalyArt\DemoParser\Exceptions\IsNotADemoException $e) {
    echo 'File is not a valid demo: ' . $e->getMessage();
} catch (\VitalyArt\DemoParser\Exceptions\NotReadableException $e) {
    echo 'Cannot read demo file: ' . $e->getMessage();
} catch (\VitalyArt\DemoParser\Exceptions\ParserException $e) {
    echo 'Parser error: ' . $e->getMessage();
}
```

### Catching all parser errors

If you don't need granular control, catch the base exception:

```php
try {
    $parser->setDemoFile('/path/to/demo.dem');
    $demo = $parser->getDemo();
} catch (\VitalyArt\DemoParser\Exceptions\ParserException $e) {
    echo 'Failed to parse demo: ' . $e->getMessage();
}
```

### Common pitfalls

| Scenario | Exception |
|---|---|
| Calling `getDemo()` without `setDemoFile()` | `FileNotSpecifiedException` |
| File path does not exist | `FileNotExistsException` |
| File extension is not `.dem` | `WrongExtensionException` |
| File exists but cannot be read (permissions) | `NotReadableException` |
| File is not a valid GoldSrc demo (no `HLDEMO` header) | `IsNotADemoException` |
