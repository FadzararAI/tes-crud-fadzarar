<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title); ?> · Stockroom</title>
    <!-- Local Tailwind browser compiler: no Node or internet needed to edit forms. -->
    <script src="assets/vendor/tailwind-browser.js"></script>
    <style type="text/tailwindcss"><?php readfile(__DIR__ . '/../assets/style.css'); ?></style>
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-mark" aria-hidden="true">S</span>
        Stockroom
    </a>
    <nav aria-label="Main navigation">
        <a
            <?php if ($active === 'inventory') echo 'aria-current="page"'; ?>
            href="index.php"
        >Inventory</a>
        <a
            <?php if ($active === 'transactions') echo 'aria-current="page"'; ?>
            href="transaksi.php"
        >Transactions</a>
    </nav>
    <span class="workspace">Fadzarar / Warehouse</span>
</header>
<main>
<?php if ($flash !== ''): ?>
    <div class="notice" role="status"><?php echo e($flash); ?></div>
<?php endif; ?>
