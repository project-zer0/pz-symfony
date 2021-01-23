# Project Zer0 Symfony Module

A pz module for PHP Symfony Framework.

## Install

Via composer:

```shell
$ composer require --dev project-zer0/pz-symfony
```

## Configuration

This module provides following config block to `.pz.yaml` file

```yaml
project-zer0:
    symfony:
        symfony_console_path: "$PZ_PWD/bin/console" # Defines a path to Symfony bin/console
        docker_compose:
            service_name: sf_console # Defines a docker-compose service to execute bin/console in
```

## Commands

This module provides these commands in `pz` tool

```shell
$ pz symfony:console         [c|console|sf] Runs Symfony Console inside app container
```

## Testing

Run test cases

```bash
$ composer test
```

Run test cases with coverage (HTML format)

```bash
$ composer test-coverage
```

Run PHP style checker

```bash
$ composer cs-check
```

Run PHP style fixer

```bash
$ composer cs-fix
```

Run all continuous integration tests

```bash
$ composer ci-run
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## License

Please see [License File](LICENSE) for more information.
