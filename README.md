# Stockroom — Paket B

An English warehouse inventory application using simple procedural PHP and Tailwind CSS. Includes item CRUD, assigned user display, seeded categories, inventory search, immutable stock movements, inclusive date filters and quantity recaps.

## Requirements and agreed environment

- PHP **5.6** with `mysqli`, `mbstring`, and `openssl`; 64-bit PHP is recommended for the unsigned stock limit. The app uses the real `mysql` extension when available, with a small `mysqli` compatibility layer for this WAMP installation.
- Apache and MySQL **5.5.62**, using InnoDB and utf8mb4.
- WAMP is the agreed replacement for the assignment's XAMPP 5.6. The equivalent assignment path is `htdocs/tes_fadzarar`; the requested WAMP path is `C:\wamp64\www\tes_fadzarar`.
- Plain PHP, no Composer or JavaScript package installation. Tailwind 4.1.18 browser compiler is bundled locally. See [Tailwind's browser compiler documentation](https://tailwindcss.com/docs/installation/play-cdn) for its local/development use.

PHP 7/8 cannot run the original legacy API directly: [PHP removed the legacy mysql extension in PHP 7](https://www.php.net/manual/en/mysql.installation.php). WAMP's PHP 5.6.40 installation did not include `php_mysql.dll`, so `koneksi.php` supplies the familiar `mysql_*` calls through `mysqli` when the real extension is missing. This fallback lets the dashboard run; `phpinfo()` will still show `mysqli`, not the real `mysql` extension.

## Setup

1. Place this entire folder in `C:\wamp64\www\tes_fadzarar`, including hidden `.git` if keeping commit history.
2. Start Apache and the WAMP MySQL 5.5.62 service. Enable `mysqli`, `mbstring`, and `openssl` for PHP 5.6.
3. Import `database.sql` using phpMyAdmin or the MySQL client. It creates `tes_fadzarar`, three tables, three categories, five sample items and six movements. Import **once into a fresh database**; it deliberately does not drop existing records.
4. Copy `config.example.php` to `config.local.php`. Set host/port, username, password and database. Use a local application account with SELECT, INSERT, UPDATE and DELETE on `tes_fadzarar` after importing the schema with an administrative account. Local credentials are Git-ignored.
5. Open `http://localhost/tes_fadzarar/info.php`. Confirm PHP 5.6 and the **mysql** section, then open `http://localhost/tes_fadzarar/`.

The current Git-ignored local config points to WAMP **MySQL 5.5.62 on port 3306**. The dashboard connection and imported `tes_fadzarar` database were confirmed on that server. PHP's real `mysql` extension is still absent from WAMP; the app currently reaches MySQL through its `mysqli` fallback. To demonstrate the actual extension in `info.php`, install a compatible `php_mysql.dll` for the PHP 5.6 build and enable it in the Apache PHP configuration.

## Use and stock rules

- Add an item with a unique SKU, name, category, unit, assigned user and rupiah unit price. Starting stock is always zero.
- Edit changes item descriptions and price, never stock. Delete asks for JavaScript confirmation and submits a CSRF-protected POST.
- Record incoming or outgoing quantities as positive whole numbers. Outgoing movements cannot exceed the current available balance.
- The item row is locked inside an InnoDB transaction; stock and the movement record commit together or roll back together.
- Movement dates describe business dates, so backdated entries are allowed. Availability is checked against current stock, not a reconstructed historical balance.
- Transactions cannot be edited/deleted. Items with history cannot be deleted. Use a new compensating movement to correct a mistake.
- Date-range endpoints are inclusive. Recaps count records and sum quantities across items/units; they are not monetary valuations.
- Categories are seeded master data. Category-management screens and authentication are outside the agreed scope.

## Change forms

Read [EDITING.md](EDITING.md) for the file map, a complete example of adding a storage-location field, Tailwind changes, and an examiner walkthrough. SQL operations live in `models/`, page markup lives in `views/`, and the required root PHP pages connect the two while handling validation.

## Verification performed

- All 13 application PHP files passed PHP 5.6 syntax checks.
- Fresh SQL import and earlier transaction checks succeeded on local MariaDB. The database was later imported into MySQL 5.5.62 and the dashboard connection was confirmed there.
- `tests/integration.mjs`: **26 passed** against official PHP 5.6 with real mysql extension and MariaDB. Tests cover CRUD, unique SKU, quoted/HTML values, forged stock, invalid quantities/dates, missing records, insufficient stock, date filters, deletion protection, CSRF and GET rejection.
- Injected a ledger INSERT failure with a temporary database trigger: the stock UPDATE rolled back. The trigger was removed afterward.
- Sent simultaneous dispatches through two PHP server processes with separate sessions: only one succeeded; the other was rejected for insufficient stock. Balance stayed nonnegative.
- Mechanical UI scan produced heuristic warnings concerning navigation underlines and colors across unrelated selectors. Browser visual/mobile/keyboard checks could **not** run because the browser host was unavailable; no visual sign-off is claimed.
- The folder was served by WAMP Apache and loaded its sample inventory after the MySQL 5.5 connection was configured. Browser visual/mobile/keyboard checks were unavailable.

To repeat HTTP checks with Node 18+ while WAMP Apache and MySQL are running:

```powershell
node tests/integration.mjs
```

The suite creates uniquely prefixed test items. One history-bearing test item remains by design when rerunning the suite; other disposable items are removed. The implementation run cleaned up its generated fixtures before exporting the five-item sample database. Run only against a local test database. Run `tests/invariants.sql` to check ledger/stock consistency.

## Submission

The repository contains incremental setup, CRUD, and transaction/documentation changes. `database.sql` contains the portable sample schema/data; `database-export.sql` is a snapshot exported from the earlier MariaDB database. Use `database.sql` for a fresh MySQL 5.5 import. `config.local.php` and downloaded runtimes are excluded.

This is a local legacy-stack assessment application. `info.php` is for examiner verification; remove it before any public hosting.
