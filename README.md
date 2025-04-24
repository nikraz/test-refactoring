# Commission Calculator CLI App (PHP 8.4 + Docker + PHPUnit)

This CLI application calculates transaction commissions based on card BIN, transaction currency, and origin (EU or non-EU).  
Built with **PHP 8.4**, **Guzzle**, **Symfony Console**, **Docker**, and **PHPUnit** — following **clean architecture** principles.

---

## Quick Start

### Prerequisites
- [Docker](https://www.docker.com/) installed
- PHP version >= 7.4
- GNU Make (optional, for convenience)
- Copy .env.example to .env
- Run `php composer.phar i`

---

## Run the App (with Docker)

```bash
docker compose build
docker compose run --rm app bin/console commission:calculate input.txt
```
or
```
make build   # Build Docker container
make run     # Run the CLI app
```

---

## Input Format (input.txt)

Each line is a JSON object:

```json
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
```

---

## Output

Each line in output is the commission for that transaction, in EUR:

```
1.00
0.47
1.66
2.41
43.72
```

---

## Run Tests

```bash
docker compose run --rm app vendor/bin/phpunit
```

or
```
make test
```

All tests are isolated, deterministic, and use mocked external services.

---

## ⚙️ Configuration

Configure your `.env` file:

```env
BIN_API_URL=https://lookup.binlist.net
EXCHANGE_API_URL=https://api.apilayer.com/exchangerates_data/latest
EXCHANGE_API_KEY=9hUZhgz8YBZJhMUAgmOnOT6H06Noljd1
USE_MOCK_BIN_PROVIDER=true
```

---

## Architecture Overview

### `src/`
| File/Folder | Responsibility |
|-------------|----------------|
| `Command/CalculateCommissionCommand.php` | CLI entry point using Symfony Console |
| `DTO/Transaction.php` | Simple data holder for parsed transaction |
| `Contract/BinProviderInterface.php` | Interface for resolving BIN to country |
| `Contract/CurrencyRateProviderInterface.php` | Interface for resolving currency exchange rate |
| `Provider/BinListProvider.php` | Real BIN API implementation using Guzzle |
| `Provider/ExchangeRatesProvider.php` | Real exchange rate provider with API key auth |
| `Provider/MockBinProvider.php` | In-memory mock BIN mapping for tests/dev |
| `Parser/TransactionParser.php` | Parses line-by-line JSON transactions |
| `Service/CommissionCalculator.php` | Core logic: conversion + commission rules |
| `Enum/EuCountry.php` | List of all EU countries as PHP Enum |

---

## Testing Details

All core components are covered with PHPUnit tests:

### Tested:
- `CommissionCalculator` (core logic, edge cases)
- `BinListProvider` (mocked Guzzle responses)
- `ExchangeRatesProvider` (mocked Guzzle with API key)
- `TransactionParser` (valid/malformed input, edge handling)

Run with:

```bash
docker compose run --rm app vendor/bin/phpunit --display-deprecations
```

---

## Extendibility

This project is **open for extension, closed for modification**:
- Add new providers without changing existing logic
- Use `.env` to toggle mocks vs real APIs
- Swap rate or BIN APIs with new format, auth, headers

---

## Future Ideas
- Add caching for rates
- Add retry/backoff logic for API calls
- Generate reports (total commission, per-country summary)
- Run on a schedule (cron/docker)

---

## Clean Code Principles Followed
- Interface-driven design
- Single Responsibility Principle (SRP)
- Dependency Inversion (DI-ready)
- Testability-first
- Environment-driven configuration
- Dockerized, zero local setup

---

## License

MIT — use freely, contribute, or fork!