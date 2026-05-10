## Contributing

### Setup

Clone the repository and install dependencies:

```shell
git clone https://github.com/VitalyArt/hltv-demo-parser.git
cd hltv-demo-parser
composer install
```

### Running Tests

Run PHPUnit tests:

```shell
./vendor/bin/phpunit --testdox --bootstrap vendor/autoload.php test/phpunit
```

Tests are located in `test/phpunit/unit/`. Make sure all tests pass before submitting changes.

### Static Analysis

Run PHPStan (level 6):

```shell
./vendor/bin/phpstan analyse
```

PHPStan configuration is in `phpstan.neon`.

### Building Documentation

The documentation uses VuePress. To preview locally:

```shell
npm install
npm run docs:dev
```

To build the static site:

```shell
npm run docs:build
```

The output is placed in `docs/.vuepress/dist/`.

### Pull Request Checklist

- [ ] All tests pass (`./vendor/bin/phpunit --testdox --bootstrap vendor/autoload.php test/phpunit`)
- [ ] PHPStan reports no errors (`./vendor/bin/phpstan analyse`)
- [ ] Documentation is updated if the public API changes
- [ ] Changes follow the existing code style
