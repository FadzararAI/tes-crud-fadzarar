<?php
require __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/models/Transaction.php';

// An empty date means that side of the date range is not limited.
$from = input($_GET, 'from');
$to = input($_GET, 'to');
$errors = array();

if ($from !== '' && !valid_date($from)) {
    $errors[] = 'Enter a valid start date.';
}

if ($to !== '' && !valid_date($to)) {
    $errors[] = 'Enter a valid end date.';
}

if (!$errors && $from !== '' && $to !== '' && $from > $to) {
    $errors[] = 'Start date must be on or before end date.';
}

$transactions = array();
$incoming = 0;
$outgoing = 0;

if (!$errors) {
    $transactions = transaction_list($from, $to);

    foreach ($transactions as $transaction) {
        if ($transaction['type'] === 'IN') {
            $incoming += $transaction['quantity'];
        } else {
            $outgoing += $transaction['quantity'];
        }
    }
}

require __DIR__ . '/views/transactions/index.php';
