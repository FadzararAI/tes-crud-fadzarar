<?php

// Read the category list used by item forms and the inventory filter.
function item_categories()
{
    return rows('SELECT * FROM categories ORDER BY name');
}

function item_category_exists($id)
{
    return one('SELECT id FROM categories WHERE id=' . (int)$id) !== null;
}

// Find an item so edit.php can fill the form with its current values.
function item_find($id)
{
    return one('SELECT * FROM items WHERE id=' . (int)$id);
}

// Build the inventory list, including the category name shown on screen.
function item_list($search, $categoryId)
{
    $conditions = array();

    if ($search !== '') {
        $searchTerm = sql_string('%' . $search . '%');
        $conditions[] = '(i.sku LIKE ' . $searchTerm . ' OR i.name LIKE ' . $searchTerm . ')';
    }

    if ($categoryId) {
        $conditions[] = 'i.category_id=' . (int)$categoryId;
    }

    $sql = 'SELECT i.*, c.name AS category '
        . 'FROM items i '
        . 'JOIN categories c ON c.id=i.category_id';

    if ($conditions) {
        $sql .= ' WHERE ' . implode(' AND ', $conditions);
    }

    $sql .= ' ORDER BY i.name';

    return rows($sql);
}

// Check whether another item already uses this SKU.
function item_sku_exists($sku, $excludeId = 0)
{
    $sql = 'SELECT id FROM items WHERE sku=' . sql_string($sku);

    if ($excludeId) {
        $sql .= ' AND id<>' . (int)$excludeId;
    }

    return one($sql) !== null;
}

// Save item details. Stock is deliberately not included here.
function item_save($item, $categoryId, $id = 0)
{
    $values = 'sku=' . sql_string($item['sku'])
        . ',name=' . sql_string($item['name'])
        . ',category_id=' . (int)$categoryId
        . ',unit=' . sql_string($item['unit'])
        . ',unit_price=' . sql_string($item['unit_price']);

    if ($id) {
        return query('UPDATE items SET ' . $values . ' WHERE id=' . (int)$id);
    }

    return query('INSERT INTO items SET ' . $values);
}

function item_has_transactions($id)
{
    return one(
        'SELECT id FROM transactions WHERE item_id=' . (int)$id . ' LIMIT 1'
    ) !== null;
}

// Delete only items that have no transaction history.
function item_delete($id)
{
    return query('DELETE FROM items WHERE id=' . (int)$id);
}
