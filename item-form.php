<?php
require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models/Item.php';

$id = positive_id(input($_GET, 'id'));
$errors = array();
$item = array(
    'sku' => '',
    'name' => '',
    'category_id' => '',
    'unit' => '',
    'unit_price' => ''
);

if ($editing) {
    $item = $id ? item_find($id) : null;

    if (!$item) {
        http_response_code(404);
        page_start('Item not found');
        require __DIR__ . '/views/shared/not-found.php';
        page_end();
        exit;
    }
}

$categories = item_categories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // Keep one explicit assignment for each form field to make changes clear.
    $item['sku'] = input($_POST, 'sku');
    $item['name'] = input($_POST, 'name');
    $item['category_id'] = input($_POST, 'category_id');
    $item['unit'] = input($_POST, 'unit');
    $item['unit_price'] = input($_POST, 'unit_price');

    if (!preg_match('/^[A-Za-z0-9][A-Za-z0-9_-]{0,39}$/D', $item['sku'])) {
        $errors[] = 'SKU must be 1–40 letters, numbers, hyphens or underscores, starting with a letter or number.';
    }

    if ($item['name'] === '' || mb_strlen($item['name'], 'UTF-8') > 120) {
        $errors[] = 'Enter an item name of 1–120 characters.';
    }

    if ($item['unit'] === '' || mb_strlen($item['unit'], 'UTF-8') > 24) {
        $errors[] = 'Enter a unit of 1–24 characters.';
    }

    $categoryId = positive_id($item['category_id']);
    if (!$categoryId || !item_category_exists($categoryId)) {
        $errors[] = 'Select an existing category.';
    }

    if (!preg_match('/^(0|[1-9][0-9]{0,11})(\.[0-9]{1,2})?$/D', $item['unit_price'])) {
        $errors[] = 'Enter a nonnegative price with up to 12 whole digits and 2 decimal places.';
    }

    $excludeId = $editing ? $id : 0;
    if (item_sku_exists($item['sku'], $excludeId)) {
        $errors[] = 'That SKU is already in use. Choose a unique SKU.';
    }

    if (!$errors) {
        try {
            item_save($item, $categoryId, $editing ? $id : 0);

            if ($editing) {
                redirect('index.php', 'Item updated. Stock is unchanged.');
            }

            redirect(
                'index.php',
                'Item added with zero stock. Record an incoming transaction to receive stock.'
            );
        } catch (Exception $exception) {
            $errors[] = 'Unable to save. Check the SKU is still unique and try again.';
        }
    }
}

require __DIR__ . '/views/inventory/form.php';
