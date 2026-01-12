# Doctrine 1

[![CI](https://github.com/cgservices/doctrine1/actions/workflows/ci.yml/badge.svg)](https://github.com/cgservices/doctrine1/actions/workflows/ci.yml)

Doctrine 1 is a PHP ORM (Object Relational Mapper) for PHP 8.4+.

## Requirements

- PHP 8.4 or higher
- PDO extension
- One of: pdo_sqlite, pdo_mysql, pdo_pgsql

## Installation

Install via Composer:

```bash
composer require cgservices/doctrine1
```

## Development

### Running Tests Locally with Docker

Since PHP 8.4 may not be installed locally, you can use Docker to run tests:

```bash
# Build the Docker image
make build

# Run all tests
make test

# Run only smoke tests
make test-smoke

# Run only core tests
make test-core

# Run tests matching a specific filter
make test-filter F=Connection

# Open a shell inside the Docker container
make shell

# Check PHP syntax
make lint

# Clean up
make clean
```

### Running Tests with Local PHP

If you have PHP 8.4 installed locally:

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run specific test suite
./vendor/bin/phpunit --testsuite "Smoke Tests"
./vendor/bin/phpunit --testsuite "Core Tests"
./vendor/bin/phpunit --testsuite "Tickets"
```

## Available Make Commands

| Command | Description |
|---------|-------------|
| `make build` | Build the Docker image |
| `make test` | Run all tests inside Docker container |
| `make test-smoke` | Run smoke tests only |
| `make test-core` | Run core tests only |
| `make test-tickets` | Run ticket tests only |
| `make test-filter F=<filter>` | Run tests matching a filter |
| `make shell` | Open a shell inside the Docker container |
| `make composer-install` | Install Composer dependencies |
| `make composer-update` | Update Composer dependencies |
| `make lint` | Check PHP syntax errors |
| `make clean` | Remove Docker image and vendor directory |

## License

This software is licensed under the LGPL. For more information, see the [LICENSE](LICENSE) file.

