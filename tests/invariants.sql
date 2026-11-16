-- Run against the imported application database. Expected: zero rows.
SELECT i.id, i.sku, i.stock,
       COALESCE(SUM(CASE WHEN t.type='IN' THEN t.quantity
                        ELSE -CAST(t.quantity AS SIGNED) END), 0) AS ledger_stock
FROM items i
LEFT JOIN transactions t ON t.item_id=i.id
GROUP BY i.id, i.sku, i.stock
HAVING i.stock <> ledger_stock;

-- Expected: zero rows.
SELECT id FROM transactions WHERE quantity=0;
