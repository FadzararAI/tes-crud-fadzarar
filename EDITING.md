# Changing forms without a framework

The application uses ordinary PHP variables, `if`, `foreach`, arrays and SQL.
There are no classes, routing framework, dependency injection, or ORM.

## Where to work

| File | Responsibility |
| --- | --- |
| Root PHP pages | Keep the current URLs and connect form processing to the right model and view |
| `models/Item.php` | Item reads, add/edit save, unique SKU lookup and protected deletion |
| `models/Transaction.php` | Movement list, item dropdown data and atomic stock movement save |
| `views/inventory/` | Inventory table and shared add/edit form markup |
| `views/transactions/` | Movement table and add movement form markup |
| `views/shared/` | Markup reused for common states such as a missing item |
| `tambah.php` / `edit.php` | Set add/edit mode and load the shared item page |
| `item-form.php` | Read item inputs, validate them and pass changes to the item model |
| `transaksi-tambah.php` | Read and validate movement inputs, then call the transaction model |
| `transaksi.php` | Check the date filter and calculate totals for the view |
| `hapus.php` | Check the request and ask the item model to delete safely |
| `koneksi.php` | Connection and small helpers around the required mysql functions |
| `bootstrap.php` | Escaping, input helpers, CSRF, messages and layout functions |
| `includes/header.php` / `footer.php` | Shared page layout and navigation |
| `assets/style.css` | Reusable Tailwind component classes |

Each page follows the same short path: **page file → model for data → view for HTML**. Models contain database work; views contain the page markup. The root pages preserve the assignment's required page names and URLs.

## Example: add a storage location to an item

1. Add `location VARCHAR(80) NOT NULL DEFAULT ''` to `items` in `database.sql` for future fresh imports. For an existing database, run `ALTER TABLE items ADD location VARCHAR(80) NOT NULL DEFAULT '';` once in phpMyAdmin.
2. Add `'location' => ''` to the starting `$item` array in `item-form.php`.
3. In the POST block, read `$item['location'] = input($_POST, 'location');`.
4. Validate the value before saving, for example check its length with `mb_strlen` and append an error to `$errors`.
5. Add `,location=` plus `sql_string($item['location'])` to the field list in `models/Item.php` inside `item_save()`. The item lookup already returns every item column.
6. Add this field inside the `.form-grid` in `views/inventory/form.php`:

```php
<div>
    <label for="location">Storage location</label>
    <input id="location" name="location" maxlength="80"
           value="<?php echo e($item['location']); ?>">
</div>
```

7. Add a header and `<?php echo e($item['location']); ?>` cell in `views/inventory/index.php`. Update the empty table row's colspan.
8. Test adding, editing, empty input, maximum length, and quotes in the new field.

`input()` reads one trimmed string, `sql_string()` escapes database text, and `e()` escapes HTML. They serve different purposes; do not substitute one for another. Cast or validate numeric identifiers before using them in SQL.

## Tailwind styling

Tailwind 4.1.18's browser compiler is bundled in `assets/vendor/tailwind-browser.js`. The local classroom application needs no npm install, build step, or internet connection. This browser compiler is intended for development/local demonstrations; a public production deployment should compile static CSS instead.

For an individual change, add utilities directly to HTML in the relevant file under `views/`:

```html
<div class="full md:max-w-xl">
    <label for="location" class="text-emerald-900">Storage location</label>
    <!-- input here -->
</div>
```

For a repeated change, update the named component in `assets/style.css`, where `@apply` groups Tailwind utilities. `.form-grid` is one column on mobile and two on desktop; `.full` spans both columns. Labels connect to inputs through matching `for` and `id` attributes.

## What to explain to the examiner

1. A page loads `bootstrap.php`, which starts the session and loads `koneksi.php`.
2. `mysql_connect` opens the connection, `mysql_select_db` selects the database, `mysql_query` runs SQL, and `mysql_fetch_array` reads rows.
3. Form processing runs before HTML. On success, a redirect avoids resubmitting the form on refresh.
4. The category dropdown comes from `categories`; JOINs display names instead of foreign-key IDs.
5. A transaction locks its item, calculates the new stock, rejects a negative balance, saves both records and commits. Any error rolls both changes back.
6. Unique SKU and foreign keys are enforced by the database as well as the relevant form checks.
