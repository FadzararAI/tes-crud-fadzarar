<?php
require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models/Transaction.php';

$errors = array();
$itemId = '';
$type = 'IN';
$quantity = '';
$date = date('Y-m-d');
$notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $itemId = positive_id(input($_POST, 'item_id'));
    $type = input($_POST, 'type');
    $quantity = input($_POST, 'quantity');
    $date = input($_POST, 'transaction_date');
    $notes = input($_POST, 'notes');

    if (!$itemId) {
        $errors[] = 'Select an item.';
    }

    if ($type !== 'IN' && $type !== 'OUT') {
        $errors[] = 'Select incoming or outgoing.';
    }

    if (!positive_id($quantity)) {
        $errors[] = 'Quantity must be a whole number between 1 and 999999999.';
    }

    if (!valid_date($date)) {
        $errors[] = 'Enter a valid transaction date.';
    }

    if (mb_strlen($notes, 'UTF-8') > 500) {
        $errors[] = 'Notes must be 500 characters or fewer.';
    }

    if (!$errors) {
        try {
            transaction_create($itemId, $type, $quantity, $date, $notes);
            redirect('transaksi.php', 'Movement recorded. Stock has been updated.');
        } catch (Exception $exception) {
            $errors[] = $exception->getMessage();
        }
    }
}

$items = transaction_items();
require __DIR__ . '/views/transactions/form.php';
