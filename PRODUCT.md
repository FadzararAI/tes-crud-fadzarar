# Product

## Platform
web

## Stack
Plain PHP 5.6, legacy mysql extension, MySQL 5.7, WAMP; approved Paket B environment deviation.

## Users and purpose
Warehouse operators maintain item descriptions and record incoming/outgoing movements. An examiner reviews the assignment requirements.

## Constraints
English interface; whole-unit quantities; rupiah prices; seeded categories; immutable transactions; no login. Stock begins at zero and changes only through transactions. Local delivery with SQL, README and three Git milestones. Public publication deferred.

## Success
Operators can find items, record a movement, and review date-filtered history without losing the stock audit trail. Negative stock and partial transaction writes must be prevented.

## Editing preference
Simple procedural PHP with explicit field assignments and shared, clearly named helpers. Tailwind CSS for UI; forms must be easy to extend.
