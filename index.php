<?php
require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models/Item.php';

$search = input($_GET, 'q');
$category = positive_id(input($_GET, 'category'));
$categories = item_categories();
$items = item_list($search, $category);

require __DIR__ . '/views/inventory/index.php';
