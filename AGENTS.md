# AGENTS.md — hltv-demo-parser

## Project

PHP 8.2+ library that parses GoldSrc/HLTV `.dem` files (Counter-Strike 1.6 etc.).
PSR-4: `VitalyArt\DemoParser\` → `src/`.  
Entrypoint: `VitalyArt\DemoParser\Parser` (`src/Parser.php`).

## Commands

```sh
./vendor/bin/phpunit                                  # run all tests
./vendor/bin/phpunit --filter=testMethodName          # single test
./vendor/bin/phpstan analyse                          # static analysis (level 6)
make run-php82-tests                                  # Docker-based test on PHP 8.2
npm run docs:dev                                      # VuePress dev server
npm run docs:build                                    # build static docs site
```

## Key facts

- PHPUnit 9.x, config at `test/phpunit/phpunit.xml` (boots `vendor/autoload.php`)
- PHPStan 2.x level 6, only analyses `src/` (config: `phpstan.neon`)
- Runtime requirement: `ext-mbstring`
- All parser exceptions extend `VitalyArt\DemoParser\Exceptions\ParserException` — catch that for any parse error
- Binary parser: reads fixed-offset fields from `.dem` files; start time extracted from filename pattern `*-YYMMDDHHmm-*`
- `Demo::getMapCrc()`, `Demo::getGameDirectory()` — header fields at offsets 536 and 276
- `Entry::getParsedFrames(): DemoFrame[]` — macro block headers (type/time/frame) parsed from LOADING entry data segment. PLAYBACK frame parsing scans end of segment for LAST_IN_SEGMENT frame
- `Demo::getDuration()` sums `trackTime` across all entries, prefers LAST_IN_SEGMENT frame time
- `MacroTypeEnum` — 10 macro types (GAME_DATA_START=0, GAME_DATA_NORMAL=1, LAST_IN_SEGMENT=5, etc.)
- `SIZE_ENTRY = 92` (not 96); entry table starts 4 bytes after directory_offset
- CI runs `phpunit` then `phpstan analyse` on every push
- No Composer scripts — use `vendor/bin/` directly
