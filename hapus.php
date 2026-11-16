<?php
require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models/Item.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Use the Delete button in inventory.');
}

verify_csrf();

$id = positive_id(input($_POST, 'id'));
$item = $id ? item_find($id) : null;

if (!$item) {
    redirect('index.php', 'Item not found. It may already have been deleted.');
}

if (item_has_transactions($id)) {
    redirect(
        'index.php',
        'Cannot delete this item: its transaction history must be preserved.'
    );
}

try {
    item_delete($id);
    redirect('index.php', 'Item deleted.');
} catch (Exception $exception) {
    redirect(
        'index.php',
        'Cannot delete this item. It may now have transaction history.'
    );
}
