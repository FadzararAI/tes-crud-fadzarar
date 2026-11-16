<?php

// Read movements for the selected inclusive date range.
function transaction_list($from = '', $to = '')
{
    $conditions = array();

    if ($from !== '') {
        $conditions[] = 't.transaction_date >= ' . sql_string($from);
    }

    if ($to !== '') {
        $conditions[] = 't.transaction_date <= ' . sql_string($to);
    }

    $sql = 'SELECT t.*, i.sku, i.name, i.unit '
        . 'FROM transactions t '
        . 'JOIN items i ON i.id=t.item_id';

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY t.transaction_date DESC, t.id DESC';

    return rows($sql);
}

function transaction_items()
{
    return rows('SELECT id, sku, name, stock, unit FROM items ORDER BY name');
}

// Update stock and add its ledger record as one database transaction.
function transaction_create($itemId, $type, $quantity, $date, $notes)
{
    query('START TRANSACTION');

    try {
        // Lock the item so two outgoing movements cannot oversell it.
        $item = one(
            'SELECT id, stock FROM items WHERE id=' . (int)$itemId . ' FOR UPDATE'
        );

        if (!$item) {
            throw new Exception('This item no longer exists. Select another item.');
        }

        $newStock = (int)$item['stock'];

        if ($type === 'IN') {
            $newStock += (int)$quantity;
        } else {
            $newStock -= (int)$quantity;
        }

        if ($newStock < 0) {
            throw new Exception(
                'Not enough stock. Available quantity: ' . $item['stock'] . '.'
            );
        }

        if ($newStock > 4294967295) {
            throw new Exception('This receipt exceeds the stock storage limit.');
        }

        query('UPDATE items SET stock=' . $newStock . ' WHERE id=' . (int)$itemId);
        query(
            'INSERT INTO transactions '
            . '(item_id, type, quantity, transaction_date, notes) VALUES ('
            . (int)$itemId . ', '
            . sql_string($type) . ', '
            . (int)$quantity . ', '
            . sql_string($date) . ', '
            . sql_string($notes)
            . ')'
        );

        query('COMMIT');
    } catch (Exception $exception) {
        query('ROLLBACK');
        throw $exception;
    }
}
